<?php
 namespace Illuminate\Database\Eloquent; interface Scope { public function apply(Builder $builder, Model $model); } 