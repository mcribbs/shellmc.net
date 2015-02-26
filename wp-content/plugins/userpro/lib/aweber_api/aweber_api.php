<?php

if (class_exists('AWeberAPI')) {
error_log('hello');
    trigger_error("Duplicate: Another AWeberAPI client library is already in scope.", E_USER_WARNING);
}
else {
error_log('else');
    require_once('aweber.php');
}
