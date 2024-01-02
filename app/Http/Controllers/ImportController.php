<?php

namespace App\Http\Controllers;


use App\Helpers\ModelHelper;
use App\Http\Requests\ImportFieldsRequest;
use App\Http\Requests\StartImportRequest;
use App\Jobs\ModelImportJob;
use App\Services\ImportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class ImportController extends Controller
{
    public function index(ImportService $exportService)
    {
        $models = $exportService->getAllowedModels();
        $fields = old('fields');
        $fieldsRendered = null;
        if (!empty($fields) && !empty(old('entity'))) {
            $fieldsRendered = View::make('pages.admin.import.fields', compact('fields'));;
        }
        return view('pages.admin.import.index', compact('models', 'fieldsRendered'));
    }

    public function getFields(ImportFieldsRequest $request)
    {
        $model = (new ($request->get('model')));
        $fields = $model->getFillable();
        return view('pages.admin.import.fields', compact('fields'));
    }

    public function start(StartImportRequest $request, ImportService $importService)
    {
        [
            'email' => $email,
            'entity' => $model,
            'withHeaders' => $withHeaders,
            'fields' => $fields,
        ] = $request->only(['email', 'entity', 'withHeaders', 'fields']);
        $modelFormatted = ImportService::IMPORTABLE_MODELS[$model];
        if (!$importService->canImport($model)) {
            return redirect()
                ->route('admin.import.index')
                ->with('error', __('admin.import_start_denied', ['model' => $modelFormatted]));
        }


        $filePath = Storage::disk(ModelImportJob::DISK_NAME)->putFileAs(
            ModelHelper::getNameFormatted($model),
            $request->files->get('file'),
            now()->format('Y-m-d H:m:s') . ".csv"
        );

        dispatch(new ModelImportJob($model, $filePath, $fields, $email, $withHeaders));

        return redirect()
            ->route('admin.import.index')
            ->with(
                'alert-success',
                __('admin.import_started', [
                    'model' => $modelFormatted,
                    'email' => $email,
                ])
            );
    }
}
