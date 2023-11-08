<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Models\Construction;
use App\Models\Language;
use App\Services\ConstructionService;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application
    {
        if (!Auth::user()->can('viewAny', Language::class)) {
            return redirect()->route('admin.home');
        }
        return view('pages.admin.languages.index', [
            'languages' => Language::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ConstructionService $constructionService): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application
    {
        if (!Auth::user()->can('create', Construction::class)) {
            return redirect()->route('admin.home');
        }
        return view('pages.admin.languages.create', [
            'constructionOptions' => $constructionService->getConstructionOptions(),
            'constructions' => $constructionService->filterEmpty(old('constructions') ?? []),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request): RedirectResponse
    {
        if (!Auth::user()->can('create', Construction::class)) {
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
        if (!Auth::user()->can('viewAny', Language::class)) {
            return redirect()->route('admin.home');
        }
        return view('pages.admin.languages.show', [
            'language' => $language,
            'constructionOptions' => $constructionService->getConstructionOptions(),
            'constructions' => $constructionService->getConstructionsFormatted($language),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language, ConstructionService $constructionService): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application
    {
        if (!Auth::user()->can('update', $language)) {
            return redirect()->route('admin.home');
        }
        return view('pages.admin.languages.edit', [
            'language' => $language,
            'constructionOptions' => $constructionService->getConstructionOptions(),
            'constructions' => $constructionService->getConstructionsFormatted($language),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, Language $language): RedirectResponse
    {
        if (!Auth::user()->can('update', $language)) {
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
        if (!Auth::user()->can('delete', $language)) {
            return redirect()->route('admin.home');
        }
        $language->delete();

        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_deleted', ['id' => $language->id]));
    }
}
