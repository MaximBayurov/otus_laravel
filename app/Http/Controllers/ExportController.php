<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartExportRequest;
use App\Jobs\ModelExportJob;
use App\Services\ExportService;

class ExportController extends Controller
{
    public function index(ExportService $exportService)
    {
        if (!\Auth::user()?->can('admin.export')) {
            return redirect()->route('admin.home');
        }

        $models = $exportService->getAllowedModels();

        return view('pages.admin.export.index', compact('models'));
    }

    public function start(StartExportRequest $request)
    {
        $data = $request->validated();
        $modelFormatted = ExportService::EXPORTABLE_MODELS[$data['entity']];
        if (!\Auth::user()?->can('admin.export.model', $data['entity'])) {
            return redirect()
                ->route('admin.export.index')
                ->with('error', __('admin.export_start_denied', ['model' => $modelFormatted]));
        }

        dispatch(new ModelExportJob($data['entity'], $data['email'], (bool) $data['redo']));

        return redirect()
            ->route('admin.export.index')
            ->with(
                'alert-success',
                __('admin.export_started', [
                    'model' => $modelFormatted,
                    'email' => $data['email'],
                ])
            );
    }
}
