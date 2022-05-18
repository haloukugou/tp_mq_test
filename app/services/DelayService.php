<?php

namespace app\services;


class DelayService
{
    private $connection;

    public function __construct()
    {
        if (is_null($this->connection)) {
            $this->connection = DelayBaseService::getConnection();
        }
    }

    /**
     * 把队列信息扔到死信队列
     * @throws \ErrorException
     */
    public function todo()
    {
        $channel = $this->connection->channel();

        $callback = function ($msg) {
            echo '来了-' . date('Y-m-d H:i:s') . PHP_EOL;
            var_dump($msg->body);
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_consume(DelayBaseService::$normalQueue, 'DelayService', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }
}