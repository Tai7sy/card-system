<?php
 namespace App\Concerns; trait SerializesLegacyDates { protected function serializeDate(\DateTimeInterface $date) { return $date->format($this->getDateFormat()); } } 