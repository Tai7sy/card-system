<?php


namespace App\Library;

use App\Order;
use App\User;
use App\FundRecord;
use Illuminate\Support\Facades\DB;

class FundHelper
{
    /**
     * @param Order $order
     * @param $processOrder
     * @return bool
     */
    public static function orderSuccess($order, callable $processOrder)
    {
        $user_id = $order->user_id;
        $add_money = $order->income;
        try {
            return DB::transaction(function () use ($order, $user_id, $add_money, $processOrder) {
                if ($processOrder() === Order::STATUS_SUCCESS) {
                    /* @var User $user */
                    $user = User::where('id', $user_id)->lockForUpdate()->firstOrFail();
                    $user->m_all += $add_money;
                    $user->saveOrFail();

                    $fund_record = new FundRecord;
                    $fund_record->user_id = $user_id;
                    $fund_record->type = FundRecord::TYPE_IN;
                    $fund_record->amount = $add_money;
                    $fund_record->balance = $user->m_balance;
                    $fund_record->remark = '订单#' . $order->order_no;
                    $fund_record->order_id = $order->id;
                    $fund_record->saveOrFail();
                }
                return true;
            });
        } catch (\Throwable $e) {
            $msg = 'FundHelper.orderSuccess error, user_id:' . $user_id . ',amount:' . $add_money . ',order_no:' . $order->order_no;
            \Illuminate\Support\Facades\Log::error($msg . ' with exception:', ['Exception' => $e]);
            return false;
        }
    }

    /**
     * @param Order $order
     * @param string $frozen_reason
     * @return bool
     */
    public static function orderFreeze($order, $frozen_reason)
    {
        if ($order->status === Order::STATUS_FROZEN || $order->status === Order::STATUS_REFUND) {
            return true;
        }
        $user_id = $order->user_id;
        try {
            return DB::transaction(function () use ($order, $user_id, $frozen_reason) {
                $order->status = \App\Order::STATUS_FROZEN;
                $order->frozen_reason = $frozen_reason;
                $order->saveOrFail();

                /* @var User $user */
                $user = User::where('id', $user_id)->lockForUpdate()->firstOrFail();
                // 只有成功的订单才会扣款 (未支付, 已支付未发货的不退款)
                // if ($order->status === \App\Order::STATUS_SUCCESS)
                if ($order->cards()->exists()) {
                    $user->m_frozen += $order->income;
                    $user->saveOrFail();

                    $fund_record = new FundRecord;
                    $fund_record->user_id = $user_id;
                    $fund_record->type = FundRecord::TYPE_OUT;
                    $fund_record->amount = -$order->income;
                    $fund_record->balance = $user->m_balance;
                    $fund_record->remark = $frozen_reason . ', 冻结订单#' . $order->order_no;
                    $fund_record->order_id = $order->id;
                    $fund_record->saveOrFail();
                } else {

                    if ($order->paid_at === NULL) {
                        $statusTxt = '未付款';
                    } else {
                        $statusTxt = '未发货';
                    }

                    $fund_record = new FundRecord;
                    $fund_record->user_id = $user_id;
                    $fund_record->type = FundRecord::TYPE_OUT;
                    $fund_record->amount = 0;
                    $fund_record->balance = $user->m_balance;
                    $fund_record->remark = $frozen_reason . ', 冻结订单(' . $statusTxt . ')#' . $order->order_no;
                    $fund_record->order_id = $order->id;
                    $fund_record->saveOrFail();
                }
                return true;
            });
        } catch (\Throwable $e) {
            $msg = 'FundHelper.orderFreeze error, user_id:' . $user_id . ',amount:' . $order->income;
            if ($order) $msg .= ',order_no:' . $order->order_no;
            else $msg .= ',order_no: null';
            \Illuminate\Support\Facades\Log::error($msg . ' with exception:', ['Exception' => $e]);
            return false;
        }
    }


    /**
     * 用户取消投诉 or 用户败诉 都是这个
     * @param Order $order
     * @param string $reason
     * @param callable $doInTransaction
     * @param int $outerStatus
     * @return bool
     */
    public static function orderUnfreeze($order, $reason, callable $doInTransaction = null, &$outerStatus = null)
    {

        $user_id = $order->user_id;
        try {
            return DB::transaction(function () use ($order, $user_id, $reason, $doInTransaction, &$outerStatus) {
                if ($doInTransaction !== null && $doInTransaction() === false) {
                    return false;
                }
                // 如果不是 冻结状态，说明已经解冻（已经撤销/拒绝投诉 或 手动解冻过订单）
                if ($order->status !== Order::STATUS_FROZEN) {
                    $outerStatus = $order->status;
                    return true;
                }
                /* @var User $user */
                $user = User::where('id', $user_id)->lockForUpdate()->firstOrFail();
                if ($order->cards()->exists()) {
                    $order->status = \App\Order::STATUS_SUCCESS; // 订单投诉前的状态是 成功状态
                    $user->m_frozen -= $order->income;
                    $user->saveOrFail();

                    $fund_record = new FundRecord;
                    $fund_record->user_id = $user_id;
                    $fund_record->type = FundRecord::TYPE_IN;
                    $fund_record->amount = $order->income;
                    $fund_record->balance = $user->m_balance;
                    $fund_record->remark = $reason . ', 解冻订单#' . $order->order_no;
                    $fund_record->order_id = $order->id;
                    $fund_record->saveOrFail();
                } else {

                    if ($order->paid_at === NULL) {
                        $order->status = \App\Order::STATUS_UNPAY;  // 订单投诉前的状态是 未付款 状态
                        $statusTxt = '未付款';
                    } else {
                        $order->status = \App\Order::STATUS_PAID;
                        $statusTxt = '未发货';
                    }

                    $fund_record = new FundRecord;
                    $fund_record->user_id = $user_id;
                    $fund_record->type = FundRecord::TYPE_IN;
                    $fund_record->amount = 0;
                    $fund_record->balance = $user->m_balance;
                    $fund_record->remark = $reason . ', 解冻订单(' . $statusTxt . ')#' . $order->order_no;
                    $fund_record->order_id = $order->id;
                    $fund_record->saveOrFail();
                }
                $outerStatus = $order->status;
                $order->saveOrFail();
                return true;
            });
        } catch (\Throwable $e) {
            $msg = 'FundHelper.orderUnfreeze error, user_id:' . $user_id . ',amount:' . $order->income;
            if ($order) $msg .= ',order_no:' . $order->order_no;
            else $msg .= ',order_no: null';
            \Illuminate\Support\Facades\Log::error($msg . ' with exception:', ['Exception' => $e]);
            return false;
        }
    }
}