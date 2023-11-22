<?php

namespace Tests\Feature\Admin\Constructions;

use App\Enums\RolesEnum;
use App\Models\Construction;
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

        $construction = Construction::factory()->create();

        $this->actingAs($user)->delete(route('admin.constructions.destroy', ['construction' => $construction->id]));

        if ($canDestroy) {
            $this->assertDatabaseMissing('constructions', $construction->toArray());
            return;
        }
        $this->assertDatabaseHas('constructions', $construction->toArray());
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
