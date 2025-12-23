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
            
            return [
                'key' => $key + 1,
                'id' => $row->id,
                'number' => $row->number,
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
            ];
        });
    }
    
}
