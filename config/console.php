<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'add_data' => \app\command\AddData::class,
        'receive_mq' => \app\command\Receive::class,
        'delay_mq' => \app\command\Delay::class,
        'normal_mq' => \app\command\Normal::class,
        'order_delay' => \app\command\OrderDelay::class,
    ],
];
