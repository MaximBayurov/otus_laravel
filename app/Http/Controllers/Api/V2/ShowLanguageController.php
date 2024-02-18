<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository;
use Domain\ModuleLanguageConstructions\Services\ConstructionImplementationsService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Languages;
use App\Http\Resources\Constructions;
use Illuminate\Http\Request;

class ShowLanguageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        string $language,
        Request $request,
        LanguagesRepository $languagesRepository,
        ConstructionImplementationsService $implementationsService,
    ): JsonResponse {
        $language = $languagesRepository->getBySlug($language);
        if (empty($language)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $constructions = ($request->get('group') === 'Y')
            ? $implementationsService->getGroupedWithCodes($language->constructions,Constructions\ItemResource::class)
            : $language->constructions;

        return response()->json([
            'language' => new Languages\ItemResource($language),
            'constructions' => new Constructions\CollectionResource($constructions),
        ]);
    }
}
