<?php
declare (strict_types=1);

namespace app\event;

use think\facade\Log;

class UserLogin
{
    public function __construct($msg)
    {
        $this->handle($msg);
    }

    public function handle($msg)
    {
        Log::info($msg . '=' . time());
    }
}
