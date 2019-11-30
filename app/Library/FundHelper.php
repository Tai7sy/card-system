<?php


namespace App\Library;

use App\Order;
use App\User;
use App\FundRecord;
use Illuminate\Support\Facades\DB;

class FundHelper
{
    const ACTION_CONTINUE = 1001;

    /**
     * @param integer $order_id
     * @param $processOrder
     * @return bool
     */
    public static function orderSuccess($order_id, callable $processOrder)
    {
        /** @var \App\Order $order */
        $order = null;
        try {
            return DB::transaction(function () use ($order_id, &$order, $processOrder) {
                /** @var \App\Order $order */
                $order = \App\Order::where('id', $order_id)->lockForUpdate()->firstOrFail();

                $ret = $processOrder($order);
                if ($ret !== self::ACTION_CONTINUE) {
                    return $ret;
                }

                /* @var User $user */
                $user = User::where('id', $order->user_id)->lockForUpdate()->firstOrFail();
                $user->m_all += $order->income;
                $user->saveOrFail();

                $fund_record = new FundRecord;
                $fund_record->user_id = $order->user_id;
                $fund_record->type = FundRecord::TYPE_IN;
                $fund_record->amount = $order->income;
                $fund_record->all = $user->m_all;
                $fund_record->frozen = $user->m_frozen;
                $fund_record->paid = $user->m_paid;
                $fund_record->balance = $user->m_balance;
                $fund_record->remark = '订单#' . $order->order_no;
                $fund_record->order_id = $order->id;
                $fund_record->saveOrFail();

                return true;
            });
        } catch (\Throwable $e) {
            $msg = 'FundHelper.orderSuccess error, order_id:' . $order_id;
            if ($order)
                $msg .= ', user_id:' . $order->user_id . ',income:' . $order->income . ',order_no:' . $order->order_no;
            Log::error($msg . ' with exception:', ['Exception' => $e]);
            return false;
        }
    }

    /**
     * @param integer $order_id 这里的订单是用户侧(retail_end)
     * @param string $frozen_reason
     * @return bool
     */
    public static function orderFreeze($order_id, $frozen_reason)
    {
        /** @var \App\Order|null $order */
        $order = null;
        try {
            return DB::transaction(function () use ($order_id, &$order, $frozen_reason) {
                /** @var \App\Order $order */
                $order = \App\Order::where('id', $order_id)->lockForUpdate()->firstOrFail();

                // 如果订单已经退款, 直接返回
                if ($order->status === Order::STATUS_REFUND) {
                    return false;
                }

                // 如果订单已经冻结过, 那么就不操作了
                if ($order->status === Order::STATUS_FROZEN) {
                    return true;
                }

                $origin_status = $order->status;
                if ($origin_status === \App\Order::STATUS_SUCCESS) {
                    $statusTxt = '已发货';
                } elseif ($origin_status === \App\Order::STATUS_UNPAY) {
                    $statusTxt = '未付款';
                } elseif ($origin_status === \App\Order::STATUS_PAID) {
                    $statusTxt = '未发货';
                } else {
                    throw new \Exception('unknown');
                }
                
                /* @var User $user */
                $user = User::where('id', $order->user_id)->lockForUpdate()->firstOrFail();

                $fund_record = new FundRecord;
                $fund_record->user_id = $order->user_id;
                $fund_record->type = FundRecord::TYPE_OUT;
                $fund_record->order_id = $order->id;
                $fund_record->remark = $order === $order ? '' : '关联订单#' . $order->order_no . ': ';

                // 只有成功的订单才会扣款 (未支付, 已支付未发货的不退款)
                if ($origin_status === \App\Order::STATUS_SUCCESS) {
                    $user->m_frozen += $order->income;
                    $user->saveOrFail();

                    $fund_record->amount = -$order->income;
                    $fund_record->remark .= $frozen_reason . ', 冻结订单#' . $order->order_no;

                } else {
                    $fund_record->amount = 0;
                    $fund_record->remark .= $frozen_reason . ', 冻结订单(' . $statusTxt . ')#' . $order->order_no;
                }
                $fund_record->all = $user->m_all;
                $fund_record->frozen = $user->m_frozen;
                $fund_record->paid = $user->m_paid;
                $fund_record->balance = $user->m_balance;
                $fund_record->saveOrFail();

                $order->status = \App\Order::STATUS_FROZEN;
                $order->frozen_reason = ($order === $order ? '' : '关联订单#' . $order->order_no . ': ') . $frozen_reason;
                $order->saveOrFail();

                return true;
            });
        } catch (\Throwable $e) {
            $msg = 'FundHelper.orderFreeze error';
            if ($order) $msg .= ', order_no:' . $order->order_no . ', user_id:' . $order->user_id . ', amount:' . $order->income;
            else $msg .= ', order_no: null';
            Log::error($msg . ' with exception:', ['Exception' => $e]);
            return false;
        }
    }


    /**
     * 用户取消投诉 or 用户败诉 都是这个
     * @param integer $order_id 这里的订单是用户侧(retail_end)
     * @param string $reason 解冻说明
     * @param callable $doInTransaction
     * @param int $outerStatus 获取订单现在的状态
     * @return bool
     */
    public static function orderUnfreeze($order_id, $reason, callable $doInTransaction = null, &$outerStatus = null)
    {
        /** @var \App\Order|null $order */
        $order = null;
        try {
            return DB::transaction(function () use ($order_id, &$order, $reason, $doInTransaction, &$outerStatus) {
                /** @var \App\Order $order */
                $order = \App\Order::where('id', $order_id)->lockForUpdate()->firstOrFail();

                // 有可能订单是投诉被冻结, 然后手动补单(状态就变成未发货或已成功),
                // 拒绝投诉的时候这里要设置 complaint 的状态
                if ($doInTransaction !== null) {
                    $ret = $doInTransaction();
                    if ($ret !== self::ACTION_CONTINUE) {
                        return $ret;
                    }
                }

                // 如果不是 冻结状态，已退款, 则返回失败
                if ($order->status === Order::STATUS_REFUND) {
                    $outerStatus = $order->status;
                    return false;
                }

                // 如果不是 冻结状态，说明已经解冻（已经撤销/拒绝投诉 或 手动解冻过订单）
                if ($order->status !== Order::STATUS_FROZEN) {
                    $outerStatus = $order->status;
                    return true;
                }

                $is_already_income = $order->card_orders()->exists(); // 这里不能用 status, 因为 status 已经是 FROZEN 状态了
                if ($is_already_income) {
                    $outerStatus = \App\Order::STATUS_SUCCESS; // 订单投诉前的状态是 成功状态
                    $statusTxt = '已发货';
                } else {
                    if ($order->paid_at === NULL) {
                        $outerStatus = \App\Order::STATUS_UNPAY;  // 订单投诉前的状态是 未付款 状态
                        $statusTxt = '未付款';
                    } else {
                        $outerStatus = \App\Order::STATUS_PAID; // 订单投诉前的状态是 已付款状态(未发货)
                        $statusTxt = '未发货';
                    }
                }

                /* @var User $user */
                $user = User::where('id', $order->user_id)->lockForUpdate()->firstOrFail();

                $fund_record = new FundRecord;
                $fund_record->user_id = $order->user_id;
                $fund_record->type = FundRecord::TYPE_IN;
                $fund_record->remark = $order === $order ? '' : '关联订单#' . $order->order_no . ': ';
                $fund_record->order_id = $order->id;
                if ($is_already_income) {
                    // 这里状态上面已经判断过了, 一定是 STATUS_FROZEN 的
                    $user->m_frozen -= $order->income;
                    $user->saveOrFail();
                    $fund_record->amount = $order->income;
                    $fund_record->remark .= $reason . ', 解冻订单#' . $order->order_no;
                } else {
                    $fund_record->amount = 0;
                    $fund_record->remark .= $reason . ', 解冻订单(' . $statusTxt . ')#' . $order->order_no;
                }
                $fund_record->all = $user->m_all;
                $fund_record->frozen = $user->m_frozen;
                $fund_record->paid = $user->m_paid;
                $fund_record->balance = $user->m_balance;
                $fund_record->saveOrFail();

                $order->status = $outerStatus;
                $order->saveOrFail();

                return true;
            });
        } catch (\Throwable $e) {
            $msg = 'FundHelper.orderUnfreeze error';
            if ($order) $msg .= ', order_no:' . $order->order_no . ', user_id:' . $order->user_id . ',amount:' . $order->income;
            else $msg .= ', order_no: null';
            Log::error($msg . ' with exception:', ['Exception' => $e]);
            return false;
        }
    }
}