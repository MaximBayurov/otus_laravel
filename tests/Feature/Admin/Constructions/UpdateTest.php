<?php

namespace Tests\Feature\Admin\Constructions;

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
     * @param \Closure $createConstruction
     * @param $assert
     *
     * @return void
     */
    public function test_update(Closure $createConstruction, $assert): void
    {
        $this->refreshDatabase();

        $user = User::factory()->create()->assignRole(RolesEnum::ADMIN);

        $constructionData = $createConstruction();
        $constructionDataNew = array_merge(
            $constructionData,
            Construction::factory()->make()->toArray()
        );
        $this->actingAs($user)->patch(route('admin.constructions.update', ['construction' => $constructionData['id']]), $constructionDataNew);

        unset(
            $constructionData['languages'],
            $constructionDataNew['languages'],
        );
        if ($assert) {
            $this->assertDatabaseHas('constructions', [
                'id' => $constructionDataNew['id']
            ]);
            return;
        }
        $this->assertDatabaseHas('constructions', [
            'id' => $constructionData['id']
        ]);
    }

    public static function providerTestUpdate(): array
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
                        Construction::factory()->create()->toArray(),
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
                        Construction::factory()->create()->toArray(),
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
                        Construction::factory()->create()->toArray(),
                        [
                            'languages' => []
                        ]
                    );
                },
                true,
            ],
            'without_languages_3' => [
                function () {
                    return Construction::factory()->create()->toArray();
                },
                true,
            ],
            'with_incorrect_languages_1' => [
                function () {
                    return array_merge(
                        Construction::factory()->create()->toArray(),
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
                        Construction::factory()->create()->toArray(),
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
                        Construction::factory()->create()->toArray(),
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
                        Construction::factory()->create()->toArray(),
                        [
                            'languages' => $languagesFormatted
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

        $constructionData = Construction::factory()->create()->toArray();
        $constructionDataNew = array_merge(
            $constructionData,
            Construction::factory()->make()->toArray()
        );
        $this->actingAs($user)->patch(
            route('admin.constructions.update', ['construction' => $constructionData['id']]),
            $constructionDataNew
        );

        if ($canEdit) {
            $this->assertDatabaseHas('constructions', [
                'id' => $constructionDataNew['id']
            ]);
            return;
        }
        $this->assertDatabaseHas('constructions', [
            'id' => $constructionData['id']
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
