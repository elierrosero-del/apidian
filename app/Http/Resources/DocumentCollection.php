<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Company;

class DocumentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            // Obtener el nombre de la empresa por identification_number
            $companyName = 'Sin Empresa';
            if ($row->identification_number) {
                $company = Company::where('identification_number', $row->identification_number)->first();
                if ($company && $company->user) {
                    $companyName = $company->user->name;
                }
            }
            
            // Determinar estado del documento
            // state_document_id: 1 = Procesado OK, 0 = Pendiente/Error
            $stateId = $row->state_document_id ?? 0;
            $stateName = $stateId == 1 ? 'Procesado' : 'Pendiente';
            $stateClass = $stateId == 1 ? 'success' : 'warning';
            
            // Verificar si tiene CUFE (indica que fue aceptado por DIAN)
            if ($row->cufe) {
                $stateName = 'Aceptado DIAN';
                $stateClass = 'success';
            }
            
            return [
                'key' => $key + 1,
                'id' => $row->id,
                'number' => $row->number,
                'prefix' => $row->prefix,
                'client' => $row->client->name ?? 'N/A',
                'currency' => $row->currency->name ?? 'N/A',
                'date' => $row->date_issue,
                'sale' => $row->sale,
                'total_discount' => $row->total_discount,
                'total_tax' => $row->total_tax,
                'subtotal' => $row->subtotal,
                'total' => $row->total,
                'xml' => $row->xml,
                'pdf' => $row->pdf,
                'url_xml' => '',
                'url_pdf' => '',
                'company_name' => $companyName,
                'identification_number' => $row->identification_number,
                'state_id' => $stateId,
                'state_name' => $stateName,
                'state_class' => $stateClass,
                'cufe' => $row->cufe,
                'type_document_id' => $row->type_document_id,
            ];
        });
    }
    
}
