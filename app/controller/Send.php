<?php

namespace app\controller;

use app\BaseController;
use app\services\DelayBaseService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Send extends BaseController
{
    public function send()
    {
        $params = $this->request->all();
        $params['create_time'] = date('Y-m-d H:i:s');
        self::pushMessage(json_encode($params));
        return json(['status' => 1, 'msg' => 'success']);
    }

    const exchange = 'dj_exchange';
    const queue = 'dj_queue';

    public static function pushMessage($data)
    {
        $config = config('rabbit_mq');
        $connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
        $channel = $connection->channel();

        $channel->queue_declare(self::queue, false, true, false, false);
        $channel->exchange_declare(self::exchange, 'direct', false, true, false);
        $channel->queue_bind(self::queue, self::exchange);
        $messageBody = $data;
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, self::exchange);
        $channel->close();
        $connection->close();
        return "ok";
    }


    public function delaySend()
    {
        $params = $this->request->all();
        $params['time'] = date('Y-m-d H:i:s');
//        self::pushDelayMessage(json_encode($params));
        self::sendMsg(json_encode($params));
        return json(['status' => 1, 'msg' => 'success']);
    }

    private function pushDelayMessage($data)
    {
        $connection = DelayBaseService::getConnection();
        $channel = $connection->channel();
        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg, DelayBaseService::$normalExchange);
        $channel->close();
        $connection->close();
    }


    public function sendMsg($data)
    {
        $connection = new AMQPStreamConnection('127.0.0.1','5672','guest','guest','demo');
        $channel = $connection->channel();

        $excName = 'delay_exo_order';
        $key = 'delay_route_order';
        $queue = 'delay_queue_order';
        $ttl = 3000;

        $channel->exchange_declare($excName, 'x-delayed-message', false, true, false);
        $args = new AMQPTable(['x-delayed-type' => 'direct']);
        $channel->queue_declare($queue, false, true, false, false, false, $args);

        $channel->queue_bind($queue, $excName, $key);
        $arr = ['delivery_mode'=>AMQPMessage::DELIVERY_MODE_NON_PERSISTENT,'application_headers'=>new AMQPTable(['x-delay'=>$ttl])];

        $msg = new AMQPMessage($data,$arr);

        $channel->basic_publish($msg,$excName,$key);
        $channel->close();
        $connection->close();
    }
}