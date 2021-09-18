<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected ?array $testData = [];

    protected function assertModelMatchesExpectedData(Model $model, int $expectedId = 1): void
    {
        $this->assertModelExists($model);
        $this->assertEquals($expectedId, $model->id);

        if ($this->testData !== null) {
            $modelArr = $model->toArray();
            unset($modelArr['id']);
            $this->assertSame($modelArr, $this->testData);
        }
    }

    abstract protected function loadTestData(?array $replacementVars = []): void;
}
