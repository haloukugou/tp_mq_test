<?php

namespace app\services;


class RedisService
{
    private static $redis;

    private $config;

    private $selectNumber;

    private function __construct($config, $selectNumber)
    {
        $this->config = $config;
        $this->selectNumber = $selectNumber;
    }

    private function __clone()
    {
    }

    /**
     * @param $config
     * @param $selectNumber
     * @return \Redis
     * @throws \Exception
     */
    public static function getRedis($config, $selectNumber)
    {
        if (!self::$redis instanceof \Redis) {
            self::$redis = self::connect($config, $selectNumber);
        }
        return self::$redis;
    }


    /**
     * @param $connection
     * @param $redisDb
     * @return \Redis
     * @throws \Exception
     */
    private static function connect($connection, $redisDb): \Redis
    {
        $redis = new \Redis();
        // 读取redis配置.
        $redisConfig = config('cache.stores.' . $connection);
        try {
            // 建立连接.
            $redis->connect($redisConfig['host'], $redisConfig['port']);
            // 身份认证.
            $redis->auth($redisConfig['password']);
            // 选择redis库.
            $redisDb = $redisDb == 20 ? $redisConfig['select'] : $redisDb;
            $redis->select($redisDb);
        } catch (\Exception $exception) {
            throw new \Exception('连接redis失败');
        }
        return $redis;
    }
}
