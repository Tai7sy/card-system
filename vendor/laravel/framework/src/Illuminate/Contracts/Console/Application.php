<?php
 namespace Illuminate\Contracts\Console; interface Application { public function call($command, array $parameters = []); public function output(); } 