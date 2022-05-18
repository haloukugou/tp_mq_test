<?php

namespace app\services;

class DelayReceiveService
{
    private $connection;

    public function __construct()
    {
        if (is_null($this->connection)) {
            $this->connection = DelayBaseService::getConnection();
        }
    }

    public function todo()
    {
        $channel = $this->connection->channel();

        $callback = function ($msg) {
            // 处理逻辑
            echo '处理了-' . date('Y-m-d H:i:s') . PHP_EOL;
            var_dump($msg->body);
            // 手动ack
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        $channel->basic_consume(DelayBaseService::$delayQueue, 'DelayReceiveService', false, false, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }
}