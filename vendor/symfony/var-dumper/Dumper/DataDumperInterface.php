<?php
 namespace Symfony\Component\VarDumper\Dumper; use Symfony\Component\VarDumper\Cloner\Data; interface DataDumperInterface { public function dump(Data $data); } 