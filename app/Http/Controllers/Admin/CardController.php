<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Card;
use App\Library\Response;

class CardController extends Controller
{
    public function get(Request $request)
    {
        $pageSize = $request->post('size', 20);
        $page = $request->post('page', 1);
        $good_id = $request->post('good_id', -1);
        if ($good_id == -1) {
            return Response::Forbidden('参数不完整');
        }

        $list = Card::where('good_id', $good_id)
            ->paginate($pageSize, ['*'], 'page', $page);
        return Response::Ret(0, 'success', $list);
    }

    public function edit(Request $request)
    {
        $id = (int)$request->post('id');
        $good_id = (int)$request->post('good_id');
        $cardNumber = $request->post('card');
        $status = (int)$request->post('status', Card::STATUS_NORMAL);
        $type = (int)$request->post('type', Card::TYPE_ONETIME);


        $card = Card::find($id);
        if (!$card) {
            $card = new Card;
        }
        $card->good_id = $good_id;
        $card->card = $cardNumber;
        $card->status = $status;
        $card->type = $type;

        $card->saveOrFail();
        return Response::Ret(0);
    }


    public function delete(Request $request)
    {
        $id = (int)$request->post('id', -1);
        if ($id === -1) {
            return Response::Forbidden();
        }
        Card::where('id', $id)->delete();
        return Response::Ret(0);
    }
}
