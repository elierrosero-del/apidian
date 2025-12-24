<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Document;
use App\Http\Resources\DocumentCollection;



class DocumentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {   
       // $list =  new CompaniesCollection(User::all());
        //return json_encode($list);
        return view('documents.index') ; 
    }

   

    public function records(Request $request)
    {
        $query = Document::query();
        
        // Filtrar por empresa (identification_number)
        if ($request->has('company') && $request->company) {
            $query->where('identification_number', $request->company);
        }
        
        $records = $query->orderBy('created_at', 'desc')->get();
        return new DocumentCollection($records);
    }


    public function downloadxml($xml)
    {
        // Buscar el documento por nombre de XML
        $document = Document::where('xml', $xml)->first();
        
        if (!$document) {
            return response()->json(['error' => 'Documento no encontrado en BD', 'xml' => $xml], 404);
        }
        
        $nit = $document->identification_number;
        
        // Intentar varias rutas posibles
        $possiblePaths = [
            storage_path("app/public/{$nit}/{$xml}"),
            storage_path("app/{$nit}/{$xml}"),
            storage_path("app/public/{$xml}"),
            storage_path("{$xml}"),
        ];
        
        foreach ($possiblePaths as $filePath) {
            if (file_exists($filePath)) {
                return response()->download($filePath);
            }
        }
        
        return response()->json([
            'error' => 'Archivo XML no encontrado',
            'xml' => $xml,
            'nit' => $nit,
            'paths_checked' => $possiblePaths
        ], 404);
    }

    public function downloadpdf($pdf)
    {
        // Buscar el documento por nombre de PDF
        $document = Document::where('pdf', $pdf)->first();
        
        if (!$document) {
            return response()->json(['error' => 'Documento no encontrado en BD', 'pdf' => $pdf], 404);
        }
        
        $nit = $document->identification_number;
        
        // Intentar varias rutas posibles
        $possiblePaths = [
            storage_path("app/public/{$nit}/{$pdf}"),
            storage_path("app/{$nit}/{$pdf}"),
            storage_path("app/public/{$pdf}"),
            storage_path("{$pdf}"),
        ];
        
        foreach ($possiblePaths as $filePath) {
            if (file_exists($filePath)) {
                return response()->download($filePath);
            }
        }
        
        return response()->json([
            'error' => 'Archivo PDF no encontrado',
            'pdf' => $pdf,
            'nit' => $nit,
            'paths_checked' => $possiblePaths
        ], 404);
    }
   




 

    


}
