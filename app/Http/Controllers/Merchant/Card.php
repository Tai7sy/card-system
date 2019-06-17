<?php

namespace App\Http\Controllers\Merchant;

use App\Library\Response;
use App\System;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Card extends Controller
{
    /**
     * @param Request $request
     * @param bool $is_export 是否导出
     * @param bool $is_delete 是否删除
     * @param bool $is_restore 是否恢复
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    function get(Request $request, $is_export = false, $is_delete = false, $is_restore = false)
    {
        /* @var \App\Card $query */
        $query = $this->authQuery($request, \App\Card::class)
            ->with(['product' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->select(['id', 'name']);
            }]);

        $searchType = $request->input('search', false);
        $searchVal = $request->input('val', false);
        if ($searchType && $searchVal) {
            if ($searchType == 'id') {
                $query->where('id', $searchVal);
            } else {
                $query->where($searchType, 'like', '%' . $searchVal . '%');
            }
        }
        $category_id = (int)$request->input('category_id');
        $product_id = $request->input('product_id', -1);

        if ($category_id > 0) {
            if ($product_id > 0) $query->where('product_id', $product_id);
            else {
                $query->whereHas('product', function ($query) use ($category_id) {
                    /* @var \Illuminate\Database\Eloquent\Builder $query */
                    $query->where('category_id', $category_id);
                });
            }
        }

        $status = $request->input('status');
        if (strlen($status))
            $query->whereIn('status', explode(',', $status));

        $onlyCanSell = (int)$request->input('onlyCanSell');
        if ($onlyCanSell)
            $query->whereRaw('`count_all`>`count_sold`');


        $type = $request->input('type');
        if (strlen($type))
            $query->whereIn('type', explode(',', $type));

        $isTrashed = $request->input('trashed') === 'true';
        if ($isTrashed)
            $query->onlyTrashed();

        if ($is_delete === true) {
            if ($isTrashed) {
                $query->forceDelete();
            } else {
                \App\Card::_trash($query);
            }
            return Response::success();
        } else if ($isTrashed && $is_restore === true) {
            \App\Card::_restore($query);
            return Response::success();
        } else {
            $query->orderByRaw('`product_id`,`type`,`status`,`id`');

            if ($is_export === true) {
                $card_txt = '';
                $query->chunk(100, function ($cards) use (&$card_txt) {
                    /* @var \App\Card[] $cards */
                    foreach ($cards as $card) {
                        $card_txt .= $card->card . "\r\n";
                    }
                });

                $file_name = 'export_cards_' . $this->getUserIdOrFail($request) . '_' . date('YmdHis') . '.txt';
                $headers = ['Content-type' => 'text/plain',
                    'Content-Disposition' => sprintf('attachment; filename="%s"', $file_name),
                    'Content-Length' => strlen($card_txt)];
                return response()->make($card_txt, 200, $headers);
            }
            $page = $request->input('current_page', 1);
            $pageSize = $request->input('per_page', 20);
            $list = $query->paginate($pageSize, ['*'], 'page', $page);
            return Response::success($list);
        }
    }

    function export(Request $request)
    {
        return self::get($request, true);
    }

    /**
     * 卡密 - 删除到回收站
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    function trash(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|string'
        ]);
        $ids = $request->post('ids');

        $query = $this->authQuery($request, \App\Card::class)
            ->whereIn('id', explode(',', $ids));

        \App\Card::_trash($query);
        return Response::success();
    }

    /**
     * 回收站 - 选中恢复
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    function restoreTrashed(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|string'
        ]);
        $ids = $request->post('ids');

        $query = $this->authQuery($request, \App\Card::class)
            ->whereIn('id', explode(',', $ids));

        \App\Card::_restore($query);
        return Response::success();
    }

    /**
     * 回收站 - 选中彻底删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function deleteTrashed(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|string'
        ]);
        $ids = $request->post('ids');

        $this->authQuery($request, \App\Card::class)
            ->whereIn('id', explode(',', $ids))
            ->forceDelete();
        return Response::success();
    }

    /**
     * 删除全部 - 自动判断是否回收站
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    function deleteAll(Request $request)
    {
        return $this->get($request, false, true);
    }


    /**
     * 回收站 - 当前筛选条件全部恢复
     * 借用 get 函数筛选, 进行回收站恢复操作
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    function restoreAll(Request $request)
    {
        return $this->get($request, false, false, true);
    }


    /**
     * 添加卡密
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \Throwable
     */
    function add(Request $request)
    {
        $product_id = (int)$request->post('product_id');
        $cards = $request->post('card');
        $type = (int)$request->post('type', \App\Card::TYPE_ONETIME);
        $is_check = $request->post('is_check') === 'true';
        // $delimiter = $request->post('delimiter', " ");

        if (str_contains($cards, '<') || str_contains($cards, '>'))
            return Response::fail('卡密不能包含 < 或 > 符号');

        $user_id = $this->getUserIdOrFail($request);
        /* @var \App\Product $product */
        $product_sql = $this->authQuery($request, \App\Product::class)->where('id', $product_id);
        $product_sql->firstOrFail(['id']);

        if ($type === \App\Card::TYPE_REPEAT) {
            if ($is_check) {
                if (\App\Card::where('product_id', $product_id)->where('card', $cards)->exists()) {
                    return Response::fail('该卡密已经存在，添加失败');
                }
            }
            $card = new \App\Card([
                'user_id' => $user_id,
                'product_id' => $product_id,
                'card' => $cards,
                'type' => \App\Card::TYPE_REPEAT,
                'count_sold' => 0,
                'count_all' => (int)$request->post('count_all', 1)
            ]);
            if ($card->count_all < 1 || $card->count_all > 10000000)
                return Response::forbidden('可售总次数不能超过10000000');

            return DB::transaction(function () use ($product_sql, $card) {
                $card->saveOrFail();
                /* @var \App\Product $product */
                $product = $product_sql->lockForUpdate()->firstOrFail();
                $product->buy_max = 1;
                $product->count_all += $card->count_all;
                $product->saveOrFail();
                return Response::success();
            });
        } else {
            $card_arr = explode("\n", $cards);
            $card_count = count($card_arr);
            $per_round = 500;
            if ($card_count > $per_round) {
                return Response::fail('每次添加不能超过 ' . $per_round . ' 张');
            }

            $cards_all = [];
            if ($is_check) {
                $cards_tmp = \App\Card::where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->get(['card'])->all();
                foreach ($cards_tmp as $cards_i) {
                    $cards_all[] = $cards_i['card'];
                }
            }

            $cards_to_insert = [];
            $card_count_real = 0;
            for ($i = 0; $i < $card_count; $i++) {
                $card = trim($card_arr[$i]);
                if (strlen($card) < 1)
                    continue;

                if (strlen($card) > 255)
                    return Response::fail('第 ' . $i . ' 张卡密 ' . $card . ' 长度错误<br>卡密最大长度为255');

                if ($is_check) {
                    if (in_array($card, $cards_all)) {
                        continue;
                    }
                    $cards_all[] = $card;
                }

                $cards_to_insert[] = [
                    'user_id' => $user_id,
                    'product_id' => $product_id,
                    'card' => $card,
                    'type' => \App\Card::TYPE_ONETIME
                ];
                $card_count_real++;
            }

            if ($card_count_real === 0)
                return Response::success();

            return DB::transaction(function () use ($product_sql, $cards_to_insert, $card_count_real) {
                \App\Card::insert($cards_to_insert);

                /* @var \App\Product $product */
                $product = $product_sql->lockForUpdate()->firstOrFail();
                $product->count_all += $card_count_real;
                $product->saveOrFail();
                return Response::success();
            });
        }
    }

    /**
     * 编辑卡密
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \Throwable
     */
    function edit(Request $request)
    {
        $id = (int)$request->post('id');
        $cardNumber = $request->post('card'); // 卡号
        $type = (int)$request->post('type', \App\Card::TYPE_ONETIME);

        /* @var \App\Card $card */
        $card = $this->authQuery($request, \App\Card::class)->findOrFail($id);
        if ($card) {
            $card->card = $cardNumber;
            $card->type = $type;
            $count_all = (int)$request->post('count_all', 1);
            $product_sql = $card->product();

            if ($type === \App\Card::TYPE_REPEAT) {
                return DB::transaction(function () use ($product_sql, $card, $count_all) {
                    /* @var \App\Card $card */
                    $card = \App\Card::where('id', $card->id)->lockForUpdate()->firstOrFail();

                    if ($count_all < $card->count_sold)
                        return Response::forbidden('可售总次数不能低于当前已售次数');

                    if ($count_all < 1 || $count_all > 10000000)
                        return Response::forbidden('可售总次数不能超过10000000');

                    /* @var \App\Product $product */
                    $product = $product_sql->lockForUpdate()->firstOrFail();
                    $product->buy_max = 1;
                    $product->count_all -= $card->count_all; // 删去原有的
                    $product->count_all += $count_all; // 加上现在的
                    $product->saveOrFail();

                    $card->count_all = $count_all;
                    $card->saveOrFail();
                    return Response::success();
                });
            } else {
                return DB::transaction(function () use ($product_sql, $card, $count_all) {
                    /* @var \App\Card $card */
                    $card = \App\Card::where('id', $card->id)->lockForUpdate()->firstOrFail();
                    /* @var \App\Product $product */
                    $product = $product_sql->lockForUpdate()->firstOrFail();
                    $product->count_all -= $card->count_all; // 删去原有的
                    $product->count_all += 1; // 加上现在的
                    $product->saveOrFail();

                    $card->count_all = 1;
                    $card->saveOrFail();
                    return Response::success();
                });
            }
        }
        return Response::success();
    }

}
