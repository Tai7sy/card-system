<?php

namespace App\Http\Controllers\Merchant;

use App\Library\Response;
use App\System;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Card extends Controller
{
    function get(Request $request, $is_export = false, $is_delete = false, $is_restore = false)
    {
        /* @var \App\Card $query */
        $query = $this->authQuery($request, \App\Card::class)
            ->with(['product' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->select(['id', 'name']);
            }]);

        $searchType = $request->post('search', false);
        $searchVal = $request->post('val', false);
        if ($searchType && $searchVal) {
            if ($searchType == 'id') {
                $query->where('id', $searchVal);
            } else {
                $query->where($searchType, 'like', '%' . $searchVal . '%');
            }
        }
        $category_id = (int)$request->post('category_id');
        $product_id = $request->post('product_id', -1);

        if ($category_id > 0) {
            if ($product_id > 0) $query->where('product_id', $product_id);
            else {
                $query->whereHas('product', function ($query) use ($category_id) {
                    /* @var \Illuminate\Database\Eloquent\Builder $query */
                    $query->where('category_id', $category_id);
                });
            }
        }

        $status = $request->post('status');
        if (strlen($status))
            $query->whereIn('status', explode(',', $status));

        $onlyCanSell = (int)$request->post('onlyCanSell');
        if ($onlyCanSell)
            $query->whereRaw('`count_all`>`count_sold`');


        $type = $request->post('type');
        if (strlen($type))
            $query->whereIn('type', explode(',', $type));

        $isTrashed = $request->post('trashed') === 'true';
        if ($isTrashed)
            $query->onlyTrashed();

        $query->orderByRaw('product_id,type,status');

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
        } else if ($is_delete === true) {
            if ($isTrashed) {
                $query->forceDelete();
            } else {
                $query->delete();
            }
            return Response::success();
        } else if ($isTrashed && $is_restore === true) {
            $query->restore();
            return Response::success();
        } else {
            $page = $request->post('current_page', 1);
            $pageSize = $request->post('per_page', 20);
            $list = $query->paginate($pageSize, ['*'], 'page', $page);
            return Response::success($list);
        }
    }

    function export(Request $request)
    {
        return self::get($request, true);
    }

    /**
     * 卡密删除到回收站
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    function trash(Request $request)
    {
        $ids = $request->post('ids', '');
        if (strlen($ids) < 1) return Response::forbidden();

        $this->authQuery($request, \App\Card::class)
            ->whereIn('id', explode(',', $ids))->delete();
        return Response::success();
    }


    function deleteTrashed(Request $request)
    {
        $ids = $request->post('ids', '');
        if (strlen($ids) < 1) return Response::forbidden();

        $this->authQuery($request, \App\Card::class)
            ->whereIn('id', explode(',', $ids))
            ->forceDelete();
        return Response::success();
    }

    function deleteAll(Request $request)
    {
        return $this->get($request, false, true);
    }

    function restoreTrashed(Request $request)
    {
        $ids = $request->post('ids', '');
        if (strlen($ids) < 1) return Response::forbidden();

        /* @var \App\Card $query */
        $query = $this->authQuery($request, \App\Card::class);

        $query->whereIn('id', explode(',', $ids))
            ->restore();
        return Response::success();
    }

    function restoreAll(Request $request)
    {
        return $this->get($request, false, false, true);
    }


    function add(Request $request)
    {
        $product_id = (int)$request->post('product_id');
        $cards = $request->post('card');
        // $status = (int)$request->post('status', \App\Card::STATUS_NORMAL);
        $status = \App\Card::STATUS_NORMAL;
        $type = (int)$request->post('type', \App\Card::TYPE_ONETIME);
        $is_check = $request->post('is_check') === 'true';
        // $delimiter = $request->post('delimiter', " ");

        if (str_contains($cards, '<') || str_contains($cards, '>'))
            return Response::fail('卡密不能用 < 或 > 分割');


        if (\App\Card::where('user_id', \Auth::id())
                ->where('status', \App\Card::STATUS_NORMAL)
                ->count() > 10000) {
            return Response::fail('您的库存卡密已经超过10000张，本次添加失败');
        }

        /* @var \App\Product $product */
        $product = $this->authQuery($request, \App\Product::class)->findOrFail($product_id);


        if ($type === \App\Card::TYPE_REPEAT) {
            if ($is_check) {
                if (\App\Card::where('product_id', $product_id)->where('card', $cards)->exists()) {
                    return Response::fail('该卡密已经存在，添加失败');
                }
            }
            $product->buy_max = 1;
            $product->saveOrFail();

            $card = new \App\Card;
            $card->user_id = $this->getUserIdOrFail($request);
            $card->product_id = $product_id;
            $card->card = $cards;
            $card->type = $type;
            $card->status = $status;
            $card->count_sold = 0;
            $card->count_all = (int)$request->post('count_all', 1);
            if ($card->count_all < 1 || $card->count_all > 10000000)
                return Response::forbidden('可售总次数不能超过10000000');
            $card->saveOrFail();
        } else {
            $card_arr = explode("\n", $cards);
            $card_count = count($card_arr);
            $per_round = 50;
            if ($card_count > $per_round) {
                return Response::fail('每次添加不能超过 ' . $per_round . ' 张');
            }
            $card_added = [];
            $card_txt = '';
            for ($i = 0; $i < $card_count; $i++) {
                $card = trim($card_arr[$i]);
                if (strlen($card) < 1)
                    continue;

                if (strlen($card) > 100)
                    return Response::fail('第 ' . $i . ' 张卡密 ' . $card . ' 长度错误<br>卡密最大长度为100');

                if ($is_check) {
                    if (in_array($card, $card_added)) {
                        continue;
                    }
                    $card_added[] = $card;
                }

                if ($card_txt === '') {
                    $card_txt .= $card;
                } else {
                    $card_txt .= '<' . $card;
                }
            }

            if ($card_txt === '')
                return Response::fail('请输入合法的卡密');

            try {
                // \App\Card::add_cards($this->getUserIdOrFail($request), $product_id, $type, $status, $card_txt, $is_check);
                \App\Card::add_cards($this->getUserIdOrFail($request), $product_id, $type, $status, $card_txt, false); // 这里检查, 数据量大了会卡
            } catch (\Exception $e) {
                \Log::error('add_cards, function fatal error: ', ['exception' => $e]);
                return Response::fail($e->getMessage());
            }
        }


        return Response::success();
    }

    function edit(Request $request)
    {
        $id = (int)$request->post('id');
        $card = $request->post('card');
        // $status = (int)$request->post('status', \App\Card::STATUS_NORMAL);
        $type = (int)$request->post('type', \App\Card::TYPE_ONETIME);

        /* @var \App\Card $c */
        $c = $this->authQuery($request, \App\Card::class)->findOrFail($id);
        if ($c) {
            $c->card = $card;
            // $c->status = $status;
            $c->type = $type;
            if ($type === \App\Card::TYPE_REPEAT) {
                $product = $c->product;
                $product->buy_max = 1;
                $product->saveOrFail();
                $c->count_all = (int)$request->post('count_all', 1);
                if ($c->count_all < 1 || $c->count_all > 10000000)
                    return Response::forbidden('可售总次数不能超过10000000');
            } else {
                $c->count_all = 1;
            }

            $c->saveOrFail();
        }
        return Response::success();
    }


}
