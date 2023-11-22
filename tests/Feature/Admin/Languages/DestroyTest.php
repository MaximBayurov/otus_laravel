<?php

namespace Tests\Feature\Admin\Languages;

use App\Enums\RolesEnum;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @dataProvider providerTestDestroyAccess
     * @param \App\Enums\RolesEnum $rolesEnum
     * @param bool $canDestroy
     *
     * @return void
     */
    public function test_destroy(RolesEnum $rolesEnum, bool $canDestroy): void
    {
        $user = User::factory()->create()->assignRole($rolesEnum);

        $construction = Language::factory()->create();

        $this->actingAs($user)->delete(route('admin.languages.destroy', ['language' => $construction->id]));

        if ($canDestroy) {
            $this->assertDatabaseMissing('languages', $construction->toArray());
            return;
        }
        $this->assertDatabaseHas('languages', $construction->toArray());
    }

    public static function providerTestDestroyAccess(): array
    {
        return [
            [
                RolesEnum::ADMIN,
                true,
            ],
            [
                RolesEnum::MODERATOR,
                false,
            ]
        ];
    }
}
