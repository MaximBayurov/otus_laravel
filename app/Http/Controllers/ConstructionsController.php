<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConstructionRequest;
use App\Http\Requests\UpdateConstructionRequest;
use App\Models\Construction;
use App\Services\CacheHelper;
use App\Services\LanguageService;
use Auth;
use Cache;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ConstructionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CacheHelper $cacheHelper): Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Application
    {
        if (!Auth::user()?->can('viewAny', Construction::class)) {
            return redirect()->route('admin.home');
        }

        $pageName = 'page';
        $page = request()->get($pageName);
        $constructions = Cache::tags([Construction::CACHE_TAG])->rememberForever(
            $cacheHelper->makeKey(['constructions:list', $page]),
            function () use ($pageName) {
                return Construction::paginate(10, pageName: $pageName);
            }
        );

        if (!empty($page) && (int)$page > 1 && $constructions->count() === 0) {
            return redirect(route('admin.constructions.index'));
        }

        return view('pages.admin.constructions.index', compact('constructions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(LanguageService $languageService): Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Application
    {
        if (!Auth::user()?->can('create', Construction::class)) {
            return redirect()->route('admin.home');
        }

        $languageOptions = $languageService->getLanguageOptions();
        $languages = $languageService->filterEmpty(old('languages') ?? []);

        return view('pages.admin.constructions.create', compact('languageOptions', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConstructionRequest $request): RedirectResponse
    {
        if (!Auth::user()?->can('create', Construction::class)) {
            return redirect()->route('admin.home');
        }

        $request->handle();

        return redirect()
            ->route('admin.constructions.index')
            ->with('alert-success', __('admin.row_created', ['entity_name' => 'Языковая конструкция']));
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Construction $construction,
        LanguageService $languageService
    ): Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Application
    {
        if (!Auth::user()?->can('viewAny', Construction::class)) {
            return redirect()->route('admin.home');
        }

        $languageOptions = $languageService->getLanguageOptions();
        $languages = $languageService->getLanguagesFormatted($construction);

        return view('pages.admin.constructions.show', compact(
            'construction',
            'languages',
            'languageOptions'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Construction $construction, LanguageService $languageService): Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Application
    {
        if (!Auth::user()?->can('update', $construction)) {
            return redirect()->route('admin.home');
        }

        $languageOptions = $languageService->getLanguageOptions();
        $languages = $languageService->getLanguagesFormatted($construction);

        return view('pages.admin.constructions.edit', compact(
            'construction',
            'languages',
            'languageOptions'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConstructionRequest $request, Construction $construction): RedirectResponse
    {
        if (!Auth::user()?->can('update', $construction)) {
            return redirect()->route('admin.home');
        }

        $request->handle($construction);

        return redirect()
            ->route('admin.constructions.index')
            ->with('alert-success', __('admin.row_updated', ['id' => $construction->id]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Construction $construction): RedirectResponse
    {
        if (!Auth::user()->can('delete', $construction)) {
            return redirect()->route('admin.home');
        }
        $construction->delete();
        Cache::tags([Construction::CACHE_TAG, Construction::CACHE_TAG])->flush();

        return redirect()
            ->route('admin.constructions.index')
            ->with('alert-success', __('admin.row_deleted', ['id' => $construction->id]));

    }
}
