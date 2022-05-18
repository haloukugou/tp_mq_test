<?php

namespace app\command;

use app\services\ReceiveService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Receive extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('receive_mq')->setDescription('mq消费者');
    }


    protected function execute(Input $input, Output $output)
    {
        (new ReceiveService())->receive();
    }
}