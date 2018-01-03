<?php

namespace App\Http\Controllers\Shop;

use App\Library\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Group;

class GroupController extends Controller
{
    public function get()
    {
        $list = Group::where('enabled', 1)->get();
        return Response::Ret(0, 'success', $list);
    }
}
