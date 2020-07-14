<?php
namespace App\Http\Controllers\Admin; use App\Library\Helper; use Carbon\Carbon; use function foo\func; use Illuminate\Http\Request; use App\Http\Controllers\Controller; use App\Library\Response; class Pay extends Controller { function get(Request $sp147552) { $spd10097 = \App\Pay::query(); $sp23f506 = $sp147552->input('enabled'); if (strlen($sp23f506)) { $spd10097->whereIn('enabled', explode(',', $sp23f506)); } $spa1f2d3 = $sp147552->input('search', false); $spa55e11 = $sp147552->input('val', false); if ($spa1f2d3 && $spa55e11) { if ($spa1f2d3 == 'simple') { return Response::success($spd10097->get(array('id', 'name', 'enabled', 'comment'))); } elseif ($spa1f2d3 == 'id') { $spd10097->where('id', $spa55e11); } else { $spd10097->where($spa1f2d3, 'like', '%' . $spa55e11 . '%'); } } $sp8b8475 = $spd10097->get(); return Response::success(array('list' => $sp8b8475, 'urls' => array('url' => config('app.url'), 'url_api' => config('app.url_api')))); } function stat(Request $sp147552) { $this->validate($sp147552, array('day' => 'required|integer|between:1,30')); $sp66f4c4 = (int) $sp147552->input('day'); if ($sp66f4c4 === 30) { $sp7b3d9e = Carbon::now()->addMonths(-1)->toDateString() . ' 00:00:00'; } else { $sp7b3d9e = Carbon::now()->addDays(-$sp66f4c4)->toDateString() . ' 00:00:00'; } $sp8b8475 = $this->authQuery($sp147552, \App\Order::class)->where(function ($spd10097) { $spd10097->where('status', \App\Order::STATUS_PAID)->orWhere('status', \App\Order::STATUS_SUCCESS); })->where('paid_at', '>=', $sp7b3d9e)->with(array('pay' => function ($spd10097) { $spd10097->select(array('id', 'name')); }))->groupBy('pay_id')->selectRaw('`pay_id`,COUNT(*) as "count",SUM(`paid`) as "sum"')->get()->toArray(); $sp296935 = array(); foreach ($sp8b8475 as $sp458343) { if (isset($sp458343['pay']) && isset($sp458343['pay']['name'])) { $sp2c79c2 = $sp458343['pay']['name']; } else { $sp2c79c2 = '未知方式#' . $sp458343['pay_id']; } $sp296935[$sp2c79c2] = array((int) $sp458343['count'], (int) $sp458343['sum']); } return Response::success($sp296935); } function edit(Request $sp147552) { $this->validate($sp147552, array('id' => 'sometimes|integer', 'name' => 'required|string', 'driver' => 'required|string', 'way' => 'required|string', 'config' => 'required|string', 'fee_system' => 'required|numeric')); $spf93c32 = \App\Pay::find((int) $sp147552->post('id')); if (!$spf93c32) { $spf93c32 = new \App\Pay(); } $spf93c32->name = $sp147552->post('name'); $spf93c32->comment = $sp147552->post('comment'); $spf93c32->driver = $sp147552->post('driver'); $spf93c32->way = $sp147552->post('way'); $spf93c32->config = $sp147552->post('config'); $spf93c32->enabled = (int) $sp147552->post('enabled'); $spf93c32->fee_system = $sp147552->post('fee_system'); $spf93c32->saveOrFail(); return Response::success(); } function comment(Request $sp147552) { $this->validate($sp147552, array('id' => 'required|integer')); $speb3ceb = (int) $sp147552->post('id'); $spf93c32 = \App\Pay::findOrFail($speb3ceb); $spf93c32->comment = $sp147552->post('comment'); $spf93c32->save(); return Response::success(); } function fee_system(Request $sp147552) { $this->validate($sp147552, array('id' => 'required|integer')); $speb3ceb = (int) $sp147552->post('id'); $spf93c32 = \App\Pay::findOrFail($speb3ceb); $spf93c32->fee_system = $sp147552->post('fee_system'); $spf93c32->saveOrFail(); return Response::success(); } function enable(Request $sp147552) { $this->validate($sp147552, array('ids' => 'required|string', 'enabled' => 'required|integer|between:0,3')); $sp548f2b = $sp147552->post('ids'); $sp23f506 = (int) $sp147552->post('enabled'); \App\Pay::whereIn('id', explode(',', $sp548f2b))->update(array('enabled' => $sp23f506)); \App\Pay::flushCache(); return Response::success(); } function delete(Request $sp147552) { $this->validate($sp147552, array('ids' => 'required|string')); $sp548f2b = $sp147552->post('ids'); \App\Pay::whereIn('id', explode(',', $sp548f2b))->delete(); \App\Pay::flushCache(); return Response::success(); } }