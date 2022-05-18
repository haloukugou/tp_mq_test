<?php

namespace app\command;

use app\services\DelayReceiveService;
use app\services\DelayService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Delay extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('delay_mq')->setDescription('mq延迟消费者');
    }


    protected function execute(Input $input, Output $output)
    {
        // 延迟队列消费
        (new DelayReceiveService())->todo();
    }
}