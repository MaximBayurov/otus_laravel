<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use Auth;
use Domain\ModuleLanguageConstructions\Models\Language;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository;
use Domain\ModuleLanguageConstructions\Services\ConstructionImplementationsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function index(
        LanguagesRepository $languagesRepository
    ): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application {
        if (!Auth::user()?->can('viewAny', Language::class)) {
            return redirect()->route('admin.home');
        }

        $page = (int) request()->get(config('pagination.languages_page_name'), 1);
        $languages = $languagesRepository->getPagination($page);

        if ($languages->hasPages() && $languages->count() === 0) {
            return redirect(route('admin.languages.index'));
        }

        return view('pages.admin.languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(
        ConstructionImplementationsService $implementationsService,
        ConstructionsRepository $constructionsRepository
    ): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application {
        if (!Auth::user()?->can('create', Language::class)) {
            return redirect()->route('admin.home');
        }

        $constructionOptions = $constructionsRepository->getOptions();
        $constructions = $implementationsService->filterEmpty(old('constructions') ?? []);

        return view(
            'pages.admin.languages.create',
            compact([
                'constructions',
                'constructionOptions',
            ])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request): RedirectResponse
    {
        if (!Auth::user()?->can('create', Language::class)) {
            return redirect()->route('admin.home');
        }

        $request->handle();

        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_created', ['entity_name' => 'Язык программирования']));
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Language $language,
        ConstructionImplementationsService $implementationsService,
        ConstructionsRepository $constructionsRepository
    ): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application {
        if (!Auth::user()?->can('viewAny', Language::class)) {
            return redirect()->route('admin.home');
        }

        $constructionOptions = $constructionsRepository->getOptions();
        $constructions = $implementationsService->getFormattedForLanguage($language, old('constructions', []));

        return view(
            'pages.admin.languages.show',
            compact([
                'language',
                'constructions',
                'constructionOptions',
            ])
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        Language $language,
        ConstructionsRepository $constructionsRepository,
        ConstructionImplementationsService $implementationsService
    ): View|\Illuminate\Foundation\Application|Factory|RedirectResponse|Application {
        if (!Auth::user()?->can('update', $language)) {
            return redirect()->route('admin.home');
        }

        $constructionOptions = $constructionsRepository->getOptions();
        $constructions = $implementationsService->getFormattedForLanguage($language, old('constructions', []));

        return view(
            'pages.admin.languages.edit',
            compact([
                'language',
                'constructions',
                'constructionOptions',
            ])
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, Language $language): RedirectResponse
    {
        if (!Auth::user()?->can('update', $language)) {
            return redirect()->route('admin.home');
        }

        $request->handle($language);

        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_updated', ['id' => $language->getId()]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language, LanguagesRepository $languagesRepository): RedirectResponse
    {
        if (!Auth::user()?->can('delete', $language)) {
            return redirect()->route('admin.home');
        }

        $languagesRepository->delete($language);

        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_deleted', ['id' => $language->getId()]));
    }
}
