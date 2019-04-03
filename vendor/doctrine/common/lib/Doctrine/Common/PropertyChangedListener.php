<?php
 namespace Doctrine\Common; interface PropertyChangedListener { function propertyChanged($sender, $propertyName, $oldValue, $newValue); } 