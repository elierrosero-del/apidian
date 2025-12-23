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
        //$invoice =  Document::find($id);
        return response()->download(storage_path($xml));
    }

    public function downloadpdf($pdf)
    {

        return response()->download(storage_path("app/{$pdf}"));
       // return false;
        //$invoice =  Document::find($id);
       // return response()->download(storage_path("app\FES-SETP{$invoice->number}.xml"));
    }
   




 

    


}
