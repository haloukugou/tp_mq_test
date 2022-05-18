<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

class AddData extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('add_data')->setDescription('添加数据');
    }

    protected function execute(Input $input, Output $output)
    {
        for ($i = 0; $i < 10000; $i++) {
            $data = [];
            for ($a = 0; $a < 100; $a++) {
                $name = generate_name(rand(0, 1))['xm'];
                while (1) {
                    $phone = str_replace(' ', '', current(generate_mobile(1)));
                    $cond = [
                        ['mobile', '=', $phone]
                    ];
                    $exists = Db::table('user')->where($cond)->value('id');
                    if (is_null($exists)) {
                        break;
                    }
                }

                $data[] = [
                    'name' => $name,
                    'mobile' => $phone,
                    'age' => mt_rand(1, 100),
                    'create_time' => date('Y-m-d H:i:s')
                ];
            }

            Db::table('user')->insertAll($data);
            echo $i . PHP_EOL;
            usleep(2);
        }
        // 指令输出
        $output->writeln('结束');
        return;
    }
}
