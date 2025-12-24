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
        
        // El archivo en BD es FES-xxx.xml pero el archivo físico es FE-xxx.xml (sin la S)
        // Crear variantes del nombre
        $xmlVariants = [$xml];
        
        // Si empieza con FES-, también buscar FE-
        if (strpos($xml, 'FES-') === 0) {
            $xmlVariants[] = str_replace('FES-', 'FE-', $xml);
        }
        // Si empieza con NCS-, también buscar NC-
        if (strpos($xml, 'NCS-') === 0) {
            $xmlVariants[] = str_replace('NCS-', 'NC-', $xml);
        }
        // Si empieza con NDS-, también buscar ND-
        if (strpos($xml, 'NDS-') === 0) {
            $xmlVariants[] = str_replace('NDS-', 'ND-', $xml);
        }
        
        // Intentar varias rutas posibles con todas las variantes
        foreach ($xmlVariants as $xmlName) {
            $possiblePaths = [
                storage_path("app/public/{$nit}/{$xmlName}"),
                storage_path("app/{$nit}/{$xmlName}"),
                storage_path("app/public/{$xmlName}"),
            ];
            
            foreach ($possiblePaths as $filePath) {
                if (file_exists($filePath)) {
                    return response()->download($filePath, $xml); // Descargar con nombre original
                }
            }
        }
        
        return response()->json([
            'error' => 'Archivo XML no encontrado',
            'xml' => $xml,
            'nit' => $nit,
            'variants_tried' => $xmlVariants
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
        
        // El archivo en BD es FES-xxx.pdf pero el archivo físico es FE-xxx.pdf (sin la S)
        $pdfVariants = [$pdf];
        
        if (strpos($pdf, 'FES-') === 0) {
            $pdfVariants[] = str_replace('FES-', 'FE-', $pdf);
        }
        if (strpos($pdf, 'NCS-') === 0) {
            $pdfVariants[] = str_replace('NCS-', 'NC-', $pdf);
        }
        if (strpos($pdf, 'NDS-') === 0) {
            $pdfVariants[] = str_replace('NDS-', 'ND-', $pdf);
        }
        
        foreach ($pdfVariants as $pdfName) {
            $possiblePaths = [
                storage_path("app/public/{$nit}/{$pdfName}"),
                storage_path("app/{$nit}/{$pdfName}"),
                storage_path("app/public/{$pdfName}"),
            ];
            
            foreach ($possiblePaths as $filePath) {
                if (file_exists($filePath)) {
                    return response()->download($filePath, $pdf);
                }
            }
        }
        
        return response()->json([
            'error' => 'Archivo PDF no encontrado',
            'pdf' => $pdf,
            'nit' => $nit,
            'variants_tried' => $pdfVariants
        ], 404);
    }
   




 

    


}
