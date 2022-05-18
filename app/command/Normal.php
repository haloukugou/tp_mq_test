<?php

namespace app\command;

use app\services\DelayService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Normal extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('normal_mq')->setDescription('mq延迟普通消费者');
    }


    protected function execute(Input $input, Output $output)
    {
        (new DelayService())->todo();
    }
}