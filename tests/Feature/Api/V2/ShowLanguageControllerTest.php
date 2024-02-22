<?php

namespace Tests\Feature\Api\V2;

use App\Models\Construction;
use App\Models\Language;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group api
 * @group languages-v2
 */
class ShowLanguageControllerTest extends TestCase
{
    use WithFaker;

    const API_VERSION = 'v2';

    public function test_show_grouped(): void
    {
        $language = $this->makeLanguageWithConstructions(true);

        $response = $this->getJson(
            route(sprintf("api.%s.languages.show", self::API_VERSION), [
                'group' => 'Y',
                'language' => $language['slug']
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
                    'codes' => [
                        '*'
                    ],
                ]
            ],
        ]);
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
