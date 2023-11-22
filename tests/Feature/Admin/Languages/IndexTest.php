<?php

namespace Tests\Feature\Admin\Languages;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @dataProvider providerTestIndexPositive
     * @param \App\Enums\RolesEnum $role
     *
     * @return void
     */
    public function test_index(RolesEnum $role): void
    {
        $user = User::factory()->create()->assignRole($role);

        $response = $this->actingAs($user)->get(route('admin.languages.index'));

        $response->assertOk();
    }

    public static function providerTestIndexPositive(): array
    {
        return [
            [
                RolesEnum::ADMIN,
            ],
            [
                RolesEnum::MODERATOR,
            ]
        ];
    }

    /**
     * @return void
     */
    public function test_index_not_auth(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.languages.index'));

        $response->assertRedirect(route('admin.home'));
    }
}
