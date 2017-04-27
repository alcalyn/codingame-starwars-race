<?php

define('TO_DEGREES', 180 / M_PI);

function d() {
    foreach (func_get_args() as $o) {
        if (is_array($o)) {
            error_log(print_r($o, true));
        } elseif (null === $o) {
            error_log('NULL');
        } else {
            error_log($o);
        }
    }
}
