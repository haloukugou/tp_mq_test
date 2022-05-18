<?php

namespace app\services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

class DelayBaseService
{
    public static $config;

    private static $connection;

    // 死信队列和交换机
    public static $delayQueue = 'dj_delay_queue';
    public static $delayExchange = 'dj_delay_exchange';
    public static $delayKey = 'dj_delay_key';

    // 死信之后的队列和交换机
    public static $normalQueue = 'dj_normal_queue';
    public static $normalExchange = 'dj_normal_exchange';
    public static $normalKey = 'dj_normal_key';

    // 信息发布者的key
    public static $msgKey = 'msg_key';


    public static function getConnection()
    {
        if (!is_null(self::$connection)) {
            return self::$connection;
        }
        if (is_null(self::$config)) {
            self::$config = config('rabbit_mq');
        }

        self::$connection = new AMQPStreamConnection(self::$config['host'], self::$config['port'], self::$config['user'], self::$config['password'], self::$config['vhost']);
        self::init();
        return self::$connection;
    }

    public static function init()
    {
        $channel = self::$connection->channel();
        self::$connection = new AMQPStreamConnection(self::$config['host'], self::$config['port'], self::$config['user'], self::$config['password'], self::$config['vhost']);
        $channel = self::$connection->channel();
        // 定义交换机
        $channel->exchange_declare(self::$delayExchange, AMQP_EX_TYPE_DIRECT, false, true);
        $channel->exchange_declare(self::$normalExchange, AMQP_EX_TYPE_FANOUT, false, true);

        // 定义队列
        $args = new AMQPTable();
        // 过期时间
        $args->set('x-message-ttl', 50000);
        // 设置队列最大长度方式
//        $args->set('x-max-length', 1);
        $args->set('x-dead-letter-exchange', 'dj_delay_exchange');
        $args->set('x-dead-letter-routing-key', 'dj_delay_key');

        // 队列
        $channel->queue_declare(self::$normalQueue, false, true, false, false, false, $args);
        $channel->queue_declare(self::$delayQueue, false, true, false, false);

        // 绑定
        $channel->queue_bind(self::$normalQueue, self::$normalExchange);
        $channel->queue_bind(self::$delayQueue, self::$delayExchange, self::$delayKey);
    }
}