<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AlojamientoPedido;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
require_once __DIR__ . '/../../../vendor/autoload.php';
use MercadoPago;
use App\Http\Controllers\MailerController;

class WebhookController extends Controller
{
    public function handle(Request $request){
        MercadoPago\SDK::setAccessToken(config('services.mercadopago.token'));
        switch($request["type"]) {
            case "payment":
                $payment = MercadoPago\Payment::find_by_id($request["data"]["id"]);
                break;
            case "plan":
                $plan = MercadoPago\Plan::find_by_id($request["data"]["id"]);
                break;
            case "subscription":
                $plan = MercadoPago\Subscription::find_by_id($request["data"]["id"]);
                break;
            case "invoice":
                $plan = MercadoPago\Invoice::find_by_id($request["data"]["id"]);
                break;
            case "point_integration_wh":
                // $request contiene la informaciòn relacionada a la notificaciòn.
                break;
        }
        MailerController::test1($payment);
        if($payment->status == "approved"){
            $alojamientoPedido = AlojamientoPedido::where('numero_transaccion', $request["data"]["id"])->whereNull('estado_transaccion')->first();
            if (!$alojamientoPedido){
            $alojamientoPedido = AlojamientoPedido::where('numero_transaccion2', $request["data"]["id"])->whereNull('estado_transaccion2')->first();
            }
        }
        if(!$alojamientoPedido){
            return response("OK", 200);
        }
        if($alojamientoPedido->valor_total == $payment->transaction_amount){
            $alojamientoPedido->estado = 'PC';
            $alojamientoPedido->fecha_pago = date('Y-m-d H:i:s');
            $alojamientoPedido->numero_transaccion = strval($payment->id);
            $alojamientoPedido->estado_transaccion = $payment->status;
            $alojamientoPedido->save();
            //MAIL ADMIN
            MailerController::adminMailPC($alojamientoPedido, $payment);
            //MAIL INQUILINO
            MailerController::renterMailPC($alojamientoPedido, $payment);
            //MAIL A PROPIETARIO
            MailerController::ownerMailPC($alojamientoPedido, $payment);
        }elseif ($alojamientoPedido->estado == 'PP') {
            $alojamientoPedido->estado = 'PC';
            $alojamientoPedido->fecha_pago = date('Y-m-d H:i:s');
            $alojamientoPedido->numero_transaccion2 = strval($payment->id);
            $alojamientoPedido->estado_transaccion2 = $payment->status;
            $alojamientoPedido->save();
            //MAIL ADMIN
            MailerController::adminMailPC($alojamientoPedido, $payment);
            //MAIL INQUILINO
            MailerController::renterMailPC($alojamientoPedido, $payment);
            //MAIL A PROPIETARIO
            MailerController::ownerMailPC($alojamientoPedido, $payment);
        }elseif ($alojamientoPedido->estado == 'CO') {
            $alojamientoPedido->estado = 'PP';
            $alojamientoPedido->fecha_pago = date('Y-m-d H:i:s');
            $alojamientoPedido->numero_transaccion = strval($payment->id);
            $alojamientoPedido->estado_transaccion = $payment->status;
            $alojamientoPedido->save();
            //MAIL ADMIN
            MailerController::adminMailPP($alojamientoPedido, $payment);
            //MAIL INQUILINO
            MailerController::renterMailPP($alojamientoPedido, $payment);
            //MAIL PROPIETARIO
            MailerController::ownerMailPP($alojamientoPedido, $payment);
        }
        return response("OK", 200);
    }
}
