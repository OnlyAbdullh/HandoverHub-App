<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BrandsImport;

class BrandImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'type' => 'nullable|string',  // اختياريّ
        ]);

        $type = $request->input('type', 'generator');

        try {
            Excel::import(
                new BrandsImport($type),
                $request->file('file')
            );
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Import failed.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => "Brands imported successfully with type: {$type}",
        ], 200);
    }

}
