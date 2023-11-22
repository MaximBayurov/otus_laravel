<?php

namespace Tests\Feature\Admin\Languages;

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
     * @param Closure $getLanguageData
     * @param bool $assert
     *
     * @return void
     */
    public function test_store(Closure $getLanguageData, bool $assert): void
    {
        $this->refreshDatabase();

        $user = User::factory()->create()->assignRole(RolesEnum::ADMIN);

        $languageData = $getLanguageData();
        $this->actingAs($user)->post(route('admin.languages.store'), $languageData);

        unset($languageData['constructions']);
        if ($assert) {
            $this->assertDatabaseHas('languages', $languageData);
            return;
        }
        $this->assertDatabaseMissing('languages', $languageData);
    }

    /**
     * @dataProvider \Tests\Feature\Admin\Languages\CreateTest::providerTestCreate
     * @param \App\Enums\RolesEnum $role
     * @param bool $canCreate
     *
     * @return void
     */
    public function test_store_access(RolesEnum $role, bool $canCreate): void
    {
        $this->refreshDatabase();

        $user = User::factory()->create()->assignRole($role);

        $languageData = Language::factory()->make()->toArray();
        $this->actingAs($user)->post(
            route('admin.languages.store'),
            $languageData
        );

        if ($canCreate) {
            $this->assertDatabaseHas('languages', $languageData);
            return;
        }
        $this->assertDatabaseMissing('languages', $languageData);
    }

    public static function providerTestStore(): array
    {
        $getConstructionsFormatted = function () {
            return array_map(function($construction) {
                return [
                    'id' => $construction['id'],
                    'code' => fake()->text(50),
                ];
            }, Construction::factory()->count(3)->create()->toArray());
        };
        return [
            'with_constructions' => [
                function () use ($getConstructionsFormatted) {
                    return array_merge(
                        Language::factory()->make()->toArray(),
                        [
                            'constructions' => $getConstructionsFormatted()
                        ]
                    );
                },
                true,
            ],
            'without_constructions_1' => [
                function () {
                    return array_merge(
                        Language::factory()->make()->toArray(),
                        [
                            'constructions' => [
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
            'without_constructions_2' => [
                function () {
                    return array_merge(
                        Language::factory()->make()->toArray(),
                        [
                            'constructions' => []
                        ]
                    );
                },
                true,
            ],
            'without_constructions_3' => [
                function () {
                    return Language::factory()->make()->toArray();
                },
                true,
            ],
            'with_incorrect_constructions_1' => [
                function () {
                    return array_merge(
                        Language::factory()->make()->toArray(),
                        [
                            'constructions' => [
                                [
                                    'id' => Construction::factory()->create()->id,
                                    'code' => null
                                ]
                            ]
                        ]
                    );
                },
                false,
            ],
            'with_incorrect_constructions_2' => [
                function () {
                    return array_merge(
                        Language::factory()->make()->toArray(),
                        [
                            'constructions' => [
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
            'with_incorrect_constructions_3' => [
                function () use ($getConstructionsFormatted) {
                    $constructionsFormatted = $getConstructionsFormatted();
                    $constructionsFormatted[] = [
                        'id' => null,
                        'code' => fake()->text(50),
                    ];
                    return array_merge(
                        Language::factory()->make()->toArray(),
                        [
                            'constructions' => $constructionsFormatted
                        ]
                    );
                },
                false,
            ],
            'with_incorrect_constructions_4' => [
                function () use ($getConstructionsFormatted) {
                    $constructionsFormatted = $getConstructionsFormatted();
                    $constructionsFormatted[] = [
                        'id' => Construction::factory()->create()->id,
                        'code' => null,
                    ];
                    return array_merge(
                        Language::factory()->make()->toArray(),
                        [
                            'constructions' => $constructionsFormatted
                        ]
                    );
                },
                false,
            ],
        ];
    }
}
