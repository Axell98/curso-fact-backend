<?php

namespace App\Http\Controllers\Sale;

use App\Models\Sale\Sale;
use Illuminate\Http\Request;
use App\Models\Sale\SalePayment;
use App\Http\Controllers\Controller;

class SalePaymentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // sale_id
        // method_payment
        // amount
        // date_payment

        $sale_payment = SalePayment::create([
            "sale_id" => $request->sale_id,
            "method_payment" => $request->method_payment,
            "amount" => $request->amount,
            "date_payment" => $request->date_payment
        ]);

        $sale = Sale::find($request->sale_id);

        $sale->update([
            "debt" => $sale->debt - $sale_payment->amount,//MONTO ADEUDADO
            "paid_out" => $sale->paid_out + $sale_payment->amount,//MONTO PAGADO
            "type_payment" => $request->type_payment
        ]);

        $state_payment = 1;
        if($sale->debt == 0){
            $state_payment = 3;
        }
        $sale->update([
            "state_payment" => $state_payment,
        ]);
        return response()->json([
            "payment" => [
                "id" => $sale_payment->id,
                "sale_id" => $sale_payment->sale_id,
                "method_payment" => $sale_payment->method_payment,
                "amount" => $sale_payment->amount,
                "date_payment" => $sale_payment->date_payment
            ],
            "code" => 200,
            "message" => "El pago se ha registrado correctamente",
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sale_payment = SalePayment::find($id);
        $sale_payment->delete();

        $sale = Sale::findOrFail($sale_payment->sale_id);

        $sale->update([
            "debt" => $sale->debt + $sale_payment->amount,
            "paid_out" => $sale->paid_out - $sale_payment->amount,
        ]);

        $state_payment = 1;
        if($sale->paid_out == 0){
            $state_payment = 1;
        }else{
            $state_payment = 2;
        }
        
        $sale->update([
            "state_payment" => $state_payment,
        ]);

        return response()->json([
            "code" => 200,
            "message" => "El pago se ha eliminado",
        ]);
    }
}
