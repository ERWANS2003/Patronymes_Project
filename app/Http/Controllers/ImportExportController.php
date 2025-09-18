<?php

namespace App\Http\Controllers;

use App\Exports\PatronymesExport;
use App\Imports\PatronymesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportExportController extends Controller
{
    public function export(): BinaryFileResponse
    {
        return Excel::download(new PatronymesExport, 'patronymes-' . date('Y-m-d') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new PatronymesImport, $request->file('file'));

            return redirect()->back()
                ->with('success', 'Importation réussie !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    public function showImportForm()
    {
        return view('admin.import');
    }
}
