<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * These tests migrate the configured database from scratch, so they must never
     * be able to run against a real database. The check happens before the
     * application boots, because booting is what triggers the destructive
     * RefreshDatabase migration.
     */
    protected function setUp(): void
    {
        $this->guardAgainstDestructiveTestRun();

        parent::setUp();
    }

    private function guardAgainstDestructiveTestRun(): void
    {
        $cachedConfig = dirname(__DIR__).DIRECTORY_SEPARATOR.'bootstrap'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'config.php';

        if (is_file($cachedConfig)) {
            throw new RuntimeException(
                'Refusing to run the test suite: cached configuration exists at bootstrap/cache/config.php. '
                .'It overrides the environment defined in phpunit.xml, so the suite would run against the '
                .'development database and destroy its data. Run "php artisan config:clear" and try again.'
            );
        }

        $database = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? getenv('DB_DATABASE');

        if (is_string($database) && ! str_contains($database, 'test')) {
            throw new RuntimeException(
                "Refusing to run the test suite: DB_DATABASE is [{$database}]. "
                .'Tests migrate this database from scratch and would destroy real data. '
                .'Point DB_DATABASE in phpunit.xml at a dedicated test database.'
            );
        }
    }
}
