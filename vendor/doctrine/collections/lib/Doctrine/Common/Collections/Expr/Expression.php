<?php
 namespace Doctrine\Common\Collections\Expr; interface Expression { public function visit(ExpressionVisitor $visitor); } 