<?php

namespace app\command;

use app\services\DelayBaseService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class OrderDelay extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('order_delay')->setDescription('订单延迟消费');
    }


    protected function execute(Input $input, Output $output)
    {
        $connection = new AMQPStreamConnection('127.0.0.1', '5672', 'guest', 'guest', 'demo');

        $channel = $connection->channel();


        $excName = 'delay_exo_order';
        $key = 'delay_route_order';
        $queue = 'delay_queue_order';

        $channel->exchange_declare($excName, 'x-delayed-message', false, true, false);

        $channel->queue_bind($queue, $excName, $key);

        $callback = function ($msg) use ($output) {
            dump('正在消费|消费时间=' . date('Y-m-d H:i:s') . '|消费数据=' . $msg->body);
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);

        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while (1) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}