<?php

namespace app\services;

class TestService
{
    public function __construct()
    {
    }

    private function __clone()
    {
    }

    public function query(): static
    {
        return $this;
    }


}