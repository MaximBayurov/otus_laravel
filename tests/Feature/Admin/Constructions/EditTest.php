<?php

namespace Tests\Feature\Admin\Constructions;

use App\Enums\RolesEnum;
use App\Models\Construction;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EditTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @dataProvider editTestProvider
     *
     * @param \Closure $getConstructionId
     * @param bool $isExistingModel
     *
     * @return void
     */
    public function test_show(Closure $getConstructionId, bool $isExistingModel): void
    {
        $user = User::factory()->create()->assignRole(RolesEnum::ADMIN);
        $constructionId = $getConstructionId();

        $response = $this->actingAs($user)->get(route('admin.constructions.edit', ['construction' => $constructionId]));

        if ($isExistingModel) {
            $response->assertOk();
            return;
        }

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider editTestProvider
     * @param \Closure $getConstructionId
     * @param bool $isExistingModel
     *
     * @return void
     */
    public function test_show_not_auth(Closure $getConstructionId, bool $isExistingModel): void
    {
        $user = User::factory()->create();
        $constructionId = $getConstructionId();

        $response = $this->actingAs($user)->get(route('admin.constructions.edit', ['construction' => $constructionId]));

        if ($isExistingModel) {
            $response->assertRedirect(route('admin.home'));
            return;
        }

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public static function editTestProvider(): array
    {
        return [
            'existing' => [
                function () {
                    return Construction::inRandomOrder()->first()->id;
                },
                true,
            ],
            'not_existing_1' => [
                function () {
                    return Construction::orderBy('id', 'desc')->first()->id + 1;
                },
                false,
            ],
            'not_existing_2' => [
                function () {
                    return "test_test";
                },
                false,
            ]
        ];
    }
}
