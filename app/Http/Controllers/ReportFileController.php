<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ReportFileController extends Controller
{
    public function show(Report $report): Response
    {
        abort_unless($report->status === 'completed', 404);

        abort_unless(
            filled($report->path),
            404
        );

        abort_unless(
            Storage::disk('local')->exists($report->path),
            404
        );

        $path = Storage::disk('local')->path($report->path);

        $extension = strtolower(
            pathinfo($path, PATHINFO_EXTENSION)
        );

        abort_unless(
            $extension === 'pdf',
            404
        );

        return response()->file(
            $path,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            ]
        );
    }
}
