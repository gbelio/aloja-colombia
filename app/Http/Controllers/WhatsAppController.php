<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Exception;
use App\AlojamientoPedido;
use App\Alojamiento;
use Mail;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\AlojamientosPedidosController;

class WhatsAppController extends Controller
{
    static function sendWhatsapp(){
        //NUESTRO TELEFONO
        $telefono = '541166793520';
        //CONFIGURACION DEL MENSAJE
        $mensaje = '{
            "messaging_product": "whatsapp",
            "to": "'.$telefono.'",
            "type": "template",
            "template":{
                "name": "hello_world",
                "language":{ "code": "en_US" }
            }
        }';
        WhatsAppController::sendMessage($mensaje);
    }

    static function ownerMessagePendingAprobation($alojamiento, $alojamientoPedido){
        $telefono = $alojamiento->Propietario->celular();
        $name = $alojamiento->Propietario->name;
        $title = $alojamientoPedido->alojamiento->titulo;
        $code = $alojamiento->codigo_alojamiento;
        $at = $alojamientoPedido->alojamiento->ciudad;
        $from = MailerController::dateFormater($alojamientoPedido->fecha_desde);
        $to = MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $nigths = $alojamientoPedido->cantidad_noches;
        $price = $alojamientoPedido->valor_total;
        $idAlojamiento = $alojamiento->id;
        $idAlojamientoPedido = $alojamientoPedido->id;

        //CONFIGURACION DEL MENSAJE
        $mensaje = '{
            "messaging_product": "whatsapp", 
            "to": "'.$telefono.'", 
            "type": "template", 
            "template":{
                "name": "reserva_pendiente_aprobacion",
                "language":{ "code": "es_AR" },
                "components": [
                    {
                        "type": "header",
                        "parameters": 
                            [{
                                "type": "text",
                                "text":"'.$name.'"
                            }]
                    },
                    {
                        "type": "body",
                        "parameters": [
                            {
                                "type": "text",
                                "text":"'.$title.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$code.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$at.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$from.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$to.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$nigths.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$price.'"
                            },
                        ]
                    },
                    {
                        "type": "button",
                        "index": "0",
                        "sub_type": "url",
                        "parameters": [
                            {
                                "type": "text",
                                "text": "'.$idAlojamiento.'"
                            }
                        ]
                    },
                    {
                        "type": "button",
                        "sub_type": "quick_reply",
                        "index": 1,
                        "parameters": 
                            [{
                                "type": "payload",
                                "payload": "'.$idAlojamientoPedido.'"
                            }]
                    },
                    {
                        "type": "button",
                        "sub_type": "quick_reply",
                        "index": 2,
                        "parameters": 
                            [{
                                "type": "payload",
                                "payload": "'.$idAlojamientoPedido.'"
                            }]
                    }
                ],
            }
        }';
        WhatsAppController::sendMessage($mensaje);
    }

    static function renterMessageAccepted($alojamiento, $alojamientoPedido){
        $telefono = $alojamientoPedido->Huesped->celular;
        $name = $alojamientoPedido->Huesped->name;
        $title = $alojamientoPedido->alojamiento->titulo;
        $code = $alojamiento->codigo_alojamiento;
        $at = $alojamientoPedido->alojamiento->ciudad;
        $from = MailerController::dateFormater($alojamientoPedido->fecha_desde);
        $to = MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $nigths = $alojamientoPedido->cantidad_noches;
        $price = $alojamientoPedido->valor_total;

        //CONFIGURACION DEL MENSAJE
        $mensaje = '{
            "messaging_product": "whatsapp", 
            "to": "'.$telefono.'", 
            "type": "template", 
            "template":{
                "name": "reserva_pendiente_pago",
                "language":{ "code": "es_AR" },
                "components": [
                    {
                        "type": "header",
                        "parameters": 
                            [{
                                "type": "text",
                                "text":"'.$name.'"
                            }]
                    },
                    {
                        "type": "body",
                        "parameters": [
                            {
                                "type": "text",
                                "text":"'.$title.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$code.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$at.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$from.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$to.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$nigths.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$price.'"
                            },
                        ]
                    },
                ],
            }
        }';
        WhatsAppController::sendMessage($mensaje);
    }

    static function ownerDoubleResponse($alojamientoPedido){
        $telefono = $alojamientoPedido->alojamiento->Propietario->celular;
        //CONFIGURACION DEL MENSAJE
        $mensaje = '{
            "messaging_product": "whatsapp",
            "to": "'.$telefono.'",
            "type": "template",
            "template": {
                "name": "accion_segunda_respuesta_botones",
                "language": {
                    "code": "es_AR"
                }
            }
        }';
        WhatsAppController::sendMessage($mensaje);
    }

    static function messagePC($alojamiento, $alojamientoPedido){
        $telefonoHuesped = $alojamientoPedido->Huesped->celular;
        $nameHuesped = $alojamientoPedido->Huesped->name;
        $telefonoAlojador = $alojamientoPedido->alojamiento->Propietario->celular;
        $nameAlojador = $alojamiento->Propietario->name;
        $title = $alojamientoPedido->alojamiento->titulo;
        $code = $alojamiento->codigo_alojamiento;
        $at = $alojamientoPedido->alojamiento->ciudad;
        $from = MailerController::dateFormater($alojamientoPedido->fecha_desde);
        $to = MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $nigths = $alojamientoPedido->cantidad_noches;
        $price = $alojamientoPedido->valor_total;

        //CONFIGURACION DEL MENSAJE
        $mensajeHuesped = '{
            "messaging_product": "whatsapp", 
            "to": "'.$telefonoHuesped.'", 
            "type": "template", 
            "template":{
                "name": "pago_realizado",
                "language":{ "code": "es_AR" },
                "components": [
                    {
                        "type": "header",
                        "parameters": 
                            [{
                                "type": "text",
                                "text":"'.$nameHuesped.'"
                            }]
                    },
                    {
                        "type": "body",
                        "parameters": [
                            {
                                "type": "text",
                                "text":"'.$title.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$code.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$at.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$from.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$to.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$nigths.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$price.'"
                            },
                        ]
                    },
                ],
            }
        }';
        $mensajeAlojador = '{
            "messaging_product": "whatsapp", 
            "to": "'.$telefonoAlojador.'", 
            "type": "template", 
            "template":{
                "name": "pago_realizado",
                "language":{ "code": "es_AR" },
                "components": [
                    {
                        "type": "header",
                        "parameters": 
                            [{
                                "type": "text",
                                "text":"'.$nameAlojador.'"
                            }]
                    },
                    {
                        "type": "body",
                        "parameters": [
                            {
                                "type": "text",
                                "text":"'.$title.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$code.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$at.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$from.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$to.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$nigths.'"
                            },
                            {
                                "type": "text",
                                "text":"'.$price.'"
                            },
                        ]
                    },
                ],
            }
        }';
        sendMessage($mensajeHuesped);
        sendMessage($mensajeAlojador);
    }

    /*
    * RECEPCION DE MENSAJES
    */
    
    static function sendMessage($mensaje){
        $token = 'EAAVkJCDR1BUBO9SJkYtyZA0853BzVGHb0OEBPzqF1Nuoj3tA9uQNgHQOpURehYS8cZCNq6lbZAawwHF89pILNClZCZBzQ3HZBWY5i0LS1KEaud2BdbCV6qwLj9ADvLrpIUZChbB0NFud6E4MzHemF3w8cnm7pIrtZA8igo01ZCwcxsZBQoAACDZBQ2yWKOetZCc79x1D';
        $url = 'https://graph.facebook.com/v18.0/252614411271335/messages';
        //DECLARAMOS LAS CABECERAS
        $header = array("Authorization: Bearer " . $token, "Content-Type: application/json",);
        //INICIAMOS EL CURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $mensaje);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //OBTENEMOS LA RESPUESTA DEL ENVIO DE INFORMACION
        $response = json_decode(curl_exec($curl), true);
        //IMPRIMIMOS LA RESPUESTA 
        print_r($response);
        //OBTENEMOS EL CODIGO DE LA RESPUESTA
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //CERRAMOS EL CURL
        curl_close($curl);
    }

    public function recibe(){
        // LEEMOS LOS DATOS ENVIADOS POR WHATSAPP
        $respuesta = file_get_contents("php://input");
        // VERIFICAMOS SI $respuesta NO ESTÁ VACÍO Y ES UN STRING
        if (!empty($respuesta) && is_string($respuesta)) {
            // CONVERTIMOS EL JSON EN ARRAY
            $respuesta = json_decode($respuesta, true);
            // VERIFICAMOS SI LA ESTRUCTURA DEL ARRAY ES LA ESPERADA
            if (isset($respuesta['entry'][0]['changes'][0]['value']['messages'][0]['button']['text'])) {
                // EXTRAEMOS RESPUESTA
                $alojamientoPedidoStatus = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['button']['text'];
                $alojamientoPedidoId = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['button']['payload']; 
                // VERIFICAMOS SI $buttonText NO ESTÁ VACÍO
                if (!empty($alojamientoPedidoStatus)) {
                    //Se realiza transformación a Request Instance porque el método que recibe el dato espera eso
                    $alojamientoPedidoAsRequest = new Request([
                        'navegacion' => $alojamientoPedidoStatus
                    ]);
                    $alojamientoPedido = new AlojamientosPedidosController;
                    $alojamientoPedido->update($alojamientoPedidoAsRequest, $alojamientoPedidoId);
                }
            }
        }
        // RETORNAMOS UNA RESPUESTA HTTP 200 OK
        return response("OK", 200);
    }

    static function test($respuesta){
        $asunto = "Respuesta usuario";
        $titulo = "Respuesta usuario";
        $cuerpo = $respuesta;
        $message = Mail::to('gastonb.exe@gmail.com');
        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));
    }
    static function test1($respuesta){
        $asunto = "Respuesta status";
        $titulo = "Respuesta status";
        $cuerpo = $respuesta;
        $message = Mail::to('gastonb.exe@gmail.com');
        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));
    }
    public function webhook(){
        //TOQUEN QUE QUERRAMOS PONER 
        $token = 'WASAPE';
        //RETO QUE RECIBIREMOS DE FACEBOOK
        $hub_challenge = isset($_GET['hub_challenge']) ? $_GET['hub_challenge'] : '';
        //TOQUEN DE VERIFICACION QUE RECIBIREMOS DE FACEBOOK
        $hub_verify_token = isset($_GET['hub_verify_token']) ? $_GET['hub_verify_token'] : '';
        //SI EL TOKEN QUE GENERAMOS ES EL MISMO QUE NOS ENVIA FACEBOOK RETORNAMOS EL RETO PARA VALIDAR QUE SOMOS NOSOTROS
        if ($token === $hub_verify_token) {
            echo $hub_challenge;
            exit;
        }
    }
}
