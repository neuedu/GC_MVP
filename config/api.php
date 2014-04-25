<?php

return array(
    'tenant' => 'epals.com',
    'elasticsearch' => array(
        'host' => 'api.dev.epals.com',
        'port'  => '9200',
    ),
    'sis_apiserver' => array(
        'url' => 'http://dev01.neuedu.dev.ec2.epals.net:8080/sis/',
    ),
    'pm_apiserver' => array(
        'url' => 'http://dev02.neuedu.dev.ec2.epals.net:8080/BasicESB/',
    ),
    'lookup_apiserver' => array(
        'url' => 'http://apidev.dev.epals.com',
        'app_id'  => 'example',
        'app_key'  => '123',
    ),
);
