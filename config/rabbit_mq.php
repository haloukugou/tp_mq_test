<?php
return [
    'host'=>'127.0.0.1',
    'port'=>'5672',
    'user'=>'guest',
    'password'=>'guest',
    'vhost'=>'/',
    'exchange_name' => 'email_exchange',
    'queue_name' => 'email_queue',
    'route_key' => 'email_route',
    'consumer_tag' => 'consumer',
];