<?php

$OC_VERSION = '2.1';

if(defined('VERSION')) {
    if(VERSION >= 2.3) {
        $OC_VERSION = '2.3';
    } else if (VERSION < 2.3 && VERSION >= 2.2) {
        $OC_VERSION = '2.2';
    }
} else {
    $conf = require __DIR__ . '/config.php';
    if (file_exists($conf['PATH']['OC_DIR'] . '/system/library/cart/customer.php')) {
        $OC_VERSION = '2.3';
    }
}

return $OC_VERSION;
