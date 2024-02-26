<?php

namespace App\Http\Controllers;

use App\Helpers\ModelHelper;
use App\Http\Requests\ImportFieldsRequest;
use App\Http\Requests\StartImportRequest;
use App\Jobs\ModelImportJob;
use App\Services\ImportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function index(ImportService $importService)
    {
        if (!\Auth::user()?->can('admin.import')) {
            return redirect()->route('admin.home');
        }

        $models = $importService->getAllowedModels();
        $fields = old('fields');
        $fieldsRendered = null;
        if (!empty($fields) && !empty(old('entity'))) {
            $fieldsRendered = View::make('pages.admin.import.fields', compact('fields'));
        }

        return view('pages.admin.import.index', compact('models', 'fieldsRendered'));
    }

    public function getFields(ImportFieldsRequest $request)
    {
        $model = (new ($request->get('model')));
        $fields = $model->getFillable();

        return view('pages.admin.import.fields', compact('fields'));
    }

    public function start(
        StartImportRequest $request,
        ImportService $importService
    ) {
        $data = $request->validated();
        $modelFormatted = ImportService::IMPORTABLE_MODELS[$data['entity']];
        if (!$importService->canImport($data['entity'])) {
            return redirect()
                ->route('admin.import.index')
                ->with('error', __('admin.import_start_denied', ['model' => $modelFormatted]));
        }

        $filePath = Storage::disk(ModelImportJob::DISK_NAME)->putFileAs(
            ModelHelper::getNameFormatted($data['entity']),
            $request->files->get('file'),
            Str::uuid()->toString() . ".csv"
        );

        dispatch(new ModelImportJob($data['entity'], $filePath, $data['fields'], $data['email'], $data['withHeaders']));

        return redirect()
            ->route('admin.import.index')
            ->with(
                'alert-success',
                __('admin.import_started', [
                    'model' => $modelFormatted,
                    'email' => $data['email'],
                ])
            );
    }
}
