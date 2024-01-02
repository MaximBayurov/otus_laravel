<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartExportRequest;
use App\Jobs\ModelExportJob;
use App\Services\ExportService;

class ExportController extends Controller
{
    public function index(ExportService $exportService)
    {
        $models = $exportService->getAllowedModels();

        return view('pages.admin.export.index', compact('models'));
    }

    public function start(StartExportRequest $request, ExportService $exportService)
    {
        [
            'email' => $email,
            'entity' => $model,
            'redo' => $redo
        ] = $request->only(['email', 'entity', 'redo']);
        $modelFormatted = ExportService::EXPORTABLE_MODELS[$model];
        if (!$exportService->canExport($model)) {
            return redirect()
                ->route('admin.export.index')
                ->with('error', __('admin.export_start_denied', ['model' => $modelFormatted]));
        }

        dispatch(new ModelExportJob($model, $email, $redo));

        return redirect()
            ->route('admin.export.index')
            ->with(
                'alert-success',
                __('admin.export_started', [
                    'model' => $modelFormatted,
                    'email' => $email,
                ])
            );
    }
}
