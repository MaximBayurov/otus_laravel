<?php

namespace Tests\Feature\Admin\Languages;

use App\Enums\RolesEnum;
use App\Models\Construction;
use App\Models\Language;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @dataProvider providerTestUpdate
     *
     * @param \Closure $createLanguage
     * @param $assert
     *
     * @return void
     */
    public function test_update(Closure $createLanguage, $assert): void
    {
        $this->refreshDatabase();

        $user = User::factory()->create()->assignRole(RolesEnum::ADMIN);

        $languageData = $createLanguage();
        $languageDataNew = array_merge(
            $languageData,
            Language::factory()->make()->toArray()
        );
        $this->actingAs($user)->patch(route('admin.languages.update', ['language' => $languageData['id']]), $languageDataNew);

        unset(
            $languageData['constructions'],
            $languageDataNew['constructions'],
        );
        if ($assert) {
            $this->assertDatabaseHas('languages', [
                'id' => $languageDataNew['id']
            ]);
            return;
        }
        $this->assertDatabaseHas('languages', [
            'id' => $languageData['id']
        ]);
    }

    public static function providerTestUpdate(): array
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
                        Language::factory()->create()->toArray(),
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
                        Language::factory()->create()->toArray(),
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
                        Language::factory()->create()->toArray(),
                        [
                            'constructions' => []
                        ]
                    );
                },
                true,
            ],
            'without_constructions_3' => [
                function () {
                    return Language::factory()->create()->toArray();
                },
                true,
            ],
            'with_incorrect_constructions_1' => [
                function () {
                    return array_merge(
                        Language::factory()->create()->toArray(),
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
                        Language::factory()->create()->toArray(),
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
                        Language::factory()->create()->toArray(),
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
                        Language::factory()->create()->toArray(),
                        [
                            'constructions' => $constructionsFormatted
                        ]
                    );
                },
                false,
            ],
        ];
    }

    /**
     * @dataProvider providerTestUpdateAccess
     * @param \App\Enums\RolesEnum $role
     * @param bool $canEdit
     *
     * @return void
     */
    public function test_update_access(RolesEnum $role, bool $canEdit): void
    {
        $this->refreshDatabase();

        $user = User::factory()->create()->assignRole($role);

        $languageData = Language::factory()->create()->toArray();
        $languageDataNew = array_merge(
            $languageData,
            Language::factory()->make()->toArray()
        );
        $this->actingAs($user)->patch(
            route('admin.languages.update', ['language' => $languageData['id']]),
            $languageDataNew
        );

        if ($canEdit) {
            $this->assertDatabaseHas('languages', [
                'id' => $languageDataNew['id']
            ]);
            return;
        }
        $this->assertDatabaseHas('languages', [
            'id' => $languageData['id']
        ]);
    }

    public static function providerTestUpdateAccess(): array
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
