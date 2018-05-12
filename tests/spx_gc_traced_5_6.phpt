--TEST--
GC is traced (PHP 7.0 - 7.1)
--SKIPIF--
<?php
if (
    version_compare(PHP_VERSION, '5.6.0') < 0
    || version_compare(PHP_VERSION, '7.0.0') >= 0
) {
    die('skip this test is for PHP 5.6 only');
}
?>
--ENV--
return <<<END
SPX_ENABLED=1
SPX_METRICS=zgr,zgb,zgc
SPX_FP_FOCUS=zgb
END;
--FILE--
<?php

function f() {
    $a = new stdClass;
    $b = new stdClass;

    $a->b = $b;
    $b->a = $a;
}

for ($i = 0; $i < 50 * 1000; $i++) {
    f();
}

?>
--EXPECTF--
*** SPX Report ***

Global stats:

  Called functions    :    50.0K
  Distinct functions  :        2

  ZE GC runs          :       10
  ZE GC root buffer   :    10.0K
  ZE GC collected     :   100.0K

Flat profile:

 ZE GC runs          | ZE GC root buffer   | ZE GC collected     |
 Inc.     | Exc.     | Inc.     | *Exc.    | Inc.     | Exc.     | Called   | Function
----------+----------+----------+----------+----------+----------+----------+----------
       10 |       10 |        1 |        1 |   100.0K |   100.0K |    50.0K | f
       10 |        0 |        1 |        0 |   100.0K |        0 |        1 | %s/spx_%s.php