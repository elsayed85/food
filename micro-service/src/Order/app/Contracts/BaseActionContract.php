<?php

namespace App\Contracts;

interface BaseActionContract
{
    public function __construct(BaseOrderServiceContract $service);
    public function execute();
}
