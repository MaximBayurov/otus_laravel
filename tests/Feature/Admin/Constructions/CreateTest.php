<?php

namespace Tests\Feature\Admin\Constructions;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @dataProvider providerTestCreate
     * @param \App\Enums\RolesEnum $role
     * @param bool $canCreate
     *
     * @return void
     */
    public function test_create(RolesEnum $role, bool $canCreate): void
    {
        $user = User::factory()->create()->assignRole($role);

        $response = $this->actingAs($user)->get(route('admin.constructions.create'));

        if ($canCreate) {
            $response->assertOk();
            return;
        }

        $response->assertRedirect(route('admin.home'));
    }

    /**
     * @return array
     */
    public static function providerTestCreate(): array
    {
        return [
            [
                RolesEnum::ADMIN,
                true,
            ],
            [
                RolesEnum::MODERATOR,
                false,
            ],
        ];
    }
}
