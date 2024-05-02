<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Http\Controllers\Exception;
require_once __DIR__ . '/../../../vendor/autoload.php';
use MercadoPago;
use App\AlojamientoPedido;
use App\Alojamiento;
use Mail;
use Auth;
use Carbon\Carbon;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\WhatsAppController;

use Illuminate\Support\Facades\Redirect;

MercadoPago\SDK::setAccessToken(config('services.mercadopago.token'));

class PaymentController extends Controller
{
    public function cardProcessPayment(Request $request, Response $response){
        try {
            $contents = json_decode(file_get_contents('php://input'), true);
            $parsed_request = $request->withParsedBody($contents);
            $parsed_body = $parsed_request->getParsedBody();
            $alojamientoPedido = AlojamientoPedido::find(
                $parsed_body['description']
            );
            $alojamiento = Alojamiento::find($alojamientoPedido->alojamiento_id);
            $payment = new MercadoPago\Payment();
            $payment->transaction_amount = (float)$parsed_body['transactionAmount'];
            $payment->token = $parsed_body['token'];
            $payment->description = "Aloja Colombia | " . $alojamiento->titulo . 
            ' | Código Propiedad: ' . $alojamiento->codigo_alojamiento . 
            ' | Código Reserva: ' . $alojamientoPedido->codigo_reserva;
            $payment->installments = 1;
            $payment->payment_method_id = $parsed_body['paymentMethodId'];
            /* $payment->external_reference = '123456789'; */
            $payment->issuer_id = $parsed_body['issuerId'];

            $payer = new MercadoPago\Payer();
            $payer->email = $parsed_body['payer']['email'];
            $payer->identification = [
                'type' => $parsed_body['payer']['identification']['type'],
                'number' => $parsed_body['payer']['identification']['number'],
            ];
            $payment->payer = $payer;
            $payment->save();

            $this->validate_payment_result($payment);

            $response_fields = [
                'id' => strval($payment->id),
                'status' => $payment->status,
                'detail' => $payment->status_detail,
            ];

            $response_body = json_encode($response_fields);
            $response->getBody()->write($response_body);

            //busca el alojamiento pedido y lo pone en Pago Parcial o Pago Completo
            if ( $payment->status == 'approved' &&
                $alojamientoPedido->valor_total == $payment->transaction_amount) {
                $alojamientoPedido->estado = 'PC';
                $alojamientoPedido->fecha_pago = date('Y-m-d H:i:s');
                $alojamientoPedido->numero_transaccion = strval($payment->id);
                $alojamientoPedido->save();
                //MAIL ADMIN
                MailerController::adminMailPC($alojamientoPedido, $payment);
                //MAIL A PROPIETARIO
                MailerController::ownerMailPC($alojamientoPedido, $payment);
                //MAIL INQUILINO
                MailerController::renterMailPC($alojamientoPedido, $payment);
                //WHATSAPP A PROPIETARIO
                WhatsAppController::messagePC($alojamiento, $alojamientoPedido);
                
            } elseif ( $payment->status == 'approved' && $payment->transaction_amount < $alojamientoPedido->valor_total && !($alojamientoPedido->estado == 'PP')){
                $alojamientoPedido->estado = 'PP';
                $alojamientoPedido->fecha_pago = date('Y-m-d H:i:s');
                $alojamientoPedido->numero_transaccion = strval($payment->id);
                $alojamientoPedido->save();
                //MAIL ADMIN
                MailerController::adminMailPP($alojamientoPedido, $payment);
                //MAIL INQUILINO
                MailerController::renterMailPP($alojamientoPedido, $payment);
                //MAIL A PROPIETARIO
                MailerController::ownerMailPP($alojamientoPedido, $payment);
            } elseif (
                $payment->status == 'approved' &&
                $alojamientoPedido->estado == 'PP'
            ) {
                $alojamientoPedido->estado = 'PC';
                $alojamientoPedido->fecha_pago = date('Y-m-d H:i:s');
                $alojamientoPedido->numero_transaccion2 = strval($payment->id);
                $alojamientoPedido->save();
                //MAIL ADMIN
                MailerController::adminMailPC($alojamientoPedido, $payment);
                //MAIL INQUILINO
                MailerController::renterMailPC($alojamientoPedido, $payment);
                //MAIL A PROPIETARIO
                MailerController::ownerMailPC($alojamientoPedido, $payment);
                //WHATSAPP A PROPIETARIO
                WhatsAppController::messagePC($alojamiento, $alojamientoPedido);
            }
            //busca el alojamiento pedido y lo pone en Pago Parcial o Pago Completo
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        } catch (Exception $exception) {
            $response_fields = ['error_message' => $exception->getMessage()];

            $response_body = json_encode($response_fields);
            $response->getBody()->write($response_body);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }

    function validate_payment_result($payment)
    {
        if ($payment->id === null) {
            $error_message = 'Unknown error cause';

            if ($payment->error !== null) {
                $sdk_error_message = $payment->error->message;
                $error_message =
                    $sdk_error_message !== null
                        ? $sdk_error_message
                        : $error_message;
            }

            throw new Exception($error_message);
        }
    }

    function cashProcessPayment(Request $request, Response $response)
    {
        $alojamientoPedido = AlojamientoPedido::find($request->getParsedBody()[
            'alojamientoPedidoId'
        ]);
        $alojamiento = Alojamiento::find($alojamientoPedido->alojamiento_id);

        MercadoPago\SDK::setAccessToken(config('services.mercadopago.token'));
        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = $request->getParsedBody()[
            'transactionAmount'
        ];
        $payment->description = $request->getParsedBody()['description'];
        $payment->payment_method_id = $request->getParsedBody()[
            'paymentMethod'
        ];
        $payment->payer = [
            'email' => $request->getParsedBody()['payerEmail']
        ];
        $payment->description = "Aloja Colombia | " . $alojamiento->titulo . 
            ' | Código Propiedad: ' . $alojamiento->codigo_alojamiento . 
            ' | Código Reserva: ' . $alojamientoPedido->codigo_reserva;
        $payment->date_of_expiration = Carbon::now("-05:00")->addDay()->format('Y-m-d\TH:m:s').'.000-05:00';
        $payment->save();
        
        $this->tieneAcceso($alojamiento, $alojamientoPedido);
        $puedeEditar = false;
        if ( Auth::user()->esAdministrador() || $alojamiento->propietario_id == \Auth::user()->id ) {
            $puedeEditar = true;
            if ($op = $request->input('op') == 2) {
                $puedeEditar = false;
            }
        }
        if(isset($payment->transaction_details->external_resource_url)){
            if ($alojamientoPedido->estado == 'CO' && $request->getParsedBody()['split-cash'] == 1) {
                $alojamientoPedido->numero_transaccion = strval($payment->id);
                $alojamientoPedido->save();
                //MAIL ADMIN
                MailerController::cashAdminMail($alojamientoPedido, $payment);
                //MAIL INQUILINO
                MailerController::cashRenterMail($alojamientoPedido, $payment);
                //MAIL PROPIETARIO
                MailerController::cashOwnerMail($alojamientoPedido, $payment);
            }elseif ($alojamientoPedido->estado == 'CO' && $request->getParsedBody()['split-cash'] == 2){
                //MAIL ADMIN
                MailerController::cashAdminMailPendientePP($alojamientoPedido, $payment);
                //MAIL INQUILINO
                MailerController::cashRenterMailPendientePP($alojamientoPedido, $payment);
                //MAIL PROPIETARIO
                MailerController::cashOwnerMailPendientePP($alojamientoPedido, $payment);
                $alojamientoPedido->numero_transaccion = strval($payment->id);
                $alojamientoPedido->save();
                $alojamientoPedido->valor_total_mitad = $alojamientoPedido->valor_total / 2;
            }elseif($alojamientoPedido->estado == 'PP'){
                //MAIL ADMIN
                MailerController::cashAdminMailPendientePC($alojamientoPedido, $payment);
                //MAIL INQUILINO
                MailerController::cashRenterMailPendientePC($alojamientoPedido, $payment);
                //MAIL PROPIETARIO
                MailerController::cashOwnerMailPendientePC($alojamientoPedido, $payment);
                $alojamientoPedido->numero_transaccion2 = strval($payment->id);
                $alojamientoPedido->save();
            }
            return view('alojamientosPedidos.cash')
                ->with('payment', $payment)
                ->with('puedeEditar', $puedeEditar)
                ->with('alojamientoPedido', $alojamientoPedido)
                ->with('descuentoDescripcion', app('\App\Http\Controllers\AlojamientosController')
                    ->descuentoFormateado( $alojamientoPedido->por_descuento,
                        $alojamientoPedido->tipo_descuento )
                );
        }
        return view('alojamientosPedidos.error');
    }

    public function tieneAcceso($alojamiento, $alojamientoPedido){
        if (!Auth::user()->esAdministrador()) {
            if (
                $alojamiento->propietario_id != \Auth::user()->id &&
                $alojamientoPedido->huesped_id != \Auth::user()->id
            ) {
                abort('403');
            }
        }
    }
}
