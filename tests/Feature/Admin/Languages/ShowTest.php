<?php

namespace Tests\Feature\Admin\Languages;

use App\Enums\RolesEnum;
use App\Models\Construction;
use App\Models\Language;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @dataProvider showTestProvider
     *
     * @param \Closure $getLanguageId
     * @param bool $isExistingModel
     *
     * @return void
     */
    public function test_show(Closure $getLanguageId, bool $isExistingModel): void
    {
        $user = User::factory()->create()->assignRole(RolesEnum::ADMIN);
        $languageId = $getLanguageId();

        $response = $this->actingAs($user)->get(route('admin.languages.show', ['language' => $languageId]));

        if ($isExistingModel) {
            $response->assertOk();
            return;
        }

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider showTestProvider
     *
     * @param \Closure $getLanguageId
     * @param bool $isExistingModel
     *
     * @return void
     */
    public function test_show_not_auth(Closure $getLanguageId, bool $isExistingModel): void
    {
        $user = User::factory()->create();
        $languageId = $getLanguageId();

        $response = $this->actingAs($user)->get(route('admin.languages.show', ['language' => $languageId]));

        if ($isExistingModel) {
            $response->assertRedirect(route('admin.home'));
            return;
        }

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public static function showTestProvider(): array
    {
        return [
            'existing' => [
                function () {
                    return Language::inRandomOrder()->first()->id;
                },
                true,
            ],
            'not_existing_1' => [
                function () {
                    return Language::orderBy('id', 'desc')->first()->id + 1;
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
