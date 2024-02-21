<?php

namespace Tests\Feature\Api\V2;

use App\Models\Language;
use Tests\TestCase;

/**
 * @group api
 * @group languages-v2
 */
class ShowLanguageControllerTest extends TestCase
{
    const API_VERSION = 'v2';

    public function test_show_grouped(): void
    {
        $language = Language::latest()->first();

        $response = $this->getJson(
            route(sprintf("api.%s.languages.show", self::API_VERSION), [
                'group' => 'Y',
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
                    'codes' => [
                        '*'
                    ],
                ]
            ],
        ]);
    }

}
