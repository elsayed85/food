<?php

namespace App\Console\Commands;

use Elsayed85\LmsRedis\LmsRedis;
use Illuminate\Console\Command;

class LmsRedisConsumeCommand extends Command
{
    public $signature = 'lms:consume';

    public $description = 'Consume events from Redis stream';

    protected LmsRedis $redisService;

    public function __construct()
    {
        parent::__construct();
        $this->redisService = $this->getLmsServiceClass();
    }

    private function getLmsServiceClass()
    {
        $service = config('lms-redis.service');
        return new $service;
    }

    public function handle(): void
    {
        foreach ($this->redisService->getUnprocessedEvents() as $event) {
            $this->redisService->processEvent($event);
            $this->redisService->addProcessedEvent($event);
        }
    }
}
