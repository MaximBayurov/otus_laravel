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
 * @group constructions
 */
class ConstructionsControllerTest extends TestCase
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
        Construction::factory()->createMany(20);

        $response = $this->getJson(
            route(
                sprintf("api.%s.constructions.index", self::API_VERSION),
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
                sprintf("api.%s.constructions.index", self::API_VERSION),
                [
                    'constructions-page' => 999999999
                ]
            )
        );

        $response->assertRedirect(route(sprintf("api.%s.constructions.index", self::API_VERSION)));
    }

    public function test_store(): void
    {
        $response = $this->postJson(
            route(
                sprintf("api.%s.constructions.store", self::API_VERSION),
            ),
            $this->makeConstructionWithLanguages(),
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

        $construction = array_filter($this->makeConstructionWithLanguages(), function ($key) use ($field) {
            return $key !== $field;
        }, ARRAY_FILTER_USE_KEY);

        $response = $this->postJson(
            route(
                sprintf("api.%s.constructions.store", self::API_VERSION),
            ),
            $construction,
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

        $construction = $this->makeConstructionWithLanguages();

        $response = $this->postJson(
            route(
                sprintf("api.%s.constructions.store", self::API_VERSION),
            ),
            $construction,
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'slug'
        ]);
        unset($construction['languages']);
        $this->assertDatabaseHas('constructions', $construction);
    }

    public function test_show(): void
    {
        $construction = Construction::latest()->first();

        $response = $this->getJson(
            route(sprintf("api.%s.constructions.show", self::API_VERSION), [
                'construction' => $construction->slug
            ])
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'construction' => [
                'id',
                'slug',
                'title',
                'description',
            ],
            'languages' => [
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

    public function test_show_grouped(): void
    {
        $construction = Construction::latest()->first();

        $response = $this->getJson(
            route(sprintf("api.%s.constructions.show", self::API_VERSION), [
                'group' => 'Y',
                'construction' => $construction->slug
            ])
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'construction' => [
                'id',
                'slug',
                'title',
                'description',
            ],
            'languages' => [
                '*' => [
                    'id',
                    'slug',
                    'title',
                    'description',
                    'codes' => [],
                ]
            ],
        ]);
    }

    public function test_show_not_found(): void
    {
        $response = $this->getJson(
            route(sprintf("api.%s.constructions.show", self::API_VERSION), [
                'group' => 'Y',
                'construction' => 'absolutely_random' . $this->faker->randomNumber(5, true)
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

        $construction = $this->makeConstructionWithLanguages(true);
        $slug = $construction['slug'];
        $construction = array_filter($construction, function ($key) use ($field) {
            return $key !== $field;
        }, ARRAY_FILTER_USE_KEY);

        $response = $this->patchJson(
            route(
                sprintf("api.%s.constructions.update", self::API_VERSION),
                [
                    'construction' => $slug
                ]
            ),
            $construction,
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

        $construction = $this->makeConstructionWithLanguages(true);

        $response = $this->patchJson(
            route(
                sprintf("api.%s.constructions.update", self::API_VERSION),
                [
                    'construction' => $construction['slug']
                ]
            ),
            $construction,
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

        $construction = $this->makeConstructionWithLanguages(true);
        $construction['slug'] .= $this->faker->randomNumber(5, true);

        $response = $this->patchJson(
            route(
                sprintf("api.%s.constructions.update", self::API_VERSION),
                [
                    'construction' => $construction['slug'],
                ]
            ),
            $construction,
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

        $constructionOld = $this->makeConstructionWithLanguages(true);
        $constructionNew = $this->makeConstructionWithLanguages();
        $constructionNew['id'] = $constructionOld['id'];

        $response = $this->patchJson(
            route(
                sprintf("api.%s.constructions.update", self::API_VERSION),
                [
                    'construction' => $constructionOld['slug'],
                ]
            ),
            $constructionNew,
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'slug'
        ]);
        unset($constructionNew['languages']);
        $this->assertDatabaseHas('constructions', $constructionNew);
    }

    public function test_destroy_without_access(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, JwtAuthControllerTest::API_AUTH_GUARD);

        $construction = $this->makeConstructionWithLanguages(true);

        $response = $this->deleteJson(
            route(
                sprintf("api.%s.constructions.destroy", self::API_VERSION),
                [
                    'construction' => $construction['slug']
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

        $construction = $this->makeConstructionWithLanguages(true);
        $construction['slug'] .= $this->faker->randomNumber(5, true);

        $response = $this->deleteJson(
            route(
                sprintf("api.%s.constructions.destroy", self::API_VERSION),
                [
                    'construction' => $construction['slug'],
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

        $construction = $this->makeConstructionWithLanguages(true);

        $response = $this->deleteJson(
            route(
                sprintf("api.%s.constructions.destroy", self::API_VERSION),
                [
                    'construction' => $construction['slug'],
                ]
            ),
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'slug'
        ]);
        unset($construction['languages']);
        $this->assertDatabaseMissing('constructions', $construction);
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

    private function makeConstructionWithLanguages(bool $create = false): array
    {
        $construction = $create
            ? Construction::factory()->create()->toArray()
            : Construction::factory()->make()->toArray();
        foreach (Language::factory()->createMany(10)->toArray() as $language) {
            $construction['languages'][] = [
                'id' => $language['id'],
                'code' => $this->faker->paragraph,
            ];
        }
        return $construction;
    }
}
