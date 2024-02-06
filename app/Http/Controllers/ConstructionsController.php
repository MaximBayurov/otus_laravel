<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConstructionRequest;
use App\Http\Requests\UpdateConstructionRequest;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Auth;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository;
use Domain\ModuleLanguageConstructions\Services\ConstructionImplementationsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ConstructionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws NotFoundExceptionInterface|ContainerExceptionInterface
     */
    public function index(
        ConstructionsRepository $constructionsRepository
    ): Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Application {
        if (!Auth::user()?->can('viewAny', Construction::class)) {
            return redirect()->route('admin.home');
        }

        $page = (int) request()->get($constructionsRepository::PAGE_NAME, 1);
        $constructions = $constructionsRepository->getPagination($page);

        if ($constructions->count() === 0 && $constructions->hasPages()) {
            return redirect(route('admin.constructions.index'));
        }

        return view('pages.admin.constructions.index', compact('constructions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(
        ConstructionImplementationsService $implementationsService,
        LanguagesRepository $languagesRepository
    ): Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Application {
        if (!Auth::user()?->can('create', Construction::class)) {
            return redirect()->route('admin.home');
        }

        $languageOptions = $languagesRepository->getOptions();
        $languages = $implementationsService->filterEmpty(old('languages') ?? []);

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
        ConstructionImplementationsService $implementationsService,
        LanguagesRepository $languagesRepository
    ): Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Application {
        if (!Auth::user()?->can('viewAny', Construction::class)) {
            return redirect()->route('admin.home');
        }

        $languageOptions = $languagesRepository->getOptions();
        $languages = $implementationsService->getFormattedForConstruction($construction, old('languages', []));

        return view(
            'pages.admin.constructions.show',
            compact(
                'construction',
                'languages',
                'languageOptions'
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        Construction $construction,
        ConstructionImplementationsService $implementationsService,
        LanguagesRepository $languagesRepository
    ): Factory|View|\Illuminate\Foundation\Application|RedirectResponse|Application {
        if (!Auth::user()?->can('update', $construction)) {
            return redirect()->route('admin.home');
        }

        $languageOptions = $languagesRepository->getOptions();
        $languages = $implementationsService->getFormattedForConstruction($construction, old('languages', []));

        return view(
            'pages.admin.constructions.edit',
            compact(
                'construction',
                'languages',
                'languageOptions'
            )
        );
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
            ->with('alert-success', __('admin.row_updated', ['id' => $construction->getId()]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Construction $construction, ConstructionsRepository $constructionsRepository): RedirectResponse
    {
        if (!Auth::user()->can('delete', $construction)) {
            return redirect()->route('admin.home');
        }

        $constructionsRepository->delete($construction);

        return redirect()
            ->route('admin.constructions.index')
            ->with('alert-success', __('admin.row_deleted', ['id' => $construction->getId()]));
    }
}
