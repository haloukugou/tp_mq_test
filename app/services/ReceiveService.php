<?php

namespace app\services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use think\facade\Db;

class ReceiveService
{
    private $connection;

    private $channel;

    private $config;

    private string $queue = 'dj_queue';

    private string $exchange = 'dj_exchange';

    private string $tag = 'dj_tag';

    public function __construct()
    {
        if (is_null($this->config)) {
            $this->config = config('rabbit_mq');
        }
        if (!$this->connection instanceof AMQPStreamConnection) {
            $this->connection = new AMQPStreamConnection(
                $this->config['host'],
                $this->config['port'],
                $this->config['user'],
                $this->config['password']
            );
        }
        if (is_null($this->channel)) {
            $this->channel = $this->connection->channel();
        }
    }

    public function receive()
    {
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->exchange_declare($this->exchange, 'direct', false, true, false);
        $this->channel->queue_bind($this->queue, $this->exchange);
        $this->channel->basic_consume($this->queue, $this->tag, false, false, false, false, array($this, 'start'));

        register_shutdown_function(array($this, 'shutDown'), $this->channel, $this->connection);
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    public function start($message)
    {
        $msg = $message->body;
//        dd($msg);
        if (!empty($msg)) {
            $data = json_decode($msg, true);
            dump($data);
            Db::table('user')->insert($data);
        }
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        // Send a message with the string "quit" to cancel the consumer.
        if ($message->body === 'quit') {
            $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
        }
    }

    public function shutdown($channel, $connection)
    {
        $channel->close();
        $connection->close();
    }
}