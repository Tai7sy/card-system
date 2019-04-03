<?php
 namespace Illuminate\Http\Resources; class MissingValue implements PotentiallyMissing { public function isMissing() { return true; } } 