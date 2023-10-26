<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Models\Construction;
use App\Models\Language;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $languages = Language::all();
        return view('pages.admin.languages.index', [
            'languages' => $languages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('pages.admin.languages.create', [
            'constructionOptions' => $this->getConstructionOptions(),
            'constructions' => old('constructions'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request): RedirectResponse
    {
        /**
         * @var Language $language
         */
        $language = Language::create($request->only(['title', 'slug', 'description']));
        if ($request->has('constructions')) {
            foreach ($request->get('constructions') as $construction) {
                $language->constructions()->attach($construction['id'], [
                    'code' => $construction['code']
                ]);
            }
        }
        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_created', ['entity_name' => 'Язык программирования']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('pages.admin.languages.show', [
            'language' => $language,
            'constructionOptions' => $this->getConstructionOptions(),
            'constructions' => $this->getConstructionsFormatted($language),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('pages.admin.languages.edit', [
            'language' => $language,
            'constructionOptions' => $this->getConstructionOptions(),
            'constructions' => $this->getConstructionsFormatted($language),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, Language $language)
    {
        $language->update($request->only(['slug', 'title', 'description']));
        if ($request->has('constructions')) {
            $language->constructions()->detach();
            foreach ($request->get('constructions') as $construction) {
                $language->constructions()->attach($construction['id'], [
                    'code' => $construction['code']
                ]);
            }
        }
        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_updated', ['id' => $language->id]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        $language->delete();

        return redirect()
            ->route('admin.languages.index')
            ->with('alert-success', __('admin.row_deleted', ['id' => $language->id]));
    }

    /**
     * Возвращает отформатированные опции для селекта с конструкциями
     * @return array
     */
    private function getConstructionOptions(): array
    {
        $result = [];
        foreach (Construction::all(['id', 'title']) as $construction) {
            $result[] = [
                'value' => $construction->id,
                'title' => $construction->title,
            ];
        }
        return $result;
    }

    /**
     * Возвращает конструкции языка в форматированном для отображения виде
     *
     * @param \App\Models\Language $language
     *
     * @return array
     */
    private function getConstructionsFormatted(Language $language): array
    {
        if (!empty(old('consctructions'))) {
            return old('consctructions');
        } else {
            return array_map(function ($item) {
                return [
                    'id' => $item['pivot']['construction_id'],
                    'code' => $item['pivot']['code'],
                ];
            }, $language->constructions->toArray());
        }
    }
}
