<?php

namespace Tests\Feature\Api\V1;

use App\Enums\RolesEnum;
use App\Models\Construction;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Api\JwtAuthControllerTest;
use Tests\TestCase;

/**
 * @group api
 * @group languages
 */
class LanguagesControllerTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public static function unsetFields()
    {
        return [
            'title' => [
                'title',
            ],
            'slug' => [
                'slug',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected const API_VERSION = 'v1';

    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        Language::factory()->createMany(20);

        $response = $this->getJson(
            route(
                sprintf("api.%s.languages.index", self::API_VERSION),
            )
        );

        $response->assertStatus(200);
        $response->assertJsonStructure($this->getListStructure());
    }

    /**
     * A basic feature test example.
     */
    public function test_index_with_redirect(): void
    {
        $response = $this->getJson(
            route(
                sprintf("api.%s.languages.index", self::API_VERSION),
                [
                    'languages-page' => 999999999
                ]
            )
        );

        $response->assertRedirect(route(sprintf("api.%s.languages.index", self::API_VERSION)));
    }

    public function test_store(): void
    {
        $response = $this->postJson(
            route(
                sprintf("api.%s.languages.store", self::API_VERSION),
            ),
            $this->makeLanguageWithConstructions(),
            [
                'Authorization' => 'Bearer asd.asdasdasd'
            ]
        );

        $response->assertStatus(403);
        $response->assertJsonStructure([
            'error'
        ]);
    }

    /**
     * @param string $field
     *
     * @dataProvider unsetFields
     * @return void
     */
    public function test_store_not_valid(string $field): void
    {
        $admin = User::factory()->create()->assignRole(RolesEnum::ADMIN);
        $this->actingAs($admin, JwtAuthControllerTest::API_AUTH_GUARD);

        $language = array_filter($this->makeLanguageWithConstructions(), function ($key) use ($field) {
            return $key !== $field;
        }, ARRAY_FILTER_USE_KEY);

        $response = $this->postJson(
            route(
                sprintf("api.%s.languages.store", self::API_VERSION),
            ),
            $language,
        );

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                $field
            ]
        ]);
    }

    public function test_store_admin(): void
    {
        $admin = User::factory()->create()->assignRole(RolesEnum::ADMIN);
        $this->actingAs($admin, JwtAuthControllerTest::API_AUTH_GUARD);

        $language = $this->makeLanguageWithConstructions();

        $response = $this->postJson(
            route(
                sprintf("api.%s.languages.store", self::API_VERSION),
            ),
            $language,
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'slug'
        ]);
        unset($language['constructions']);
        $this->assertDatabaseHas('languages', $language);
    }

    public function test_show(): void
    {
        $language = Language::latest()->first();

        $response = $this->getJson(
            route(sprintf("api.%s.languages.show", self::API_VERSION), [
                'language' => $language->slug
            ])
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'language' => [
                'id',
                'slug',
                'title',
                'description',
            ],
            'constructions' => [
                '*' => [
                    'id',
                    'slug',
                    'title',
                    'description',
                    'code',
                ]
            ],
        ]);
    }

    public function test_show_not_found(): void
    {
        $response = $this->getJson(
            route(sprintf("api.%s.languages.show", self::API_VERSION), [
                'group' => 'Y',
                'language' => 'absolutely_random' . $this->faker->randomNumber(5, true)
            ])
        );

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error'
        ]);
    }

    /**
     * @dataProvider unsetFields
     *
     * @param string $field
     *
     * @return void
     */
    public function test_update_not_valid(string $field): void
    {
        $admin = User::factory()->create()->assignRole(RolesEnum::ADMIN);
        $this->actingAs($admin, JwtAuthControllerTest::API_AUTH_GUARD);

        $language = $this->makeLanguageWithConstructions(true);
        $slug = $language['slug'];
        $language = array_filter($language, function ($key) use ($field) {
            return $key !== $field;
        }, ARRAY_FILTER_USE_KEY);

        $response = $this->patchJson(
            route(
                sprintf("api.%s.languages.update", self::API_VERSION),
                [
                    'language' => $slug
                ]
            ),
            $language,
        );

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                $field
            ]
        ]);
    }

    public function test_update_without_access(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, JwtAuthControllerTest::API_AUTH_GUARD);

        $language = $this->makeLanguageWithConstructions(true);

        $response = $this->patchJson(
            route(
                sprintf("api.%s.languages.update", self::API_VERSION),
                [
                    'language' => $language['slug']
                ]
            ),
            $language,
        );

        $response->assertStatus(403);
        $response->assertJsonStructure([
            'error'
        ]);
    }

    public function test_update_not_found(): void
    {
        $admin = User::factory()->create()->assignRole(RolesEnum::ADMIN);
        $this->actingAs($admin, JwtAuthControllerTest::API_AUTH_GUARD);

        $language = $this->makeLanguageWithConstructions(true);
        $language['slug'] .= $this->faker->randomNumber(5, true);

        $response = $this->patchJson(
            route(
                sprintf("api.%s.languages.update", self::API_VERSION),
                [
                    'language' => $language['slug'],
                ]
            ),
            $language,
        );

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error'
        ]);
    }

    public function test_update_admin(): void
    {
        $admin = User::factory()->create()->assignRole(RolesEnum::ADMIN);
        $this->actingAs($admin, JwtAuthControllerTest::API_AUTH_GUARD);

        $languageOld = $this->makeLanguageWithConstructions(true);
        $languageNew = $this->makeLanguageWithConstructions();
        $languageNew['id'] = $languageOld['id'];

        $response = $this->patchJson(
            route(
                sprintf("api.%s.languages.update", self::API_VERSION),
                [
                    'language' => $languageOld['slug'],
                ]
            ),
            $languageNew,
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'slug'
        ]);
        unset($languageNew['constructions']);
        $this->assertDatabaseHas('languages', $languageNew);
    }

    public function test_destroy_without_access(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, JwtAuthControllerTest::API_AUTH_GUARD);

        $language = $this->makeLanguageWithConstructions(true);

        $response = $this->deleteJson(
            route(
                sprintf("api.%s.languages.destroy", self::API_VERSION),
                [
                    'language' => $language['slug']
                ]
            ),
        );

        $response->assertStatus(403);
        $response->assertJsonStructure([
            'error'
        ]);
    }

    public function test_destroy_not_found(): void
    {
        $admin = User::factory()->create()->assignRole(RolesEnum::ADMIN);
        $this->actingAs($admin, JwtAuthControllerTest::API_AUTH_GUARD);

        $language = $this->makeLanguageWithConstructions(true);
        $language['slug'] .= $this->faker->randomNumber(5, true);

        $response = $this->deleteJson(
            route(
                sprintf("api.%s.languages.destroy", self::API_VERSION),
                [
                    'language' => $language['slug'],
                ]
            ),
        );

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error'
        ]);
    }

    public function test_destroy_admin(): void
    {
        $admin = User::factory()->create()->assignRole(RolesEnum::ADMIN);
        $this->actingAs($admin, JwtAuthControllerTest::API_AUTH_GUARD);

        $language = $this->makeLanguageWithConstructions(true);

        $response = $this->deleteJson(
            route(
                sprintf("api.%s.languages.destroy", self::API_VERSION),
                [
                    'language' => $language['slug'],
                ]
            ),
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'slug'
        ]);
        unset($language['constructions']);
        $this->assertDatabaseMissing('languages', $language);
    }

    private function getListStructure(): array
    {
        return [
            'data' => [
                '*' => [
                    'id',
                    'slug',
                    'title',
                    'description',
                ],
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active',
                    ],
                ],
            ],
        ];
    }

    private function makeLanguageWithConstructions(bool $create = false): array
    {
        $language = $create
            ? Language::factory()->create()->toArray()
            : Language::factory()->make()->toArray();
        foreach (Construction::factory()->createMany(10)->toArray() as $construction) {
            $language['constructions'][] = [
                'id' => $construction['id'],
                'code' => $this->faker->paragraph,
            ];
        }
        return $language;
    }
}
