<?php

namespace Tests;

use App\Services\PesapalService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(PesapalService::class, FakePesapalService::class);
    }
}
