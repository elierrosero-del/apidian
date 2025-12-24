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
            abort(404, 'Documento no encontrado');
        }
        
        $nit = $document->identification_number;
        $filePath = storage_path("app/public/{$nit}/{$xml}");
        
        if (!file_exists($filePath)) {
            abort(404, 'Archivo XML no encontrado');
        }
        
        return response()->download($filePath);
    }

    public function downloadpdf($pdf)
    {
        // Buscar el documento por nombre de PDF
        $document = Document::where('pdf', $pdf)->first();
        
        if (!$document) {
            abort(404, 'Documento no encontrado');
        }
        
        $nit = $document->identification_number;
        $filePath = storage_path("app/public/{$nit}/{$pdf}");
        
        if (!file_exists($filePath)) {
            abort(404, 'Archivo PDF no encontrado');
        }
        
        return response()->download($filePath);
    }
   




 

    


}
