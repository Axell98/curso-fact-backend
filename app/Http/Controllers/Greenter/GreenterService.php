<?php

namespace App\Http\Controllers\Greenter;

use DateTime;
use Greenter\See;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\SaleDetail;
use Illuminate\Support\Facades\Storage;
use Greenter\Ws\Services\SunatEndpoints;


class GreenterService {

    public function getSee() {
        $see = new See();
        $see->setCertificate(Storage::get('certificate-demo.pem'));
        $see->setService(env("APP_ENV") == "local" ? SunatEndpoints::FE_BETA : SunatEndpoints::FE_PRODUCCION);
        $see->setClaveSOL(env("RUC"), env("USER_SOL"), env("USER_PASS"));
        return $see;
    }

    public function getCompany($company) {
        return (new Company())
            ->setRuc($company->n_document)
            ->setRazonSocial($company->razon_social)
            ->setNombreComercial($company->razon_social_comercial)
            ->setAddress($this->getAddress($company));
    }
    
    public function getAddress($company) {
        // Emisor
        return (new Address())
            ->setUbigueo($company->ubigeo_distrito)
            ->setDepartamento($company->region)
            ->setProvincia($company->provincia)
            ->setDistrito($company->distrito)
            ->setUrbanizacion($company->urbanizacion)
            ->setDireccion($company->address)
            ->setCodLocal($company->cod_local); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.
    }

    public function getClient($sale){
        $cod_document = 1;$client = $sale->client;
        switch ($client->type_document) {
            case 'RUC':
                $cod_document = 6;
                break;
            case 'CARNET DE EXTRANJERIA':
                $cod_document = 4;
                break;
            case 'PASAPORTE':
                $cod_document = 7;
                break;
        }
        return (new Client())
            ->setTipoDoc($cod_document)
            ->setNumDoc($client->n_document)
            ->setRznSocial($client->full_name);
    }

    public function getInvoice($data,$company,$sale) {
        
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion($data["tipo_operacion"]) // Venta - Catalog. 51
            ->setTipoDoc($data['tipo_doc']) // Factura - Catalog. 01 
            ->setSerie($data['serie'])
            ->setCorrelativo($data['correlativo'])
            ->setCompany($this->getCompany($company))
            ->setClient($this->getClient($sale))
            ->setTipoMoneda($data['tipo_moneda'])// Sol - Catalog. 02
            ->setFechaEmision(new DateTime());
            
         $invoice->setMtoBaseIvap($data['mto_base_ivap'] ?? null) // Base IVAP
            ->setMtoIvap($data['mto_ivap'] ?? null) // Suma IVAP

            //Impuestos
            ->setMtoIGV($data['mto_igv'])
            ->setMtoIGVGratuitas($data['mto_igv_gratuitas'])
            ->setIcbper($data['icbper'])
            ->setTotalImpuestos($data['total_impuestos'])

            //Totales
            ->setValorVenta($data['valor_venta'])
            ->setSubTotal($data['sub_total'])
            ->setRedondeo($data['redondeo'])
            ->setMtoImpVenta($data['mto_imp_venta'])


            ->setDetails($this->getDetails($sale->sale_details))
            ->setLegends($this->getLegends($data["legends"]));

        return $invoice;
    }

     public function getDetails($sale_details) {
        $greenter_sale_details = [];
        foreach ($sale_details as $sale_detail) {
            $mto_valor_unitario = 0;

            $porcentaje_igv = 18;

            $detail = (new SaleDetail())
                ->setCodProducto('P001')
                ->setUnidad($sale_detail->unidad_medida) // Unidad - Catalog. 03
                ->setDescripcion($sale_detail->product->title)
                ->setCantidad($sale_detail->quantity)
                ->setMtoValorUnitario($sale_detail->price_base)
                ->setMtoValorVenta($sale_detail->subtotal)
                ->setMtoBaseIgv($sale_detail->subtotal)
                ->setPorcentajeIgv($porcentaje_igv) // 18%
                ->setIgv($sale_detail->igv)
                ->setTotalImpuestos($sale_detail->igv) // Suma de impuestos en el detalle
                ->setTipAfeIgv($sale_detail->tip_afe_igv) // Gravado Op. Onerosa - Catalog. 07
                ->setMtoPrecioUnitario($sale_detail->price_final);
            array_push($greenter_sale_details,$detail);
        }
        return $greenter_sale_details;
    }

    public function getLegends($legends)
    {
        $green_legends = [];

        foreach ($legends as $legend) {
            $green_legends[] = (new Legend())
                ->setCode($legend['code'] ?? null) // Monto en letras - Catalog. 52
                ->setValue($legend['value'] ?? null);
        }

        return $green_legends;
    }
}