<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Document;
use App\Company;
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
        
        // Filtrar por tipo de documento
        if ($request->has('type') && $request->type) {
            $query->where('type_document_id', $request->type);
        }
        
        // Búsqueda unificada (número o cliente)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', '%' . $search . '%')
                  ->orWhereHas('client', function($qc) use ($search) {
                      $qc->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Paginación
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        
        $total = $query->count();
        $records = $query->orderBy('created_at', 'desc')
                        ->skip(($page - 1) * $perPage)
                        ->take($perPage)
                        ->get();
        
        // Procesar documentos con información adicional
        $data = [];
        foreach ($records as $key => $row) {
            // Obtener ambiente de la empresa
            $environment = 2; // Por defecto habilitación
            if ($row->identification_number) {
                $company = Company::where('identification_number', $row->identification_number)->first();
                if ($company && $company->type_environment_id) {
                    $environment = $company->type_environment_id;
                }
            }
            
            // Determinar estado real del documento
            $stateId = $row->state_document_id ?? 0;
            $hasCufe = $row->cufe && strlen($row->cufe) > 10;
            
            $stateName = 'Pendiente';
            $stateClass = 'warning';
            $canResend = true;
            
            if ($stateId == 1 && $hasCufe) {
                $stateName = 'Procesado';
                $stateClass = 'success';
                $canResend = false;
            } elseif ($stateId == 1 && !$hasCufe) {
                $stateName = 'Enviado';
                $stateClass = 'info';
                $canResend = true;
            } elseif ($stateId == 0 || !$hasCufe) {
                $stateName = 'Pendiente';
                $stateClass = 'warning';
                $canResend = true;
            }
            
            // Tipo de documento
            $typeDocId = $row->type_document_id;
            $typeDocName = 'Documento';
            switch ($typeDocId) {
                case 1: $typeDocName = 'Factura'; break;
                case 2: $typeDocName = 'Factura Exp.'; break;
                case 3: $typeDocName = 'Contingencia'; break;
                case 4: $typeDocName = 'Nota Crédito'; break;
                case 5: $typeDocName = 'Nota Débito'; break;
                case 6: $typeDocName = 'Nómina'; break;
                case 7: $typeDocName = 'Nómina Ajuste'; break;
                case 11: $typeDocName = 'Doc. Soporte'; break;
                case 12: $typeDocName = 'Nota Ajuste DS'; break;
                case 13: $typeDocName = 'NC Doc. Soporte'; break;
            }
            
            $data[] = [
                'key' => $key + 1,
                'id' => $row->id,
                'number' => $row->number,
                'prefix' => $row->prefix,
                'client' => $row->client->name ?? 'N/A',
                'date' => $row->date_issue,
                'total' => $row->total,
                'xml' => $row->xml,
                'pdf' => $row->pdf,
                'cufe' => $row->cufe,
                'state_id' => $stateId,
                'state_name' => $stateName,
                'state_class' => $stateClass,
                'can_resend' => $canResend,
                'type_document_name' => $typeDocName,
                'environment' => $environment, // 1=Producción, 2=Habilitación
                'identification_number' => $row->identification_number,
            ];
        }
        
        return response()->json([
            'data' => $data,
            'total' => $total,
            'page' => (int)$page,
            'per_page' => (int)$perPage,
            'last_page' => ceil($total / $perPage)
        ]);
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
