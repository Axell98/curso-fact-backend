<?php

namespace App\Http\Controllers\Greenter;

use DateTime;
use Greenter\See;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\Cuota;
use Greenter\Model\Sale\Charge;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\Detraction;
use Greenter\Model\Sale\Prepayment;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\SalePerception;
use Illuminate\Support\Facades\Storage;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;


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
            
        if($sale->type_payment == 2 && $sale->sale_payments->count() > 1){
            $invoice->setFormaPago(new FormaPagoCredito($sale->sale_payments->sum("amount")));
            $cuotas = [];
            foreach ($sale->sale_payments as $payment) {
                array_push($cuotas,(new Cuota())
                ->setMonto($payment->amount)
                ->setFechaPago(new DateTime($payment->date_payment)));
            }
            $invoice->setCuotas($cuotas);
        }else{
            $invoice->setFormaPago(new FormaPagoContado()); // FormaPago: Contado
        }

        $sales_anticipos = $sale->sales_anticipos ? json_decode($sale->sales_anticipos,true) : [];

        if(sizeof($sales_anticipos) > 0 && $sale->amount_anticipo){
            $set_descuentos = [];
            $set_anticipos = [];
            $total_anticipos = 0;
            foreach ($sales_anticipos as $key => $sales_anticip) {
                array_push($set_descuentos,(new Charge())
                    ->setCodTipo('04')
                    ->setFactor(1)
                    ->setMonto($sales_anticip["subtotal"]) // anticipo
                    ->setMontoBase($sales_anticip["subtotal"]));

                array_push($set_anticipos,(new Prepayment())
                    ->setTipoDocRel('02') // catalog. 12
                    ->setNroDocRel($sales_anticip["n_operacion"])
                    ->setTotal(round($sales_anticip["subtotal"],2)));
                $total_anticipos += round($sales_anticip["subtotal"],2);
            }
            $invoice->setDescuentos($set_descuentos)
                        ->setAnticipos($set_anticipos)
                        ->setTotalAnticipos($total_anticipos);
        }


        if($sale->retencion_igv > 0){
            if($sale->retencion_igv == 1){//RETENCION
                $invoice->setDescuentos([
                    (new Charge())
                        ->setCodTipo('62') // Catalog. 53
                        ->setMontoBase(($data['mto_imp_venta']))
                        ->setFactor(0.03) // 3%
                        ->setMonto(round(($data['mto_imp_venta'])*0.03,2))
                ]);
            }
            if($sale->retencion_igv == 2){//DETRACCIÓN
                $invoice->setDetraccion(
                // MONEDA SIEMPRE EN SOLES
                    (new Detraction())
                        // Carnes y despojos comestibles
                        ->setCodBienDetraccion('014') // catalog. 54
                        // Deposito en cuenta
                        ->setCodMedioPago('001') // catalog. 59
                        ->setCtaBanco(env("CTA_BANCO"))
                        ->setPercent(4.00)
                        ->setMount(round(($data['mto_imp_venta'])*0.04,2))
                );
            }
            if($sale->retencion_igv == 3){//PERCEPCION
                $invoice->setPerception((new SalePerception())
                ->setCodReg('51')
                ->setPorcentaje(0.04)
                ->setMtoBase(($data['mto_imp_venta']))
                ->setMto(round(($data['mto_imp_venta'])*0.04,2))
                ->setMtoTotal(round( ((($data['mto_imp_venta'])*0.04) + ($data['mto_imp_venta'])),2)));
            }
        }

        if($data['isc'] > 0) {
            $invoice->setMtoBaseIsc($data['mto_oper_isc']) // Sumatoria MtoBaseISC detalles
            ->setMtoISC($data['isc']);
        }

        if($sale->discount_global > 0){
            $invoice->setDescuentos([
                (new Charge())
                    ->setCodTipo('02') // Catalog. 53
                    ->setMontoBase($sale->discount_global)
                    ->setFactor(1)
                    ->setMonto($sale->discount_global)
            ]);
        }

        if($sale->is_exportacion == 1){
            $invoice->setMtoOperExportacion($data['mto_oper_exportacion'] ?? null);
        }else{
            $invoice->setMtoOperGravadas($data['mto_oper_gravadas'] ?? null)
                ->setMtoOperExoneradas($data['mto_oper_exoneradas'] ?? null)
                ->setMtoOperInafectas($data['mto_oper_inafectas'] ?? null)
                ->setMtoOperGratuitas($data['mto_oper_gratuitas'] ?? null);
        }

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
            $mto_valor_unitario = $sale_detail->price_base;
            $mto_valor_venta = $sale_detail->subtotal;
            $mto_base_igv = $sale_detail->subtotal + $sale_detail->isc;

            $porcentaje_igv = 18;

            if(!in_array($sale_detail->tip_afe_igv,[10,11])){
                $porcentaje_igv = 0;
            }
            if($sale_detail->tip_afe_igv == 11){
                $mto_valor_unitario = 0;
            }
            if($sale_detail->tip_afe_igv == 17){
                $porcentaje_igv = 4;
            }

            $igv = $sale_detail->igv;
            $total_impuesto = $igv + $sale_detail->icbper + $sale_detail->isc;
            $mto_precio_unitario = $sale_detail->price_final;
            error_log($igv." *** ".$total_impuesto);
            $detail = (new SaleDetail())
                ->setCodProducto('P001')
                ->setUnidad($sale_detail->unidad_medida) // Unidad - Catalog. 03
                ->setDescripcion($sale_detail->product->title)
                ->setCantidad($sale_detail->quantity)
                ->setMtoValorUnitario($mto_valor_unitario)
                ->setMtoValorVenta($mto_valor_venta)
                ->setMtoBaseIgv($mto_base_igv)
                ->setPorcentajeIgv($porcentaje_igv) // 18%
                ->setIgv($igv)
                ->setTotalImpuestos($total_impuesto) // Suma de impuestos en el detalle
                ->setTipAfeIgv($sale_detail->tip_afe_igv) // Gravado Op. Onerosa - Catalog. 07
                ->setMtoPrecioUnitario($mto_precio_unitario);

            if($sale_detail->tip_afe_igv == 11){
                $detail->setMtoValorGratuito($sale_detail->price_base);
            }

            if($sale_detail->icbper > 0){
                    $detail->setFactorIcbper($sale_detail->per_icbper) // 0.5%
                    ->setIcbper($sale_detail->icbper);
            }

            if($sale_detail->isc > 0){
                $detail->setMtoBaseIsc($mto_valor_venta)//200
                    ->setTipSisIsc('01') // Catalog 08: Sistema al Valor
                    ->setPorcentajeIsc($sale_detail->percentage_isc) // 17%
                    ->setIsc($sale_detail->isc); // 200 * 0.17 (17%)
            }

            if($sale_detail->discount > 0){
                $monto_base = round($sale_detail->quantity * $sale_detail->price_base,2);
                // error_log($monto_base);
                $detail->setDescuentos([
                    (new Charge())
                        ->setCodTipo('00') // Catalog. 53 (00: Descuento que afecta la Base Imponible)
                        ->setMontoBase($monto_base) // cantidad * valor unitario
                        ->setFactor(round($sale_detail->discount/$monto_base,6)) // 20% descuento
                        ->setMonto($sale_detail->discount)
                ]);
            }
            if($sale_detail->sale && $sale_detail->sale->is_exportacion == 1){
                $detail->setCodProdSunat($sale_detail->product->sku);
            }

            if($sale_detail->electronic_note && $sale_detail->electronic_note->is_exportacion == 1){
                $detail->setCodProdSunat($sale_detail->product->sku);
            }

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

    public function sunatResponse($result){
        $response = [];

        $response["success"] = $result->isSuccess();

        if(!$response["success"]){
            $response["error"] = [
                "code" => $result->getError()->getCode(),
                "message" => $result->getError()->getMessage(),
            ];
            return $response;
        }

        // Generar un nombre único para el archivo
        $file_name = "cdrs/".uniqid() . '.' . 'zip';
        Storage::disk('public')->put($file_name, $result->getCdrZip());
        // Obtener la ruta pública del archivo
        $public_path = Storage::url($file_name);
        $response['cdrZip'] = $public_path;//base64_encode($result->getCdrZip());

        $cdr = $result->getCdrResponse();

        $response['cdrResponse'] = [
            'code' => (int)$cdr->getCode(),
            'description' => $cdr->getDescription(),
            'notes' => $cdr->getNotes()
        ];

        return $response;
    }

    public function getNota($data,$company,$nota_electronic){
        $nota = (new Note())
                    ->setUblVersion('2.1')
                    ->setTipoDoc($data['tipo_doc']) // 07: Nota de Crédito / 08: Nota de Débito - Catalog. 01 
                    ->setSerie($data['serie'])
                    ->setCorrelativo($data['correlativo'])
                    ->setFechaEmision(new DateTime()) // Zona horaria: Lima

                    ->setTipDocAfectado($data['tipo_doc_afect']) // Tipo Doc: Factura
                    ->setNumDocfectado($data['num_doc_afect']) // Factura: Serie-Correlativo
                    ->setCodMotivo($data['cod_motivo']) // Catalogo. 09
                    ->setDesMotivo($data['des_motivo'])

                    ->setTipoMoneda($data['tipo_moneda']) // Sol - Catalog. 02
                    ->setCompany($this->getCompany($company))
                    ->setClient($this->getClient($nota_electronic));
                    
        if($data['isc'] > 0) {
            $nota->setMtoBaseIsc($data['mto_oper_isc']) // Sumatoria MtoBaseISC detalles
            ->setMtoISC($data['isc']);
        }

        if($nota_electronic->retencion_igv > 0){
            if($nota_electronic->retencion_igv == 3){//PERCEPCION
                $nota->setPerception((new SalePerception())
                ->setCodReg('51')
                ->setPorcentaje(0.04)
                ->setMtoBase($data['mto_imp_venta'])
                ->setMto(round($data['mto_imp_venta']*0.04,2))
                ->setMtoTotal(round( (($data['mto_imp_venta']*0.04) + $data['mto_imp_venta']),2)));
            }
        }

        //Mto Operaciones
        if($nota_electronic->is_exportacion == 1){
            $nota->setMtoOperExportacion($data['mto_oper_exportacion'] ?? null);
        }else{
            $nota->setMtoOperGravadas($data['mto_oper_gravadas'] ?? null)
                ->setMtoOperExoneradas($data['mto_oper_exoneradas'] ?? null)
                ->setMtoOperInafectas($data['mto_oper_inafectas'] ?? null)
                ->setMtoOperGratuitas($data['mto_oper_gratuitas'] ?? null);
        }

        $nota->setMtoBaseIvap($data['mto_base_ivap'] ?? null) // Base IVAP
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

        //Productos
        ->setDetails($this->getDetails($nota_electronic->details))

        //Leyendas
        ->setLegends($this->getLegends($data['legends']));

        return $nota;
    }
}