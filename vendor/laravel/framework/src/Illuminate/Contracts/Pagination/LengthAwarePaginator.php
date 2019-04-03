<?php
 namespace Illuminate\Contracts\Pagination; interface LengthAwarePaginator extends Paginator { public function getUrlRange($start, $end); public function total(); public function lastPage(); } 