<?php


use PHPStan\Testing\LevelsTestCase;

final class ServiceManagerIntegrationTest extends LevelsTestCase
{

    public function dataTopics(): array
    {
        // TODO: Implement dataTopics() method.
    }

    public function getDataPath(): string
    {
        return __DIR__ . '/data';
    }

    public function getPhpStanExecutablePath(): string
    {
        return __DIR__ . '/../../../vendor/bin/phpstan';
    }

    public function getPhpStanConfigPath(): ?string
    {
        return __DIR__ . '/phpstan.neon';
    }
}