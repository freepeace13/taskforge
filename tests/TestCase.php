<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\InteractsWithTenant;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use InteractsWithTenant;
}
