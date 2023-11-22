<?php

namespace Tests\Unit\Enums\Permissions;

use App\Enums\Permissions\Languages;
use PHPUnit\Framework\TestCase;

class LanguagesTest extends TestCase
{
    /**
     * @dataProvider codesProvider
     *
     * @param \App\Enums\Permissions\Languages $case
     * @param string $code
     *
     * @return void
     */
    public function testCode(Languages $case, string $code): void
    {
        $this->assertEquals($case->code(), $code);
    }

    public static function codesProvider(): array
    {
        return [
            [
                Languages::VIEW,
                'view languages',
            ],
            [
                Languages::CREATE,
                'create languages',
            ],
            [
                Languages::UPDATE,
                'update languages',
            ],
            [
                Languages::DELETE,
                'delete languages',
            ],
        ];
    }
}
