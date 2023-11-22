<?php

namespace Tests\Unit\Enums\Permissions;

use App\Enums\Permissions\Admin;
use PHPUnit\Framework\TestCase;

class AdminTest extends TestCase
{

    /**
     * @dataProvider codesProvider
     *
     * @param \App\Enums\Permissions\Admin $case
     * @param string $code
     *
     * @return void
     */
    public function testCode(Admin $case, string $code): void
    {
        $this->assertEquals($case->code(), $code);
    }

    public static function codesProvider(): array
    {
        return [
            [
                Admin::VIEW,
                'view admin',
            ]
        ];
    }
}
