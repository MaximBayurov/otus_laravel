<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Models\Construction;
use App\Models\Language;
use App\Services\CacheHelper;
use App\Services\ConstructionService;
use App\Services\LanguageService;
use Auth;
use Cache;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LanguageService $languageService): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application
    {
        if (!Auth::user()?->can('viewAny', Language::class)) {
            return redirect()->route('admin.home');
        }

        $page = (int)request()->get($languageService::PAGE_NAME, 1);
        $languages = $languageService->getPagination($page);

        if ($languages->hasPages() && $languages->count() === 0) {
            return redirect(route('admin.languages.index'));
        }

        return view('pages.admin.languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ConstructionService $constructionService): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application
    {
        if (!Auth::user()?->can('create', Language::class)) {
            return redirect()->route('admin.home');
        }

        $constructionOptions = $constructionService->getConstructionOptions();
        $constructions = $constructionService->filterEmpty(old('constructions') ?? []);

        return view('pages.admin.languages.create', compact([
            'constructions',
            'constructionOptions'
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request): RedirectResponse
    {
        if (!Auth::user()?->can('create', Language::class)) {
            return redirect()->route('admin.home');
        }

        $language = $request->handle();

        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_created', ['entity_name' => 'Язык программирования']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language, ConstructionService $constructionService): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application
    {
        if (!Auth::user()?->can('viewAny', Language::class)) {
            return redirect()->route('admin.home');
        }

        $constructionOptions = $constructionService->getConstructionOptions();
        $constructions = $constructionService->getConstructionsFormatted($language);

        return view('pages.admin.languages.show', compact([
            'language',
            'constructions',
            'constructionOptions',
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language, ConstructionService $constructionService): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application
    {
        if (!Auth::user()?->can('update', $language)) {
            return redirect()->route('admin.home');
        }

        $constructionOptions = $constructionService->getConstructionOptions();
        $constructions = $constructionService->getConstructionsFormatted($language);

        return view('pages.admin.languages.edit', compact([
            'language',
            'constructions',
            'constructionOptions',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, Language $language): RedirectResponse
    {
        if (!Auth::user()?->can('update', $language)) {
            return redirect()->route('admin.home');
        }

        $language = $request->handle($language);

        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_updated', ['id' => $language->id]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language): RedirectResponse
    {
        if (!Auth::user()?->can('delete', $language)) {
            return redirect()->route('admin.home');
        }
        $language->delete();
        \Cache::tags([Language::CACHE_TAG, Construction::CACHE_TAG])->flush();

        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_deleted', ['id' => $language->id]));
    }
}
