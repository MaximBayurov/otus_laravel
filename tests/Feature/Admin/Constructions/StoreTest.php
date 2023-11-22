<?php

namespace Tests\Feature\Admin\Constructions;

use App\Enums\RolesEnum;
use App\Models\Construction;
use App\Models\Language;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @dataProvider providerTestStore
     *
     * @param Closure $getConstructionData
     * @param bool $assert
     *
     * @return void
     */
    public function test_store(Closure $getConstructionData, bool $assert): void
    {
        $this->refreshDatabase();

        $user = User::factory()->create()->assignRole(RolesEnum::ADMIN);

        $constructionData = $getConstructionData();
        $this->actingAs($user)->post(route('admin.constructions.store'), $constructionData);

        unset($constructionData['languages']);
        if ($assert) {
            $this->assertDatabaseHas('constructions', $constructionData);
            return;
        }
        $this->assertDatabaseMissing('constructions', $constructionData);
    }

    /**
     * @dataProvider \Tests\Feature\Admin\Constructions\CreateTest::providerTestCreate
     * @param \App\Enums\RolesEnum $role
     * @param bool $canCreate
     *
     * @return void
     */
    public function test_store_access(RolesEnum $role, bool $canCreate): void
    {
        $this->refreshDatabase();

        $user = User::factory()->create()->assignRole($role);

        $constructionData = Construction::factory()->make()->toArray();
        $this->actingAs($user)->post(
            route('admin.constructions.store'),
            $constructionData
        );

        if ($canCreate) {
            $this->assertDatabaseHas('constructions', $constructionData);
            return;
        }
        $this->assertDatabaseMissing('constructions', $constructionData);
    }

    public static function providerTestStore(): array
    {
        $getLanguagesFormatted = function () {
            return array_map(function($language) {
                return [
                    'id' => $language['id'],
                    'code' => fake()->text(50),
                ];
            }, Language::factory()->count(3)->create()->toArray());
        };
        return [
            'with_languages' => [
                function () use ($getLanguagesFormatted) {
                    return array_merge(
                        Construction::factory()->make()->toArray(),
                        [
                            'languages' => $getLanguagesFormatted()
                        ]
                    );
                },
                true,
            ],
            'without_languages_1' => [
                function () {
                    return array_merge(
                        Construction::factory()->make()->toArray(),
                        [
                            'languages' => [
                                [
                                    'id' => null,
                                    'code' => null,
                                ]
                            ]
                        ]
                    );
                },
                true,
            ],
            'without_languages_2' => [
                function () {
                    return array_merge(
                        Construction::factory()->make()->toArray(),
                        [
                            'languages' => []
                        ]
                    );
                },
                true,
            ],
            'without_languages_3' => [
                function () {
                    return Construction::factory()->make()->toArray();
                },
                true,
            ],
            'with_incorrect_languages_1' => [
                function () {
                    return array_merge(
                        Construction::factory()->make()->toArray(),
                        [
                            'languages' => [
                                [
                                    'id' => Language::factory()->create()->id,
                                    'code' => null
                                ]
                            ]
                        ]
                    );
                },
                false,
            ],
            'with_incorrect_languages_2' => [
                function () {
                    return array_merge(
                        Construction::factory()->make()->toArray(),
                        [
                            'languages' => [
                                [
                                    'id' => null,
                                    'code' => fake()->text(50),
                                ]
                            ]
                        ]
                    );
                },
                false,
            ],
            'with_incorrect_languages_3' => [
                function () use ($getLanguagesFormatted) {
                    $languagesFormatted = $getLanguagesFormatted();
                    $languagesFormatted[] = [
                        'id' => null,
                        'code' => fake()->text(50),
                    ];
                    return array_merge(
                        Construction::factory()->make()->toArray(),
                        [
                            'languages' => $languagesFormatted
                        ]
                    );
                },
                false,
            ],
            'with_incorrect_languages_4' => [
                function () use ($getLanguagesFormatted) {
                    $languagesFormatted = $getLanguagesFormatted();
                    $languagesFormatted[] = [
                        'id' => Language::factory()->create()->id,
                        'code' => null,
                    ];
                    return array_merge(
                        Construction::factory()->make()->toArray(),
                        [
                            'languages' => $languagesFormatted
                        ]
                    );
                },
                false,
            ],
        ];
    }
}
