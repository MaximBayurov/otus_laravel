<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Http\Resources\Constructions;
use App\Http\Resources\Languages;
use Domain\ModuleLanguageConstructions\Models\Language;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        Request $request,
        LanguagesRepository $languagesRepository
    ): Application|Languages\CollectionResource|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application {
        $page = (int) $request->get(config('pagination.languages_page_name'), 1);
        $languages = $languagesRepository->getPagination($page);
        $languages->withPath($request->url());

        if ($languages->hasPages() && $languages->count() === 0) {
            return redirect(route($request->route()->getName()));
        }

        return new Languages\CollectionResource($languages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request): JsonResponse
    {
        if (!auth('api')->user()?->can('create', Language::class)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $language = $request->handle();

        return response()->json([
            'success' => true,
            'slug' => $language->getSlug(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(
        string $language,
        LanguagesRepository $languagesRepository
    ): JsonResponse {
        $language = $languagesRepository->getBySlug($language);
        if (empty($language)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'language' => new Languages\ItemResource($language),
            'constructions' => new Constructions\CollectionResource($language->constructions),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateLanguageRequest $request,
        string $language,
        LanguagesRepository $languagesRepository
    ): JsonResponse {
        $language = $languagesRepository->getBySlug($language);
        if (empty($language)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if (!auth('api')->user()?->can('update', $language)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->handle($language);

        return response()->json([
            'success' => true,
            'slug' => $language->getSlug(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        string $language,
        LanguagesRepository $languagesRepository
    ): JsonResponse {
        $language = $languagesRepository->getBySlug($language);
        if (empty($language)) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if (!auth('api')->user()?->can('delete', $language)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $languagesRepository->delete($language);

        return response()->json([
            'success' => true,
            'slug' => $language->getSlug(),
        ]);
    }
}
