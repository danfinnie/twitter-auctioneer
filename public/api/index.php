<?php 
require_once __DIR__ . '/../../vendor/autoload.php';

//crazy simple router
with('/api', function () {
    respond('/login', function () {
        echo 'This is a login.';
    });
    
    respond('/payment', function () {
        echo 'This is a payment.';
    });
});

dispatch();