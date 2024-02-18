<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConstructionRequest;
use App\Http\Requests\UpdateConstructionRequest;
use App\Http\Resources\Constructions;
use App\Http\Resources\Languages;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository;
use Domain\ModuleLanguageConstructions\Services\ConstructionImplementationsService;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class ConstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        Request $request,
        ConstructionsRepository $constructionsRepository
    ): Application|Constructions\CollectionResource|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application {
        $page = (int) $request->get(config('pagination.constructions_page_name'), 1);
        $constructions = $constructionsRepository->getPagination($page);
        $constructions->withPath($request->url());

        if ($constructions->hasPages() && $constructions->count() === 0) {
            return redirect(route($request->route()->getName()));
        }

        return new Constructions\CollectionResource($constructions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConstructionRequest $request): JsonResponse
    {
        if (!auth('api')->user()?->can('create', Construction::class)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $construction = $request->handle();

        return response()->json([
            'success' => true,
            'slug' => $construction->getSlug(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(
        string $construction,
        Request $request,
        ConstructionsRepository $constructionsRepository,
        ConstructionImplementationsService $implementationsService,
    ): JsonResponse {
        $construction = $constructionsRepository->getBySlug($construction);
        if (empty($construction)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $languages = ($request->get('group') === 'Y')
            ? $implementationsService->getGroupedWithCodes($construction->languages, Languages\ItemResource::class)
            : $construction->languages;

        return response()->json([
            'construction' => new Constructions\ItemResource($construction),
            'languages' => new Languages\CollectionResource($languages),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateConstructionRequest $request,
        string $construction,
        ConstructionsRepository $constructionsRepository
    ): JsonResponse {
        $construction = $constructionsRepository->getBySlug($construction);
        if (empty($construction)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if (!auth('api')->user()?->can('update', $construction)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->handle($construction);

        return response()->json([
            'success' => true,
            'slug' => $construction->getSlug(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        string $construction,
        ConstructionsRepository $constructionsRepository
    ): JsonResponse {
        $construction = $constructionsRepository->getBySlug($construction);
        if (empty($construction)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if (!auth('api')->user()?->can('delete', $construction)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $constructionsRepository->delete($construction);

        return response()->json([
            'success' => true,
            'slug' => $construction->getSlug(),
        ]);
    }
}
