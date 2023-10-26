<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConstructionRequest;
use App\Http\Requests\UpdateConstructionRequest;
use App\Models\Construction;
use App\Models\Language;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ConstructionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $constructions = Construction::all();
        return view('pages.admin.constructions.index', [
            'constructions' => $constructions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('pages.admin.constructions.create', [
            'languageOptions' => $this->getLanguageOptions(),
            'languages' => old('languages'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConstructionRequest $request): RedirectResponse
    {
        /**
         * @var Construction $construction
         */
        $construction = Construction::create($request->only(['title', 'slug', 'description']));
        if ($request->has('languages')) {
            foreach ($request->get('languages') as $language) {
                $construction->languages()->attach($language['id'], [
                    'code' => $language['code']
                ]);
            }
        }
        return redirect()
            ->route('admin.constructions.index')
            ->with('alert-success', __('admin.row_created', ['entity_name' => 'Языковая конструкция']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Construction $construction): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('pages.admin.constructions.show', [
            'construction' => $construction,
            'languageOptions' => $this->getLanguageOptions(),
            'languages' => $this->getLanguagesFormatted($construction),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Construction $construction): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('pages.admin.constructions.edit', [
            'construction' => $construction,
            'languageOptions' => $this->getLanguageOptions(),
            'languages' => $this->getLanguagesFormatted($construction),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConstructionRequest $request, Construction $construction): RedirectResponse
    {
        $construction->update($request->only(['slug', 'title', 'description']));
        if ($request->has('languages')) {
            $construction->languages()->detach();
            foreach ($request->get('languages') as $language) {
                $construction->languages()->attach($language['id'], [
                    'code' => $language['code']
                ]);
            }
        }
        return redirect()
            ->route('admin.constructions.index')
            ->with('alert-success', __('admin.row_updated', ['id' => $construction->id]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Construction $construction): RedirectResponse
    {
        $construction->delete();

        return redirect()
            ->route('admin.constructions.index')
            ->with('alert-success', __('admin.row_deleted', ['id' => $construction->id]));

    }



    /**
     * Возвращает отформатированные опции для селекта с языками
     * @return array
     */
    private function getLanguageOptions(): array
    {
        $result = [];
        foreach (Language::all(['id', 'title']) as $language) {
            $result[] = [
                'value' => $language->id,
                'title' => $language->title,
            ];
        }
        return $result;
    }

    /**
     * Возвращает языки программирования в форматированном для отображения виде
     *
     * @param \App\Models\Construction $construction
     *
     * @return array
     */
    private function getLanguagesFormatted(Construction $construction): array
    {
        if (!empty(old('languages'))) {
            return old('languages');
        } else {
            return array_map(function ($item) {
                return [
                    'id' => $item['pivot']['language_id'],
                    'code' => $item['pivot']['code'],
                ];
            }, $construction->languages->toArray());
        }
    }
}
