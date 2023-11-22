<?php

namespace Tests\Unit\Enums\Permissions;

use App\Enums\Permissions\Constructions;
use PHPUnit\Framework\TestCase;

class ConstructionsTest extends TestCase
{
    /**
     * @dataProvider codesProvider
     *
     * @param \App\Enums\Permissions\Constructions $case
     * @param string $code
     *
     * @return void
     */
    public function testCode(Constructions $case, string $code): void
    {
        $this->assertEquals($case->code(), $code);
    }

    public static function codesProvider(): array
    {
        return [
            [
                Constructions::VIEW,
                'view constructions',
            ],
            [
                Constructions::CREATE,
                'create constructions',
            ],
            [
                Constructions::UPDATE,
                'update constructions',
            ],
            [
                Constructions::DELETE,
                'delete constructions',
            ],
        ];
    }
}
