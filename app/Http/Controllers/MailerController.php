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

class MailerController extends Controller
{
    /* static $MAIL_RESERVAS = "reservas@alojacolombia.com";
    static $MAIL_PAGOS = "pagosrecibidos@alojacolombia.com"; */
    static $MAIL_RESERVAS = "gastonb.exe@gmail.com";
    static $MAIL_PAGOS = "gastonb.exe@gmail.com";
    /////////////////////////////////////////////////////////////////////

    // MAIL A PROPIETARIO RECORDANDO A LOS 30 DÍAS QUE ACTIVE SU PROPIEDAD. SE ENVÍA UNA SOLA VEZ.
    static function ownerMailActivate($alojamientoIncompleto){
        $asuntoA = 'Falta activar la propiedad';
        $tituloA = '¡Activá tu propiedad!';
        $cuerpoA = '<p><br>Hola ' . $alojamientoIncompleto->Propietario->name . ',</p>
            <div style="text-align-last: center;"><h2>' . $alojamientoIncompleto->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoIncompleto->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p>Gracias por ser parte de Aloja Colombia, la nueva plataforma de alquileres por días en Colombia.</p>
            <p>Descubre alojamientos y experiencias únicas en toda Colombia.</p>
            <p>
                Queremos darte nuevamente la bienvenida e invitarte a que actives tu propiedad,
                recuerda que publicarla en Aloja Colombia no tiene ningún costo.
            </p>
            <p><a href="https://alojacolombia.com/alojamientos/' . $alojamientoIncompleto->id . '/edit?paso=11">Activá tu propiedad haciendo click aquí</a></p>
            <p>Anímate y comienza a ofrecer tú propiedad, y comienza a generar muchos ingresos con su alquiler.</p>
            <p>Cualquier duda o consulta, estamos para colaborarte.</p>
            <p>Saludos,</p>
            <br>
            <p>Equipo Aloja Colombia,</p><br><br>';
        $message = Mail::to($alojamientoIncompleto->Propietario->email);
        $message->send(new \App\Mail\MailGenerico($asuntoA, $tituloA, $cuerpoA));
    }

    // MAIL A PROPIETARIO RECORDANDO A LOS 15 DÍAS QUE COMPLETE LA CARGA. SE ENVÍA DOS VECES.
    static function ownerMailFU($alojamientoIncompleto){
        $fotoAlojamiento = "";
        if(isset($alojamientoIncompleto->fotoAlojamiento[0]->archivo)){
            $fotoAlojamiento = '<img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoIncompleto->fotoAlojamiento[0]->archivo. '">';
        }
        $asuntoA = 'Todavía no completaste la carga de tu alojamiento';
        $tituloA = '¡Completá tu alojamiento!';
        $cuerpoA = '<p><br>Hola ' . $alojamientoIncompleto->Propietario->name . ',</p>
            <div style="text-align-last: center;"><h2>' . $alojamientoIncompleto->titulo . '</h2>' . 
                $fotoAlojamiento . '
            </div>
            <br>
            <p>Gracias por ser parte de Aloja Colombia, la nueva plataforma de alquileres por días en Colombia.</p>
            <p>Descubre alojamientos y experiencias únicas en toda Colombia.</p>
            <p>
                Queremos darte nuevamente la bienvenida e invitarte a que termines de cargar los
                datos tu propiedad para que puedas activarla, recuerda que publicarla en Aloja
                Colombia no tiene ningún costo.
            </p>
            <p><a href="https://alojacolombia.com/alojamientos/' . $alojamientoIncompleto->id . '/edit?paso=11">Terminá la carga haciendo click aquí</a></p>
            <p>Anímate y comienza a ofrecer tú propiedad, y comienza a generar muchos ingresos con su alquiler.</p>
            <p>Cualquier duda o consulta, estamos para colaborarte.</p>
            <p>Saludos,</p>
            <br>
            <p>Equipo Aloja Colombia,</p><br><br>';
        $message = Mail::to($alojamientoIncompleto->Propietario->email);
        $message->send(new \App\Mail\MailGenerico($asuntoA, $tituloA, $cuerpoA));
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL DE BIENVENIDA A USUARIO
    static function userMailWelcome($user){
        $asunto = 'Hola ' . $user->name . ', ¡Bienvenido a Aloja Colombia!';
        $titulo = 'Bienvenido a Aloja Colombia';
        $cuerpo = '<p><br>Hola ' . $user->name . ', ¡Te damos la bienvenida a Aloja Colombia, tu cuenta ha sido creada con éxito!</p>
            <br>
            <p>Descubre alojamientos y experiencias únicas en toda Colombia.</p>
            <br>
            <p>Aloja Colombia es una comunidad en donde encontraras propiedades para alquilar por días, 
            en la que todos pueden pertenecer, por eso debes tratar a todos sus miembros con respeto e 
            igualdad, sin importar raza, religión, nacionalidad, etnia, color de piel, orientación sexual, 
            identidad de género, edad o personas con movilidad reducida.</p>
            <br>
            <p>¡Gracias por ser parte de nuestra comunidad!</p>
            <br>
            <p>Saludos,</p>
            <br>
            <p>Equipo Aloja Colombia,</p><br><br>';
        $message = Mail::to($user->email);
        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL A ADMINISTRADOR PAGO COMPLETO
    static function adminMailPC($alojamientoPedido, $payment){
        $asuntoAdmin = 'Reserva confirmada para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloAdmin = '¡Reserva confirmada!';
        $cuerpoAdmin ='<p>Hola Equipo Aloja, hay una reserva confirmada.</p>' .
            $secondPay = MailerController::secondPay($alojamientoPedido) . '
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>' .
            $contacto = MailerController::usersInfo($alojamientoPedido) . '
            <p><b>Datos bancarios</b></p>
            <p><b>Banco:</b> ' . $alojamientoPedido->alojamiento->cuenta_banco . '</p>
            <p><b>Nro de cuenta:</b> '. $alojamientoPedido->alojamiento->cuenta_nro . '</p>
            <p><b>Tipo de cuenta:</b> ' . $alojamientoPedido->alojamiento->cuenta_tipo . '</p>
            <hr/>
            <p><b>Detalle de la reserva:<b></p>
            <p><b>Huespedes:</b> ' . $alojamientoPedido->huespedes . '</p>
            <p><b>Reserva:</b> ' . MailerController::dateFormater($alojamientoPedido->fecha_pedido) . '</p>
            <p><b>Llegada:</b> ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</p>
            <p><b>Salida:</b> ' . MailerController::dateFormater($alojamientoPedido->fecha_hasta) . '</p>
            <p><b>Número de transacción MercadoPago:</b> ' . $payment->id . '</p>
            <br>
            <p><b>Fecha a depositar:</b> ' . MailerController::dateFormater(
                Carbon::createFromFormat(
                    'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                )->addDay()->toDateString()
            ) . '</p>'
            .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'ADMINISTRADOR',
                        true
                    )
                )
            . '
            <p><b>Política de cancelación</b></p>' . '
            <p>' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido). '</p>
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf">términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to(MailerController::$MAIL_PAGOS);
        $message->send(
        new \App\Mail\MailGenerico(
            $asuntoAdmin, $tituloAdmin, $cuerpoAdmin
        ));
    }
    //MAIL A INQUILINO PAGO COMPLETO
    static function renterMailPC($alojamientoPedido, $payment){
        $asuntoHuesped = 'Tu reserva ha sido confirmada para '
        . $alojamientoPedido->alojamiento->titulo . ' en '
        . $alojamientoPedido->alojamiento->ciudad . ' del '
        . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
        . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloHuesped = 'Su pago ha sido exitoso';
        $cuerpoHuesped ='<p>Hola ' . $alojamientoPedido->Huesped->name . ',</p>
            <p>¡Felicitaciones, el pago de tu reserva ha sido exitoso!</p>' .
            $secondPay = MailerController::secondPay($alojamientoPedido) . '
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p>Tu Alojador te estará esperando el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p><b>Datos de tú Alojador:</b></p>
            <p><b>Nombre:</b> ' . $alojamientoPedido->Alojamiento->Propietario->name . '</p>
            <p><b>Celular: </b>' . $alojamientoPedido->Alojamiento->Propietario->celular . '</p>
            <p><b>Cómo llegar:</b> '.$alojamientoPedido->Alojamiento->direccion.',
                '.$alojamientoPedido->Alojamiento->barrio.','.$alojamientoPedido->Alojamiento->ciudad.',
                '.$alojamientoPedido->Alojamiento->municipio.','.$alojamientoPedido->Alojamiento->departamento.',
                '.$alojamientoPedido->Alojamiento->mapa_locacion.'
            </p>
            <p>Comunícate con tu Alojador para confirmar los detalles de tu llegada.</p>
            <p><b>Número de transacción:</b> ' . $payment->id . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    )
                ).
                $valorDeposito = MailerController::valorDeposito($alojamientoPedido) .
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido). '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p><hr/>
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes en las 
                que debes mantener el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles.</p>
            <p>¡Que disfrutes tu estadía!</p>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            </p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoHuesped, $tituloHuesped, $cuerpoHuesped)
        );
    }
    //MAIL A PROPIETARIO PAGO COMPLETO
    static function ownerMailPC($alojamientoPedido, $payment){
        $asuntoPropietario = '¡Felicitaciones! has alquilado tu propiedad - '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloPropietario = '¡Felicitaciones! has alquilado tu propiedad';
        $cuerpoPropietario = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->name .' felicitaciones,</p>' .
            $secondPay = MailerController::secondPay($alojamientoPedido) . '
            <p>Tienes una nueva reserva confirmada de ' . $alojamientoPedido->Huesped->name . '</p>
            <p>Llega el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p><b>Número de transacción:</b> ' . $payment->id .'</p>
            <p><b>Datos del alojado:</b> ' . $alojamientoPedido->Huesped->name . ' ' . $alojamientoPedido->Huesped->apellido . '</p>
            <p><b>Celular:</b> ' . $alojamientoPedido->Huesped->celular . '</p>
            <p><b>Información de la reserva</b></p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        true
                    ) 
                ) . 
            '
            <p><b>Liquidación</b></p>
            <p>Te enviaremos el dinero que ganes como Alojador 24 horas después de la llegada de tu huésped. 
                El pago se te hará por medio de una transferencia bancaria a la cuenta que registraste pasadas 24 horas 
                de la llegada de los huéspedes. (Este tiempo lo reservamos para que los huéspedes nos indiquen que 
                todo está de acuerdo a lo publicado en tu anuncio).
            </p>
            <hr/>
            <p><b>Depósito Reembolsable</b></p>
            <p>Si colocaste la opción de Depósito Reembolsable debes tener en cuenta que debe haber una persona que 
                reciba a los huéspedes a su llegada, realice un inventario y reciba el depósito. Al finalizar su estadía deberá 
                revisar que no se presenten daños o faltantes en la propiedad para que este sea reembolsado.
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>' .
            $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <p>En caso de tener que cancelar esta reserva, van a continuar bloqueadas las noches en tu calendario, 
                en caso de que sean repetitivas las cancelaciones de tus reservas, cuenta y usuario podría ser dada de baja.
            </p>
            <hr/>
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda tener todo limpio y desinfectado, espacios ventilados y proporciónales a tus huéspedes alcohol 
                en gel a la llegada y salida de tu Alojamiento.
            </p><hr/>
            <p><b>Cómo llegar a tu Alojamiento</b></p>
            <p>Asegúrate que tu huésped tenga las indicaciones necesarias para llegar tu Alojamiento.</p>
            <hr/>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoPropietario, $tituloPropietario, $cuerpoPropietario)
        );
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL ADMIN PAGO PARCIAL
    static function adminMailPP($alojamientoPedido, $payment){
        $asuntoAdmin = 'Reserva parcial confirmada para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloAdmin = '¡Reserva parcial confirmada!';
        $cuerpoAdmin ='<p>Hola <b>Equipo Aloja Colombia,</b></p>
            <p>Hay una reserva dividida en 2 pagos:</p>
            <p><b>Total de la reserva:</b> '.$alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total
            ) .'</p>
            <p><b>Primer pago aprobado:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número de transacción MercadoPago:</b> ' . $payment->id . '</p>
            <br>
            <p><b>Segundo pago:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2) .' lo debe realizar antes del <b>'
                . MailerController::dateFormater(Carbon::createFromFormat('Y-m-d H:i:s', $alojamientoPedido->fecha_desde)->subDays(15)->toDateString().' 00:00:00').'</b>.
            </p>' . 
            $contacto = MailerController::usersInfo($alojamientoPedido) . '
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <hr/>
            <p><b>Datos bancarios</b></p>
            <p><b>Nombre:</b> ' . $alojamientoPedido->Alojamiento->Propietario->name . ', ' . $alojamientoPedido->Alojamiento->Propietario->apellido . '</p>
            <p><b>Banco:</b> ' . $alojamientoPedido->alojamiento->cuenta_banco . '</p>
            <p><b>Nro de cuenta:</b> '. $alojamientoPedido->alojamiento->cuenta_nro . '</p>
            <p><b>Tipo de cuenta:</b> ' . $alojamientoPedido->alojamiento->cuenta_tipo . '</p>
            <hr/>
            <p><b>Detalle de la reserva:<b></p>
            <p><b>Huespedes:</b> ' . $alojamientoPedido->huespedes . '</p>
            <p><b>Reserva:</b> ' . MailerController::dateFormater($alojamientoPedido->fecha_pedido) . '</p>
            <p><b>Llegada:</b> ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</p>
            <p><b>Salida:</b> ' . MailerController::dateFormater($alojamientoPedido->fecha_hasta) . '</p>
            <p><b>Fecha a depositar:</b> ' 
                . MailerController::dateFormater(
                    Carbon::createFromFormat(
                        'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                    )->addDay()->toDateString()
                ) . '
            </p>
            <br>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'ADMINISTRADOR',
                        true
                    )
                ) .
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido). '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to(MailerController::$MAIL_PAGOS);
        $message->send(
            new \App\Mail\MailGenerico($asuntoAdmin, $tituloAdmin, $cuerpoAdmin)
        );
    }

    //MAIL INQUILINO PAGO PARCIAL
    static function renterMailPP($alojamientoPedido, $payment){
        $asuntoHuesped = 'Tu reserva ha sido confirmada para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloHuesped = 'Su pago parcial ha sido exitoso';
        $cuerpoHuesped = '<p>Hola ' . $alojamientoPedido->Huesped->name .',</p>
            <p>¡Felicitaciones, el pago parcial de tu reserva ha sido exitoso!</p>
            <p><b>Número de transacción:</b> ' . $payment->id . '</p>
            <br>
            <p><b>Elegiste efectuar tu reserva en 2 pagos:</b></p>
            <p><b>Total de la reserva:</b> '.$alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total
            ) .'</p>
            <p><b>Primer pago aprobado:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Segundo pago:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda( $alojamientoPedido->valor_total / 2) .
                ' lo debes realizar antes del <b>'. MailerController::dateFormater(
                    Carbon::createFromFormat(
                        'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                    )->subDays(15)->toDateString().' 00:00:00'
                ) .'</b>.
            </p>
            <ul style="text-align: left; padding-left: 0px;" style="text-align: left; padding-left: 0px;">
                <li>Se te enviara un correo 24 horas antes de la fecha límite que debes realizar el pago.</li>
                <li>Si el pago no es efectuado pasadas 24hs de los tiempos establecidos, la reserva quedara 
                    Anulada y se aplicaran las respectivas políticas de cancelación de cada Alojamiento.
                </li>
                <li>Una vez realizado la totalidad del pago, se te enviara toda la información de tu Alojador 
                    y la ubicación exacta de tu Alojamiento.
                </li>
            </ul>
            <p><a href="https://alojacolombia.com/alojamientosPedidos/' . $alojamientoPedido->id . '/edit?op=2"><b>Hacer el pago</b></a></p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p>Tu Alojador te estará esperando el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p><b>Datos de tú Alojador:</b></p>
            <p><b>Nombre:</b> ' . $alojamientoPedido->Alojamiento->Propietario->name . '</p>
            <p><b>Celular:</b> ' . $alojamientoPedido->Alojamiento->Propietario->celular . '</p>
            <p><b>Cómo llegar:</b> '.$alojamientoPedido->Alojamiento->direccion.',
                '.$alojamientoPedido->Alojamiento->barrio.','.$alojamientoPedido->Alojamiento->ciudad.',
                '.$alojamientoPedido->Alojamiento->municipio.','.$alojamientoPedido->Alojamiento->departamento.',
                '.$alojamientoPedido->Alojamiento->mapa_locacion.'
            </p>
            <p>Comunícate con tu Alojador para confirmar los detalles de tu llegada.</p>
            <br>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    )
                ) . 
                $valorDeposito = MailerController::valorDeposito($alojamientoPedido) .
            '
            <p><b>Política de cancelación</b></p>' .
            $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido). '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes en las que debes mantener 
                el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles.</p>
            <p>¡Que disfrutes tu estadía!</p>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoHuesped, $tituloHuesped, $cuerpoHuesped)
        );
    }

    //MAIL PROPIETARIO PAGO PARCIAL
    static function ownerMailPP($alojamientoPedido, $payment){
        $asuntoPropietario = '¡Felicitaciones! has alquilado tu propiedad - '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloPropietario = '¡Felicitaciones! has alquilado tu propiedad';
        $cuerpoPropietario = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->name . ' felicitaciones,</p>
            <p>Tienes una nueva reserva confirmada de ' . $alojamientoPedido->Huesped->name . '</p>
            <p>Llega el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p><b>Número de transacción:</b> ' . $payment->id . '</p>
            <ul style="text-align: left; padding-left: 0px;">
                <li>' . $alojamientoPedido->Huesped->name . ' eligió la modalidad de pagar la reserva en 2 pagos, uno en el 
                    momento de confirmar la reserva y el otro pago 15 días antes de la llegada a tu Alojamiento.
                </li>
                <li>Una vez sea realizado el segundo pago, te proporcionaremos toda la información de contacto de tu Huésped.</li>
            </ul>
            <p><b>Información de la reserva</b></p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        true
                    ) 
                ) . 
            '
            <p><b>Liquidación</b></p>
            <p>Te enviaremos el dinero que ganes como Alojador 24 horas después de la llegada de tu huésped. 
                El pago se te hará por medio de una transferencia bancaria a la cuenta que registraste pasadas 24 horas 
                de la llegada de los huéspedes. (Este tiempo lo reservamos para que los huéspedes nos indiquen que todo 
                está de acuerdo a lo publicado en tu anuncio).
            </p>
            <hr/>
            <p><b>Depósito Reembolsable</b></p>
            <p>Si colocaste la opción de Depósito Reembolsable debes tener en cuenta que debe haber una persona que 
                reciba a los huéspedes a su llegada, realice un inventario y reciba el depósito. Al finalizar su estadía deberá 
                revisar que no se presenten daños o faltantes en la propiedad para que este sea reembolsado.
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido). '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <p>En caso de tener que cancelar esta reserva, van a continuar bloqueadas las noches en tu calendario, 
                en caso de que sean repetitivas las cancelaciones de tus reservas, cuenta y usuario podría ser dada de baja.
            </p>
            <hr/>
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda tener todo limpio y desinfectado, espacios ventilados y proporciónales a tus huéspedes alcohol en gel a la llegada y salida de tu Alojamiento.</p>
            <hr/>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoPropietario, $tituloPropietario, $cuerpoPropietario)
        );
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL A INQUILINO PENDIENTE DE PAGO (1 PAGO)
    static function cashRenterMail($alojamientoPedido, $payment){
        $asuntoHuesped = 'Pendiente pago para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloHuesped = 'Tienes 24 para realizar el pago';
        $cuerpoHuesped = '<p>Hola ' . $alojamientoPedido->Huesped->name . ',
            <br>
            <p>¡Felicitaciones, tienes 24 para realizar el pago!</p>
            <br>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p><b>Número de transacción:</b> ' . $payment->id . '</p>
            <br>
            <b><a style="color: #ef251b;border: 1px solid;text-decoration: none;display: block;text-align: center;border-color: #ef251b;border-radius: 12px;padding: 10px;" href="' . $payment->transaction_details->external_resource_url . '" target="_blank" rel="noopener noreferrer" class="btn btn-info">LINK DE PAGO</a></b>
            <br>
            <br>
            <p>Tu Alojador te estará esperando el<b> ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p>Si no realizas el pago en <b>24</b>, tu reserva será <b>cancelada.</b></p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    ) 
                ) . 
            '
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes en las que debes mantener 
                el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles. ¡Que disfrutes tu estadía! Si tienes alguna 
                pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia</p>
            <style type="text/css">
                a:hover {
                    background-color: #edf2f7;
                    text-decoration: none;
                    transition: 1s;
                }
            </style>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoHuesped, $tituloHuesped, $cuerpoHuesped)
        );
    }

    //MAIL PROPIETARIO PENDIENTE DE PAGO (1 PAGO)
    static function cashOwnerMail($alojamientoPedido, $payment){
        $asuntoPropietario = 'Pendiente pago para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloPropietario = '¡Felicitaciones estás a solo un paso!';
        $cuerpoPropietario = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->name . ',
            <br>
            <p>¡Felicitaciones, ' . $alojamientoPedido->Huesped->name . ' tiene 24 para realizar el pago!</p>
            <br>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>La fecha reservada es el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p><b>Fecha a depositar:</b> ' 
                . MailerController::dateFormater(
                    Carbon::createFromFormat(
                        'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                    )->addDay()->toDateString()
                ) . '
            </p>
            <p>Si no realiza el pago en 24hs, la reserva será <b>cancelada.</b></p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        true
                    ) 
                ) . 
            '
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, 
                hay áreas comunes en las que debes mantener el distanciamiento físico y 
                usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles. 
                ¡Que disfrutes tu estadía! Si tienes alguna pregunta o duda comunícate con 
                nosotros al centro de ayuda ayuda@alojacolombia.com
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido). '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoPropietario, $tituloPropietario, $cuerpoPropietario)
        );
    }

    //MAIL ADMIN PENDIENTE DE PAGO (1 PAGO)
    static function cashAdminMail($alojamientoPedido, $payment){
        $asuntoAdmin = 'Pendiente pago para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta) . ' - 24hs';
        $tituloAdmin = '¡Reserva pendiente de pago - 24hs!';
        $cuerpoAdmin = '<p>Hola equipo Aloja,<br>' . $alojamientoPedido->Huesped->name . ' 
            hizo una reserva de pago en efectivo. Tiene 24hs para hacer el pago o la misma será cancelada.</p>
            <br>' .
            $contacto = MailerController::usersInfo($alojamientoPedido) . '
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>La fecha reservada es el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p>Si no realiza el pago en 24hs, la reserva será cancelada.</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'ADMINISTRADOR',
                        true
                    ) 
                ) . 
            '
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes en las que 
            debes mantener el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.</p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles. 
                ¡Que disfrutes tu estadía! Si tienes alguna pregunta o duda comunícate con nosotros al 
                centro de ayuda ayuda@alojacolombia.com
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia</p>';
        $message = Mail::to(MailerController::$MAIL_RESERVAS);
        $message->send(
            new \App\Mail\MailGenerico($asuntoAdmin, $tituloAdmin, $cuerpoAdmin));
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL A INQUILINO PENDIENTE DE PAGO (2 PAGOS)
    static function cashRenterMailPendientePP($alojamientoPedido, $payment){
        $asuntoHuesped = 'Pendiente pago para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloHuesped = 'Tienes 24hs para realizar el pago';
        $cuerpoHuesped = '<p>Hola ' . $alojamientoPedido->Huesped->name . ', <br>
            ¡Felicitaciones, tienes 24hs para realizar el pago!</p>
            <br>
            <p><b>Elegiste efectuar tu reserva en 2 pagos:</b></p>
            <br>
            <p><b>Total de la reserva:</b> '.$alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total
            ) .'</p>
            <p><b>Primer pago:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número de transacción:</b> ' . $payment->id . '</p>
            <br>
            <b><a style="color: #ef251b;border: 1px solid;text-decoration: none;display: block;text-align: center;border-color: #ef251b;border-radius: 12px;padding: 10px;" href="' . $payment->transaction_details->external_resource_url . '" target="_blank" rel="noopener noreferrer" class="btn btn-info">LINK DE PAGO</a></b>
            <br>
            <br>
            <p><b>Segundo pago:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda( $alojamientoPedido->valor_total / 2) . ' lo debes realizar antes del <b>'
                . MailerController::dateFormater(
                    Carbon::createFromFormat(
                        'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                    )->subDays(15)->toDateString().' 00:00:00'
                ) .'
            </b>.
            <ul style="text-align: left; padding-left: 0px;">
                <li>Se te enviara un correo 24hs antes de la fecha límite que debes realizar el pago.</li>
                <li>Si el pago no es efectuado pasadas 24hs de los tiempos establecidos, la reserva quedara 
                    Anulada y se aplicaran las respectivas políticas de cancelación de cada Alojamiento.
                </li>
                <li>Una vez realizado la totalidad del pago, se te enviara toda la información de tu Alojador y la ubicación exacta de tu Alojamiento.</li>
            </ul>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>Tu Alojador te estará esperando el<b> ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p>Si no realizas el pago en <b>24hs</b>, tu reserva será <b>cancelada.</b></p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    ) 
                ) . 
            '
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes 
                en las que debes mantener el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles. 
                ¡Que disfrutes tu estadía! Si tienes alguna pregunta o duda comunícate 
                con nosotros al centro de ayuda ayuda@alojacolombia.com
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido). '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia</p>
            <style type="text/css">
                a:hover {
                    background-color: #edf2f7;
                    text-decoration: none;
                    transition: 1s;
                }
            </style>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoHuesped, $tituloHuesped, $cuerpoHuesped)
        );
    }

    //MAIL A PROPIETARIO PENDIENTE DE PAGO (2 PAGOS)
    static function cashOwnerMailPendientePP($alojamientoPedido, $payment){
        $asuntoPropietario = 'Pendiente pago para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloPropietario = '¡Felicitaciones estás a solo un paso!';
        $cuerpoPropietario = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->name . ',
            <br>
            <p>¡Felicitaciones, ' . $alojamientoPedido->Huesped->name . ' tiene 24hs para realizar el pago!</p>
            <br>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>La fecha reservada es el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p>Si no realiza el pago en 24hs, la reserva será <b>cancelada.</b></p>
            <br>
            <p><b>Fecha a depositar:</b> ' 
                . MailerController::dateFormater(
                    Carbon::createFromFormat(
                        'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                    )->addDay()->toDateString()
                ) . '
            </p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        true
                    ) 
                ) . 
            '
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, 
                hay áreas comunes en las que debes mantener el distanciamiento físico y 
                usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles. 
                ¡Que disfrutes tu estadía! Si tienes alguna pregunta o duda comunícate con 
                nosotros al centro de ayuda ayuda@alojacolombia.com
            </p>
            <hr/>
            <p><b>Política de cancelación</b><br>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido). '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoPropietario, $tituloPropietario, $cuerpoPropietario)
        );
    }

    //MAIL A ADMIN PENDIENTE DE PAGO (2 PAGOS)
    static function cashAdminMailPendientePP($alojamientoPedido, $payment){
        $asuntoAdmin = 'Pendiente pago para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta) . ' - 24hs';
        $tituloAdmin = '¡Reserva pendiente de pago - 24hs!';
        $cuerpoAdmin = '<p>Hola equipo Aloja,<br>'
            . $alojamientoPedido->Huesped->name . ' hizo una reserva de pago en efectivo. 
            Tiene 24hs para hacer el pago o la misma será cancelada.</p>
            <br>
            <p>Hay una reserva dividida en 2 pagos:</p>
            <p><b>Total de la reserva:</b> '.$alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total
            ) .'</p>
            <b>Primer pago:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número de transacción MercadoPago:</b> ' . $payment->id . '</p>
            <br>
            <p><b>Segundo pago:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2) .' lo debe realizar antes del <b>'
                . MailerController::dateFormater(
                    Carbon::createFromFormat(
                        'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                    )->subDays(15)->toDateString().' 00:00:00'
                ).'</b>.
            </p>' .
            $contacto = MailerController::usersInfo($alojamientoPedido) . '
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>La fecha reservada es el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p>Si no realiza el pago en 24hs, la reserva será cancelada.</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'ADMINISTRADOR',
                        true
                    ) 
                ) . 
            '
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes 
                en las que debes mantener el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles. 
                ¡Que disfrutes tu estadía! Si tienes alguna pregunta o duda comunícate 
                con nosotros al centro de ayuda ayuda@alojacolombia.com
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia</p>';
        $message = Mail::to(MailerController::$MAIL_RESERVAS);
        $message->send(
            new \App\Mail\MailGenerico($asuntoAdmin, $tituloAdmin, $cuerpoAdmin)
        );
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL A INQUILINO PENDIENTE DE SEGUNDO PAGO
    static function cashRenterMailPendientePC($alojamientoPedido, $payment){
        $asuntoHuesped = 'Pendiente segundo pago para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloHuesped = 'Tienes 24hs para realizar el pago';
        $cuerpoHuesped = '<p>Hola ' . $alojamientoPedido->Huesped->name . ', </p>
            <br>
            <p>¡Felicitaciones, tienes 24hs para realizar el pago! Luego el mismo vencerá y deberás generar otro.</p>
            <br>
            <p><b>Solo falta efectuar tu segundo pago:</b><br>
            <p><b>Total de la reserva:</b> '.$alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total
            ) .'</p>
            <p><b>Primer pago aprobado:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número de transacción:</b> ' . $alojamientoPedido->numero_transaccion . '</p>
            <br>
            <p><b>Segundo pago pendiente:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número de transacción:</b> ' . $payment->id . '</p>
            <br>
            <b><a style="color: #ef251b;border: 1px solid;text-decoration: none;display: block;text-align: center;border-color: #ef251b;
                border-radius: 12px;padding: 10px;" href="' . $payment->transaction_details->external_resource_url . '" target="_blank"
                rel="noopener noreferrer" class="btn btn-info">LINK DE PAGO</a>
            </b>
            <br>
            <br>
            <p><b>Segundo pago:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda( $alojamientoPedido->valor_total / 2) .
                ' lo debes realizar antes del <b>'
                . MailerController::dateFormater(
                    Carbon::createFromFormat(
                        'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                    )->subDays(15)->toDateString().' 00:00:00'
                ) .'</b>.
            </p>
            <ul style="text-align: left; padding-left: 0px;">
                <li>Se te enviara un correo 24hs antes de la fecha límite que debes realizar el pago.</li>
                <li>Si el pago no es efectuado pasadas 24hs de los tiempos establecidos, la reserva quedara 
                    Anulada y se aplicaran las respectivas políticas de cancelación de cada Alojamiento.
                </li>
                <li>Una vez realizado la totalidad del pago, se te enviara toda la información de tu Alojador y 
                    la ubicación exacta de tu Alojamiento.
                </li>
            </ul>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>Tu Alojador te estará esperando el<b> ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b><p>
            <p>Si no realizas el pago en <b>24hs</b>, tu reserva será <b>cancelada.</b></p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    ) 
                ) . 
            '
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes en las 
                que debes mantener el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles. 
                ¡Que disfrutes tu estadía! Si tienes alguna pregunta o duda comunícate con 
                nosotros al centro de ayuda ayuda@alojacolombia.com
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia</p>
            <style type="text/css">
                a:hover {
                    background-color: #edf2f7;
                    text-decoration: none;
                    transition: 1s;
                }
            </style>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoHuesped, $tituloHuesped, $cuerpoHuesped)
        );
    }

    //MAIL A PROPIETARIO PENDIENTE DE SEGUNDO PAGO
    static function cashOwnerMailPendientePC($alojamientoPedido, $payment){
        $asuntoPropietario = 'Pendiente segundo pago para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloPropietario = '¡Felicitaciones estás a solo un paso!';
        $cuerpoPropietario = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->name . ', </p>
            <br>
            <p>¡Felicitaciones, ' . $alojamientoPedido->Huesped->name . ' tiene 24hs para realizar el segundo pago!</p>
            <br>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>La fecha reservada es el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p><b>Fecha a depositar:</b> ' 
                . MailerController::dateFormater(
                    Carbon::createFromFormat(
                        'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                    )->addDay()->toDateString()
                ) . '
            </p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        true
                    ) 
                ) . 
            '
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes en las 
                que debes mantener el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles. 
                ¡Que disfrutes tu estadía! Si tienes alguna pregunta o duda comunícate con 
                nosotros al centro de ayuda ayuda@alojacolombia.com
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoPropietario, $tituloPropietario, $cuerpoPropietario)
        );
        //MAIL PROPIETARIO
    }

    //MAIL A ADMIN PENDIENTE DE SEGUNDO PAGO
    static function cashAdminMailPendientePC($alojamientoPedido, $payment){
        $asuntoAdmin = 'Pendiente segundo pago para '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta) . ' - 24hs';
        $tituloAdmin = '¡Reserva pendiente de segundo pago - 24hs!';
        $cuerpoAdmin = '<p>Hola equipo Aloja,</p>
            <br>
            <p>' . $alojamientoPedido->Huesped->name . ' hizo una reserva de pago en efectivo. Tiene 24hs para hacer el segundo pago, sino deberá volver a generar el cupón.</p>
            <br>
            <p>Hay una reserva dividida en 2 pagos:</p>
            <p><b>Total de la reserva:</b> '.$alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total
            ) .'</p>
            <p><b>Primer pago:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número de transacción MercadoPago:</b> ' . $alojamientoPedido->numero_transaccion . '</p>
            <p><b>Segundo pago:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda( $alojamientoPedido->valor_total / 2) .' 
                lo debe realizar antes del <b>'. MailerController::dateFormater(
                    Carbon::createFromFormat(
                        'Y-m-d H:i:s', $alojamientoPedido->fecha_desde
                    )->subDays(15)->toDateString().' 00:00:00'
                ) .'</b>.
            </p>' .
            $contacto = MailerController::usersInfo($alojamientoPedido) . '
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>La fecha reservada es el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p>Si no realiza el pago en 24hs, la reserva será cancelada.</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'ADMINISTRADOR',
                        true
                    ) 
                ) . 
            '
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes en las 
                que debes mantener el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles. 
                ¡Que disfrutes tu estadía! Si tienes alguna pregunta o duda comunícate con 
                nosotros al centro de ayuda ayuda@alojacolombia.com
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia</p>';
        $message = Mail::to(MailerController::$MAIL_RESERVAS);
        $message->send(
            new \App\Mail\MailGenerico($asuntoAdmin, $tituloAdmin, $cuerpoAdmin)
        );
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL A INQUILINO 7 DÍAS PREVIOS A LA FECHA DE LLEGADA
    static function renterMailSeven($alojamientoPedido){
        $asuntoHuesped = 'Tu viaje se aproxima - '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $tituloHuesped = '¡Tu viaje se aproxima!';
        $cuerpoHuesped = '<p>Hola ' . $alojamientoPedido->Huesped->name . ',
            ¡' . $alojamientoPedido->alojamiento->ciudad . ' te espera!</p>
            <br>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <br>
            <p>Tu Alojador te estará esperando el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p>Recuerda que tu alojador está a tu disposición para poder ayudarte.</p>
            <p>Ponte en contacto con <b>' . $alojamientoPedido->Alojamiento->Propietario->name . '</b> para aclarar cualquier duda sobre tu estancia.</p>
            <p><b>Datos de tú Alojador:</b></p>
            <p><b>Nombre:</b> ' . $alojamientoPedido->Alojamiento->Propietario->name . ', ' . $alojamientoPedido->Alojamiento->Propietario->apellido . '</p>
            <p><b>Celular:</b> ' . $alojamientoPedido->Alojamiento->Propietario->celular . '</p>
            <p><b>Cómo llegar:</b> '.$alojamientoPedido->Alojamiento->direccion.',
                '.$alojamientoPedido->Alojamiento->barrio.','.$alojamientoPedido->Alojamiento->ciudad.',
                '.$alojamientoPedido->Alojamiento->municipio.','.$alojamientoPedido->Alojamiento->departamento.',
                '.$alojamientoPedido->Alojamiento->mapa_locacion.'
            </p>
            <p>Comunícate con tu Alojador para confirmar los detalles de tu llegada.</p>
            <br>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    )
                ).
                $valorDeposito = MailerController::valorDeposito($alojamientoPedido) . 
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes en las 
                que debes mantener el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles.</p>
            <p>¡Que disfrutes tu estadía!</p>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoHuesped, $tituloHuesped, $cuerpoHuesped)
        );
    }
    //MAIL A PROPIETARIO 7 DÍAS PREVIOS A LA FECHA DE LLEGADA
    static function ownerMailSeven($alojamientoPedido){
        $asuntoPropietario = $alojamientoPedido->Huesped->name . ' llegará pronto - Viaja el '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' - '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad;
        $tituloPropietario = $alojamientoPedido->Huesped->name . ' llegará pronto';
        $cuerpoPropietario = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->name . '
            Te recordamos que ' . $alojamientoPedido->Huesped->name . ' llegará el ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>Ve preparando todo para su llegada.</p>
            <p><b>Datos del huesped:</b></p>
            <p><b>Nombre:</b> '. $alojamientoPedido->Huesped->name . ' ' . $alojamientoPedido->Huesped->apellido . '</p>
            <p><b>Celular:</b> ' . $alojamientoPedido->Huesped->celular . '</p>
            <p>Comunícate con tu huésped para confirmar los datos de llegada o para darle la bienvenida.</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        true
                    ) 
                ) . 
            '
            <p><b>Liquidación</b></p>
            <p>Te enviaremos el dinero que ganes como Alojador 24 horas después de la llegada de tu huésped. 
                El pago se te hará por medio de una transferencia bancaria a la cuenta que registraste pasadas 24 
                horas de la llegada de los huéspedes. (Este tiempo lo reservamos para que los huéspedes nos indiquen 
                que todo está de acuerdo a lo publicado en tu anuncio).
            </p>
            <hr/>
            <p><b>Depósito Reembolsable</b></p>
            <p>Si colocaste la opción de Depósito Reembolsable debes tener en cuenta que debe haber una persona 
                que reciba a los huéspedes a su llegada, realice un inventario y reciba el depósito. Al finalizar su estadía 
                deberá revisar que no se presenten daños o faltantes en la propiedad para que este sea reembolsado.
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <p>En caso de tener que cancelar esta reserva, van a continuar bloqueadas las noches en tu calendario, 
                en caso de que sean repetitivas las cancelaciones de tus reservas, cuenta y usuario podría ser dada de baja.
            </p>
            <hr/>
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda tener todo limpio y desinfectado, espacios ventilados y proporciónales a tus huéspedes alcohol 
                en gel a la llegada y salida de tu Alojamiento.
            </p>
            <hr/>
            <p><b>Cómo llegar a tu Alojamiento</b></p>
            <p>Asegúrate que tu huésped tenga las indicaciones necesarias para llegar tu Alojamiento.</p>
            <hr/>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoPropietario, $tituloPropietario, $cuerpoPropietario)
        );
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL A INQUILINO 1 DÍA PREVIO A LA FECHA DE LLEGADA
    static function renterMailOne($alojamientoPedido){
        $asuntoHuesped = '¿Ya tienes todo listo para mañana? – Viajas el '
        . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' - ' . $alojamientoPedido->alojamiento->titulo . ' en '
        . $alojamientoPedido->alojamiento->ciudad;
        $tituloHuesped = '¿Estás listo?';
        $cuerpoHuesped = '<p>Hola ' . $alojamientoPedido->Huesped->name . ',</p>
            <p>¡Tú viaje está a la vuelta de la esquina!</p>
            <p>¡' . $alojamientoPedido->alojamiento->ciudad . ' te espera!</p>
            <br>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p>Tu Alojador te estará esperando el <b>' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</b></p>
            <p>Recuerda que tu alojador está a tu disposición para poder ayudarte.</p>
            <p>Ponte en contacto con <b>' . $alojamientoPedido->Alojamiento->Propietario->name . '</b> para aclarar cualquier duda sobre tu estancia.</p>
            <p><b>Datos de tú Alojador:</b></p>
            <p><b>Nombre:</b> ' . $alojamientoPedido->Alojamiento->Propietario->name . ', ' . $alojamientoPedido->Alojamiento->Propietario->apellido . '</p>
            <p><b>Celular:</b> ' . $alojamientoPedido->Alojamiento->Propietario->celular . '</p>
            <p><b>Cómo llegar:</b> '.$alojamientoPedido->Alojamiento->direccion.',
                '.$alojamientoPedido->Alojamiento->barrio.','.$alojamientoPedido->Alojamiento->ciudad.',
                '.$alojamientoPedido->Alojamiento->municipio.','.$alojamientoPedido->Alojamiento->departamento.',
                '.$alojamientoPedido->Alojamiento->mapa_locacion.'
            </p>
            <p>Comunícate con tu Alojador para confirmar los detalles de tu llegada.</p>
            <br>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    )
                ).
                $valorDeposito = MailerController::valorDeposito($alojamientoPedido) .
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda que si en Alojamiento es en un edificio o un conjunto cerrado, hay áreas comunes en las 
                que debes mantener el distanciamiento físico y usas tapabocas en los lugares que sea obligatorio.
            </p>
            <hr/>
            <p><b>Infórmate bien</b></p>
            <p>Asegúrate de consultar las reglas de la casa y los servicios disponibles.</p>
            <p>¡Que disfrutes tu estadía!</p>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoHuesped, $tituloHuesped, $cuerpoHuesped)
        );
    }

    //MAIL A PROPIETARIO 1 DÍA PREVIO A LA FECHA DE LLEGADA
    static function ownerMailOne($alojamientoPedido){
        $asuntoPropietario = $alojamientoPedido->Huesped->name . ' llega mañana - Viaja el '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' - '
            . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad;
        $tituloPropietario = '¡' . $alojamientoPedido->Huesped->name . ' llega mañana!';
        $cuerpoPropietario = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->name . ',</p>
            <br>
            <p>Te recordamos que ' . $alojamientoPedido->Huesped->name . ' <b>llega mañana</b> ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . '</p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>¿Ya tienes todo listo para recibir a <b>'. $alojamientoPedido->Huesped->name . '</b>?</p>
            <p><b>Datos del huesped:</b></p>
            <p><b>Nombre:</b> '. $alojamientoPedido->Huesped->name . ' ' . $alojamientoPedido->Huesped->apellido . '</p>
            <p><b>Celular:</b> ' . $alojamientoPedido->Huesped->celular . '</p>
            <p>Comunícate con tu huésped para confirmar los datos de llegada o para darle la bienvenida.</p>
            <p><b>Información de la reserva</b></p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        true
                    ) 
                ) . 
            '
            <p><b>Liquidación</b></p>
            <p>Te enviaremos el dinero que ganes como Alojador 24 horas después de la llegada de tu huésped. 
                El pago se te hará por medio de una transferencia bancaria a la cuenta que registraste pasadas 24 
                horas de la llegada de los huéspedes. (Este tiempo lo reservamos para que los huéspedes nos indiquen 
                que todo está de acuerdo a lo publicado en tu anuncio).
            </p>
            <hr/>
            <p><b>Depósito Reembolsable</b></p>
            <p>Si colocaste la opción de Depósito Reembolsable debes tener en cuenta que debe haber una 
                persona que reciba a los huéspedes a su llegada, realice un inventario y reciba el depósito. 
                Al finalizar su estadía deberá revisar que no se presenten daños o faltantes en la propiedad 
                para que este sea reembolsado.
            </p>
            <hr/>
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <p>En caso de tener que cancelar esta reserva, van a continuar bloqueadas las noches en tu calendario, 
                en caso de que sean repetitivas las cancelaciones de tus reservas, cuenta y usuario podría ser dada de baja.
            </p>
            <hr/>
            <p><b>Seguridad contra el COVID-19</b></p>
            <p>Recuerda tener todo limpio y desinfectado, espacios ventilados y proporciónales a 
                tus huéspedes alcohol en gel a la llegada y salida de tu Alojamiento.
            </p>
            <hr/>
            <p><b>Cómo llegar a tu Alojamiento</b></p>
            <p>Asegúrate que tu huésped tenga las indicaciones necesarias para llegar tu Alojamiento.</p>
            <hr/>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoPropietario, $tituloPropietario, $cuerpoPropietario)
        );
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL A INQUILINO 18 HS PREVIAS A LA FECHA DE LLEGADA
    static function renterMailEighteen($alojamientoPedido){
        $asuntoHuesped = $alojamientoPedido->Huesped->name . ' te damos unos consejos antes de empezar tu viaje.';
        $tituloHuesped = 'Consejos';
        $cuerpoHuesped = '<div class="d-flex flex-column align-items-center" style="text-align-last:center;">
            <p>Hola ' . $alojamientoPedido->Huesped->name . ',</p>
            <br>
            <p>Gracias por elegir Aloja Colombia, antes de empezar tu viaje te damos unos consejos muy importantes que harán disfrutar al máximo tu estadía.</p>
            <br>
            <img style="width: 50px; height: 55px;" src="https://alojacolombia.com/img/diamond.png">
            <p style="font-size: 20px;text-align: center;"><b>Cada alojamiento es único</b></p>
            <br>
            <p style="text-align: center;">Recuerda que no estás en un hotel, por tal motivo los espacios y servicios que este brinde, pueden variar según cada Alojamiento.</p>
            <br>
            <img style="width: 50px;; height: 60px" src="https://alojacolombia.com/img/home.png">
            <p style="font-size: 20px;text-align: center;"><b>Cuida tu Alojamiento como si fuera tu casa</b></p>
            <br>
            <p style="text-align: center;">Los Alojadores invierten mucho tiempo y dinero para que te sientas como en tu casa, recuerda que es importante respetar sus reglas, espacio y el entorno en que se encuentra tu Alojamiento.</p>
            <br>
            <img style="width: 60px; ; height: 50px" src="https://alojacolombia.com/img/chat.png">
            <p style="font-size: 20px;text-align: center;"><b>Escríbele a tu Alojador antes de tu llegada</b></p>
            <br>
            <p style="text-align: center;">Mantente en contacto con él, avísale si tienes alguna duda de cómo llegar al Alojamiento, o 
                por si tiene alguna duda o inconveniente durante tu estadía.
            </p>
            <br>
            <br>
            <p>Tu viaje está próximo a comenzar,</p>
            <p>¡' . $alojamientoPedido->alojamiento->ciudad . ' te espera!</p>
            <br>
            <br>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia</b></p>
        </div>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoHuesped, $tituloHuesped, $cuerpoHuesped)
        );
    }
    //MAIL A INQUILINO 18 HS PREVIAS A LA FECHA DE LLEGADA
    static function ownerMailEighteen($alojamientoPedido){
        $asuntoPropietario ='Consejos para la llegada de tus huéspedes. ';
        $tituloPropietario = 'Consejos';
        $cuerpoPropietario = '<div class="d-flex flex-column align-items-center" style="text-align-last:center;">
            <p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->name . '</p>
            <p>Te damos unos consejos muy importantes.</p>
            <img style="width: 65px; height: 55px;" src="https://alojacolombia.com/img/clean.png">
            <p style="font-size: 20px;text-align: center;"><b>Deja limpio y ordenado el Alojamiento</b></p>
            <br>
            <p style="text-align: center;">La primera impresión muchas veces es fundamental, deja todo limpio y ordenado para que a la llegada de tus 
                huéspedes se sientan como en casa. Ten a la entrada Alcohol en gel para que tus huéspedes puedan desinfectarse a su llegada.
            </p>
            <br>
           <img style="width: 50px; height: 55px;" src="https://alojacolombia.com/img/password.png">
            <p style="font-size: 20px;text-align: center;"><b>Claves y Contraseñas</b></p>
            <br>
            <p style="text-align: center;">Recuerda informar o dejar en un lugar visible el usuario y la contraseña del WIFI (En caso de tener) y otras 
                claves que la propiedad pueda tener como alarmas y clave de accesos a la entrada.
            </p>
            <br>
            <img style="width: 35px; height: 55px;" src="https://alojacolombia.com/img/phone.png">
            <p style="font-size: 20px;text-align: center;"><b>Mantente comunicado con tus huéspedes</b></p>
            <br>
            <p style="text-align: center;">Mantente pendiente de la llegada de tus huéspedes, su hora de llegada, se puntual en el horario, no los hagas 
                esperar ya que muchas veces pueden estar cansados de su viaje. Asegúrate de darle las indicaciones correspondientes para que no tengan 
                ningún inconveniente en encontrar tu Alojamiento.
            </p>
            <br>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda ayuda@alojacolombia.com</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia</b></p>
        </div>';
        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
        $message->send(
            new \App\Mail\MailGenerico($asuntoPropietario, $tituloPropietario, $cuerpoPropietario)
        );
    }
    /////////////////////////////////////////////////////////////////////
    
    //MAIL DE ALERTA PARA EL PROPIETARIO QUE NO ACEPTO UNA SOLICITUD DE RESERVA
    static function ownerMailPending($alojamientoPedido){
        $asunto = 'Reserva pendiente de aprobación ' . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva pendiente de aprobación';
        $cuerpo = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() . ',</p>
            <p>Te recordamos que tienes una reserva pendiente de aprobación de  ' . $alojamientoPedido->Huesped->nombreCompleto() . 
                ', te quedan <b>12 horas</b> para que la aceptarla,
            </p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <a href="' .
                url(
                    '/alojamientosPedidos/' . $alojamientoPedido->id . '/edit'
                ) .
                '" class="button button-primary" target="_blank" >Aceptar o Rechazar
            </a>
            <br>
            <br>
            <p>Podrías ganar $ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_propietario) .' si aceptas esta reserva</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        false
                    ) 
                ) .
            '
            <p>Tienes 12 horas para confirmar la reserva, entre más rápido respondas más tiempo tendrán tus huéspedes para organizar su viaje.</p>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda 
                <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>
            </p>
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia,</p>';
            $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
            $message->send(
                new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo)
            );
    }

        /////////////////////////////////////////////////////////////////////

    //MAIL A PROPIETARIO - RESERVA PENDIENTE DE APROBACIÓN
    static function ownerMailPendingAprobation($alojamiento, $alojamientoPedido){
        $asunto = 'Reserva pendiente de aprobación ' . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva pendiente de aprobación';
        $cuerpo = '<p>Hola ' . $alojamiento->Propietario->nombreCompleto() . ',</p>
            <p>Tienes una reserva pendiente de aprobación de  ' . \Auth::user()->nombreCompleto() . ',</p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <a href="' .
                url(
                    '/alojamientosPedidos/' . $alojamientoPedido->id . '/edit'
                ) .
                '" class="button button-primary" target="_blank" >Aceptar o Rechazar
            </a>
            <br>
            <br>
            <p>Podrías ganar $ ' . $alojamiento->precioFormateadoMoneda(
                    $alojamientoPedido->valor_propietario) .' si aceptas esta reserva</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        false
                    ) 
                ) .
            '
            <p>Tienes 48 horas para confirmar la reserva, entre más rápido respondas más tiempo tendrán tus huéspedes para organizar su viaje.</p>
            <p>Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda 
                <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>
            </p>
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia,</p>';

            $message = Mail::to($alojamiento->Propietario->email);

            $message->send(
                new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo)
            );
    }
    //MAIL A INQUILINO - RESERVA PENDIENTE DE APROBACIÓN
    static function renterMailPendingAprobation($alojamiento, $alojamientoPedido){
        $asunto = 'Reserva pendiente de aprobación ' . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva pendiente de aprobación';
        $cuerpo = '<p>Hola ' . \Auth::user()->nombreCompleto() . ',</p>
            <p>La reserva aún no está confirmada, Recibirás una respuesta dentro de las próximas 48 horas.</p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    )
                ) .
            '
            <p><b>Proceso de reserva</b><br/>Una vez el Alojador acepte tu reserva, podrás efectuar el pago. 
                Recuerda que los pagos solo se realizan solo a través de la plataforma. Si tienes alguna pregunta o 
                duda comunícate con nosotros al centro de ayuda 
                <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>
            </p>
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia,</p>';
        $message = Mail::to(\Auth::user()->email);
        $message->send(
            new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo)
        );
    }
    //MAIL A ADMIN - RESERVA PENDIENTE DE APROBACIÓN
    static function adminMailPendingAprobation($alojamiento, $alojamientoPedido){
        $asunto = 'Reserva pendiente de aprobación ' . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva pendiente de aprobación';
        $cuerpo = '<p>Hola Equipo Aloja Colombia,</p>
            <p>Hay una reserva de ' . \Auth::user()->nombreCompleto() .
                ' pendiente de aprobación por parte de ' .
                $alojamiento->Propietario->nombreCompleto() . '.
            </p>' . 
            $contacto = MailerController::usersInfo($alojamientoPedido) . '
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'ADMINISTRADOR',
                        true
                    )
                ) .
            '
            <p>Saludos,</p>
            <p>Equipo Aloja Colombia,</p>';
        $message = Mail::to(MailerController::$MAIL_RESERVAS);
        $message->send(
            new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo)
        );
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL A PROPIETARIO - CANCELADO AUTOMÁTICO POR FALTA DE CONFIRMACIÓN 48HS (SO)
    static function ownerMailAutoCancelSO($alojamientoPedido){
        $asunto = 'Reserva no aprobada – '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta) .
            ' – ' . $alojamientoPedido->Alojamiento->titulo;
        $titulo = 'Reserva no aprobada';
        $cuerpo = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() .
            ', no aceptaste la reserva de ' .
            $alojamientoPedido->Huesped->nombreCompleto() . '.</p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>Recuerda que entre más reservas aceptes, mas reputación va a tener tu Alojamiento y 
                quedara en los primeros lugares de los motores de búsqueda. Si tienes alguna pregunta o 
                duda comunícate con nosotros al centro de ayuda 
                <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>
            </p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to(
            $alojamientoPedido->Alojamiento->Propietario->email
        );
        $message->send(new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo));
    }

    //MAIL A INQUILINO - CANCELADO AUTOMÁTICO POR FALTA DE CONFIRMACIÓN 48HS (SO)
    static function renterMailAutoCancelSO($alojamientoPedido){
        $asunto = $asunto = 'Reserva no aprobada – '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta) .
            ' – ' . $alojamientoPedido->Alojamiento->titulo;
        $titulo = 'Reserva no aprobada';
        $cuerpo = '<p>Hola ' . $alojamientoPedido->Huesped->nombreCompleto() . ',</p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>Desafortunadamente la reserva no fue aprobada por el Alojador, o no tuvimos respuesta dentro del plazo establecido.</p>
            <p>Puedes ingresar nuevamente a la plataforma y buscar otros Alojamientos dentro de las fechas indicadas. 
                Te ofrecemos disculpas que no hayas podido reservar este Alojamiento. Si tienes alguna pregunta o duda 
                comunícate con nosotros al centro de ayuda 
                <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>
            </p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo));
    }

    //MAIL A ADMIN - CANCELADO AUTOMÁTICO POR FALTA DE CONFIRMACIÓN 48HS (SO)
    static function adminMailAutoCancelSO($alojamientoPedido){
        $asunto = 'Reserva no aprobada – '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta) .
            ' – ' . $alojamientoPedido->Alojamiento->titulo;
        $titulo = 'Reserva no aprobada';
        $cuerpo =
            '<p>Hola Equipo Aloja Colombia,</p>' .
            '<p>La reserva fue rechazada por no ser aceptada en el plazo de 48hs</p>' .
            $contacto = MailerController::usersInfo($alojamientoPedido) . '
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to(MailerController::$MAIL_RESERVAS);
        $message->send(new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo));
    }

    /////////////////////////////////////////////////////////////////////

    //MAIL A PROPIETARIO - CANCELADO AUTOMÁTICO POR FALTA DE PAGO 24HS (CO)
    static function ownerMailAutoCancelCO($alojamientoPedido){
        $asunto = 'Reserva Cancelada Por Falta de Pago – ' .
            $alojamientoPedido->Alojamiento->titulo . 
            ' en ' . $alojamientoPedido->Alojamiento->ciudad .
            ' del ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva Cancelada';
        $cuerpo = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() .',</p>
        <p>Desafortunadamente la reserva fue cancelada porque el huesped no la pagó
            dentro de los plazos establecidos.
        </p>
        <p>El pago debería haberse realizado antes de ' . $alojamientoPedido->fecha_confirmacion . '</p>
        <p>Código de reserva: ' . $alojamientoPedido->codigo_reserva . '</p>
        <ul>
            <li>Si el pago no era efectuado pasadas 24 hs de los tiempos establecidos, la
                reserva quedará Anulada y se aplicaron las respectivas políticas de cancelación.
            </li>
        </ul>
            <div style="text-align-last: center;">
                <h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        true
                    ) 
                ) .
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>En caso de que te corresponda algun tipo reembolso, el equipo de Aloja
                Colombia se comunicará directamente contigo para hacer la liquidación
                correspondiente.
            </p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to(
            $alojamientoPedido->Alojamiento->Propietario->email
        );
        $message->send(new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo));
    }
    //MAIL A INQUILINO - CANCELADO AUTOMÁTICO POR FALTA DE PAGO 24HS (CO)
    static function renterMailAutoCancelCO($alojamientoPedido){
        $asunto = 'Reserva Cancelada Por Falta de Pago – ' .
            $alojamientoPedido->Alojamiento->titulo . 
            ' en ' . $alojamientoPedido->Alojamiento->ciudad .
            ' del ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva Cancelada';
        $cuerpo = '<p>Hola ' . $alojamientoPedido->Huesped->name .',</p>
        <p>Desafortunadamente tu reserva fue cancelada porque no la pagaste
            dentro de los plazos establecidos.
        </p>
        <p>El pago debería haberse realizado antes de ' . $alojamientoPedido->fecha_confirmacion . '</p>
        <p>Código de reserva: ' . $alojamientoPedido->codigo_reserva . '</p>
        <ul>
            <li>Si el pago no era efectuado pasadas 24 hs de los tiempos establecidos, la
                reserva quedará Anulada y se aplicarán las respectivas políticas de
                cancelación en caso que hayas elegido efectuar la reserva en dos pagos.
            </li>
        </ul>
            <div style="text-align-last: center;">
                <h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    ) 
                ) .
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>En caso de que te corresponda algun tipo reembolso, el equipo de Aloja
                Colombia se comunicará directamente contigo para hacer la liquidación
                correspondiente.
            </p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo));
    }
    //MAIL A ADMIN - CANCELADO AUTOMÁTICO POR FALTA DE PAGO 24HS (CO)
    static function adminMailAutoCancelCO($alojamientoPedido){
        $asunto = 'Reserva Cancelada Por Falta de Pago – ' .
            $alojamientoPedido->Alojamiento->titulo . 
            ' en ' . $alojamientoPedido->Alojamiento->ciudad .
            ' del ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva Cancelada';
        $cuerpo = '<p>Hola Equipo Aloja,</p>
        <p>Desafortunadamente la reserva fue cancelada porque el huesped no la pagó
            dentro de los plazos establecidos.
        </p>
        <p>El pago debería haberse realizado antes de ' . $alojamientoPedido->fecha_confirmacion. '</p>
        <p>Código de reserva: ' . $alojamientoPedido->codigo_reserva . '</p>' .
        $contacto = MailerController::usersInfo($alojamientoPedido) . '
        <ul>
            <li>Si el pago no era efectuado pasadas 24 hs de los tiempos establecidos, la
                reserva quedará Anulada y se aplicaron las respectivas políticas de cancelación.
            </li>
        </ul>
            <div style="text-align-last: center;">
                <h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'ADMINISTRADOR',
                        true
                    ) 
                ) .
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>En caso de que corresponda algún tipo reembolso, debemos comunicarnos
                con el huesped para hacer la liquidación correspondiente.
            </p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to(MailerController::$MAIL_RESERVAS);
        $message->send(new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo));
    }

/////////////////////////////////////////////////////////////////////

    //MAIL A PROPIETARIO - CANCELADO MANUAL HUESPED
    static function ownerMailManualCancel($alojamientoPedido){
        $asunto = 'Reserva Cancelada Por el Huesped – ' .
            $alojamientoPedido->Alojamiento->titulo . 
            ' en ' . $alojamientoPedido->Alojamiento->ciudad .
            ' del ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) .
            ' al ' . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva Cancelada';
        $cuerpo = '<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() .',</p>
            <p>Desafortunadamente la reserva fue cancelada por el huesped</p>
            <p>Código de reserva: ' . $alojamientoPedido->codigo_reserva . '</p>
            <div style="text-align-last: center;">
                <h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'PROPIETARIO',
                        true
                    ) 
                ) .
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>En caso de que te corresponda algun tipo reembolso, el equipo de Aloja
                Colombia se comunicará directamente contigo para hacer la liquidación
                correspondiente.
            </p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to(
            $alojamientoPedido->Alojamiento->Propietario->email
        );
        $message->send(new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo));
    }
    //MAIL A INQUILINO - CANCELADO MANUAL HUESPED
    static function renterMailManualCancel($alojamientoPedido){
        $asunto = 'Reserva Cancelada Por el Huesped – ' .
            $alojamientoPedido->Alojamiento->titulo . 
            ' en ' . $alojamientoPedido->Alojamiento->ciudad .
            ' del ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) .
            ' al ' . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva Cancelada';
        $cuerpo = '<p>Hola ' . $alojamientoPedido->Huesped->name .',</p>
            <p>Desafortunadamente cancelaste tu reserva</p>
            <p>Código de reserva: ' . $alojamientoPedido->codigo_reserva . '</p>
            <div style="text-align-last: center;">
                <h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    ) 
                ) .
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>En caso de que te corresponda algun tipo reembolso, el equipo de Aloja
                Colombia se comunicará directamente contigo para hacer la liquidación
                correspondiente.
            </p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to($alojamientoPedido->Huesped->email);
        $message->send(new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo));
    }

    //MAIL A ADMIN - CANCELADO MANUAL HUESPED
    static function adminMailManualCancel($alojamientoPedido){
        $asunto = 'Reserva Cancelada Por el Huesped – ' .
            $alojamientoPedido->Alojamiento->titulo . 
            ' en ' . $alojamientoPedido->Alojamiento->ciudad .
            ' del ' . MailerController::dateFormater($alojamientoPedido->fecha_desde) .
            ' al ' . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva Cancelada';
        $cuerpo = '<p>Hola Equipo Aloja,</p>
            <p>Desafortunadamente la reserva fue cancelada por el huesped.</p>
            <p>Código de reserva: ' . $alojamientoPedido->codigo_reserva . '</p>' .
            $contacto = MailerController::usersInfo($alojamientoPedido) . '
            <div style="text-align-last: center;">
                <h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'ADMINISTRADOR',
                        true
                    ) 
                ) .
            '
            <p><b>Política de cancelación</b></p>
            ' . $politicaCancelacion = MailerController::politicaCancelacion($alojamientoPedido) . '
            <p>Revisa la política de cancelación en los <a href="/doc/terminos.pdf" >términos y condiciones.</a></p>
            <hr/>
            <p>En caso de que corresponda algún tipo reembolso, debemos comunicarnos
                con el huesped para hacer la liquidación correspondiente.
            </p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
        $message = Mail::to(MailerController::$MAIL_RESERVAS);
        $message->send(new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo));
    }
    
    /////////////////////////////////////////////////////////////////////

    //MAIL A PROPIETARIO - RESERVA ACEPTADA
    static function ownerMailAccepted($alojamientoPedido){
        $mensaje = '<p>Hemos enviado a la aprobación de la socitud de reserva.</p>
            <br>
            <p>Una vez sea aprobado el pago, se te enviará un correo electrónico con los 
                datos de contacto de tu húesped.
            </p>
            <br>
            <p>En caso que el pago no sea efectuado durante las próximas 24hs, 
                la reserva se anulará automáticamente y se te notificará a tu correo electrónico.
            </p>';
            $asunto = 'Reserva aprobada pendiente de pago ' . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
            $titulo = 'Reserva Aprobada pendiente de pago';
            $cuerpo ='<p>Hola ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() .
                ', aceptaste la reserva de ' . $alojamientoPedido->Huesped->nombreCompleto() . ',</p>
                <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                    <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
                </div>
                <br>
                <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
                ' .
                    ($liquidacion =
                        MailerController::liquidacion(
                            $alojamientoPedido,
                            $alojamientoPedido->fecha_desde,
                            $alojamientoPedido->fecha_hasta,
                            'PROPIETARIO',
                            true
                        )
                    ) . 
                '
                <p>Esta solicitud fue enviada y estamos a la espera del pago, una vez sea confirmado te lo informaremos. 
                    El huésped tiene 24 horas para efectuar el pago, delo contrario la reserva quedara liberada y quedara tu 
                    Alojamiento disponible nuevamente. Si tienes alguna pregunta o duda comunícate con nosotros al centro 
                    de ayuda <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>
                </p>
                <p>Saludos,</p>
                <p><b>Equipo Aloja Colombia,</b></p>';
            $message = Mail::to(
                $alojamientoPedido->Alojamiento->Propietario->email
            );
            $message->send(
                new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo)
            );
    }

    //MAIL A INQUILINO - RESERVA ACEPTADA
    static function renterMailAccepted($alojamientoPedido){
        $mensaje = '<p>Hemos enviado la aprobación de la solicitud de reserva.</p>
            <br>
            <p>Una vez sea aprobado el pago, se te enviará un correo electrónico con los 
                datos de contacto.
            </p>
            <br>
            <p>En caso que el pago no sea efectuado durante las próximas 24hs., 
                la reserva se anulará automáticamente y se te notificará a tu correo electrónico.
            </p>';
        $asunto = 'Reserva aprobada pendiente de pago ' . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva Aprobada pendiente de pago';
        $cuerpo = '<p>Hola ' . $alojamientoPedido->Huesped->nombreCompleto() . ',</p>
        <p>¡Felicitaciones¡ Tu solicitud de reserva ha sido aprobada,</p>
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            <p>Para finalizar el proceso de reserva de este Alojamiento, tienes 24 horas a partir de la llegada de este correo 
                para realizar tu pago, pasado este tiempo tu reserva quedará anulada, si deseas retomar la 
                reserva deberás iniciar el proceso nuevamente.
            </p>
            <p>Recuerda que solo lo puedes hacerlo por medio de nuestra plataforma, solo así podremos garantizar tu seguridad y la de tu dinero.</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'INQUILINO',
                        true
                    ) 
                ) .
            '
            <p>Una vez efectuado el pago recibirás toda la información de tu Alojador y la ubicación 
                exacta del Alojamiento. Si tienes alguna pregunta o duda comunícate con nosotros al centro 
                de ayuda <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>
            </p>
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
            $message = Mail::to($alojamientoPedido->Huesped->email);
            $message->send(
                new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo)
            );
    }

    //MAIL A ADMIN - RESERVA ACEPTADA
    static function adminMailAccepted($alojamientoPedido){
        $mensaje = '<p>Hemos enviado la aprobación de la solicitud de reserva.</p>
            <br>
            <p>Una vez sea aprobado el pago, se te enviará un correo electrónico con los 
                datos de contacto de tu húesped.
            </p>
            <br>
            <p>En caso que el pago no sea efectuado durante las próximas 24hs., 
                la reserva se anulará automáticamente y se te notificará a tu correo electrónico.
            </p>';
        $asunto = 'Reserva aprobada pendiente de pago ' . $alojamientoPedido->alojamiento->titulo . ' en '
            . $alojamientoPedido->alojamiento->ciudad . ' del '
            . MailerController::dateFormater($alojamientoPedido->fecha_desde) . ' al '
            . MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        $titulo = 'Reserva Aprobada pendiente de pago';
        $cuerpo = '<p>Hola Equipo Aloja Colombia,</p>
            <p>Hay una reserva de ' . $alojamientoPedido->Huesped->nombreCompleto() . 
                ' pendiente de pago por parte de ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() . '
            </p>' .
            $contacto = MailerController::usersInfo($alojamientoPedido) . '
            <div style="text-align-last: center;"><h2>' . $alojamientoPedido->alojamiento->titulo . '</h2>
                <img style="width: 250px;border-radius: 3%;" src="https://alojacolombia.com/uploads/'. $alojamientoPedido->alojamiento->fotoAlojamiento[0]->archivo. '">
            </div>
            <br>
            <p><b>Código de la propiedad:</b> ' . $alojamientoPedido->alojamiento->codigo_alojamiento . '</p>
            ' .
                ($liquidacion =
                    MailerController::liquidacion(
                        $alojamientoPedido,
                        $alojamientoPedido->fecha_desde,
                        $alojamientoPedido->fecha_hasta,
                        'ADMINISTRADOR',
                        true
                    )
                ) .
            '
            <p>Saludos,</p>
            <p><b>Equipo Aloja Colombia,</b></p>';
            $message = Mail::to(MailerController::$MAIL_RESERVAS);
            $message->send(
                new \App\Mail\MailGenerico($asunto, $titulo, $cuerpo)
            );
    }

    /////////////////////////////////////////////////////////////////////

    //LIQUIDACION DEL COBRO PARA ADMIN, ALOJADOR Y HUESPED
    static function liquidacion($alojamientoPedido, $fechaDesde, $fechaHasta, $modo, $completo){
        $fechaDesdeDate = \Carbon\Carbon::parse($fechaDesde);
        $fechaDesdeDia = ucfirst($fechaDesdeDate->translatedFormat('l')) . ',';
        $fechaDesdeFormateada =
            $fechaDesdeDate->translatedFormat('d') .
            ' de ' .
            ucfirst($fechaDesdeDate->monthName) .
            ' de ' .
            $fechaDesdeDate->format('Y');
        $fechaHastaDate = \Carbon\Carbon::parse($fechaHasta);
        $fechaHastaDia = ucfirst($fechaHastaDate->translatedFormat('l')) . ',';
        $fechaHastaFormateada =
            $fechaHastaDate->translatedFormat('d') .
            ' de ' .
            ucfirst($fechaHastaDate->monthName) .
            ' de ' .
            $fechaHastaDate->format('Y');
        $liquidacion ='<div class="mail_liquidacion">
            <span class="mail_liquidacion_titulo">' .
                $alojamientoPedido->Alojamiento->titulo .
            ' </span><br/><br/>
            Código de la propiedad: 
            <span class="mail_liquidacion_valor">' .
                $alojamientoPedido->Alojamiento->codigo_alojamiento .
            ' </span><hr/>
            Código de la reserva: 
            <span class="mail_liquidacion_valor">' .
                $alojamientoPedido->codigo_reserva .
            ' </span><hr/>
            <table style="width: 100%"><tr><td class="mail_liquidacion_fechadesde"> <b>Desde</b>: ' .
                $fechaDesdeDia .
                '<br>' .
                $fechaDesdeFormateada .
                '<br>Check In: ' .
                $alojamientoPedido->Alojamiento->check_in .
                ' hs</td><td class="mail_liquidacion_fechahasta"> <b>Hasta</b>: ' .
                $fechaHastaDia .
                '<br>' .
                $fechaHastaFormateada .
                '<br>Check Out: ' .
                $alojamientoPedido->Alojamiento->check_out .
                ' hs</td></tr></table><hr/>
                Huéspedes: <span class="mail_liquidacion_valor">' .
                $alojamientoPedido->huespedes .
            '</span>';
        if ($completo) {
            $huespedesX = '';
            if($alojamientoPedido->Alojamiento->tipo_alquiler == 'HU'){
                $huespedesX = ' x ' . $alojamientoPedido->huespedes . ' huespedes';
            }
            $liquidacion .='<hr/>
                <span class="mail_liquidacion_titulo">Cobro</span>
                <br/><br/>
                <p>$ ' .
                    $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                        $alojamientoPedido->valor_noche_promedio
                    ) .
                    ' x ' .
                    $alojamientoPedido->cantidad_noches .
                    ' noches' . $huespedesX . ' 
                    <span class="mail_liquidacion_valor">$ ' .
                        $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                            $alojamientoPedido->valor_subtotal
                        ) .
                    '</span>
                </p>
                <p>Descuento: <span class="mail_liquidacion_valor">- $ ' .
                $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                    $alojamientoPedido->valor_descuento
                ) .
                '</span></p>
                <p>Tarifa de limpieza: <span class="mail_liquidacion_valor">$ ' .
                $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                    $alojamientoPedido->valor_limpieza
                ) .
                '</span></p>';
            if ($modo == 'PROPIETARIO') {
                $liquidacion .= '<p>Comisión por servicio: <span class="mail_liquidacion_valor">- $ ' .
                    $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                        $alojamientoPedido->valor_comision_servicio
                    ) .
                    '</span></p>
                    <p>Total que recibirás: <span class="mail_liquidacion_valor">$ ' .
                        $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                            $alojamientoPedido->valor_propietario
                        ) .
                    '</span></p>';
            }
            if ($modo == 'ADMINISTRADOR') {
                $liquidacion .= '<p>Total abonado por reserva: <span class="mail_liquidacion_valor">$ ' .
                    $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                        $alojamientoPedido->valor_total
                    ) .
                    '</span></p>
                    <p>Comisión alojador (3%): <span class="mail_liquidacion_valor">$ ' .
                        $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                            $alojamientoPedido->valor_comision_servicio
                        ) .
                    '</span></p>
                    <p>Comisión alojado (15%): <span class="mail_liquidacion_valor">$ ' .
                    $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                        $alojamientoPedido->valor_servicio
                    ) .
                    '</span></p>
                    <p>Total comisiones: <span class="mail_liquidacion_valor">$ ' .
                    $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                        $alojamientoPedido->totalComisiones()
                    ) .
                    '</span></p>
                    <p>Total a transferir al alojador: <span class="mail_liquidacion_valor">$ ' .
                    $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                        $alojamientoPedido->valor_propietario
                    ) .
                    '</span></p>';
            }
            if ($modo == 'INQUILINO') {
                $liquidacion .= '<p>Tarifa por servicio: <span class="mail_liquidacion_valor">$ ' .
                    $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                        $alojamientoPedido->valor_servicio
                    ) .
                    '</span></p>
                    <p>Total: <span class="mail_liquidacion_valor">$ ' .
                    $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                        $alojamientoPedido->valor_total
                    ) .
                    '</span></p>';
            }
        } // Fin completo
        $liquidacion .= '</div>';
        if ($completo) {
            if (
                $modo == 'INQUILINO' ||
                $modo == 'PROPIETARIO' ||
                $modo == 'ADMINISTRADOR'
            ) {
                $liquidacion .= '<p><b>Limpieza del Alojamiento</b></p>
                    <p>Este Alojamiento está comprometido a seguir el proceso de limpieza implementado durante la pandemia de COVID-19 y también en el futuro.</p><hr/>';
            }
        } // Fin completo 2
        return $liquidacion;
    }

    //MUESTRA EN MAIL EL VALOR DEL DEPÓSITO
    static function valorDeposito($alojamientoPedido){
        if(isset($alojamientoPedido->valor_deposito) || $alojamientoPedido->valor_deposito>0){
            $valorDeposito = '<p><b>Deposito Reembolsable</b></p>
                <p>Recuerda que este Alojamiento tiene un deposito reembolsable de <b>$'
                    . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_deposito) . ' </b>el
                    cual será devuelto al final de tu estadía de no presentarse daños o faltantes a la propiedad.
                </p>
                <hr/>';
            return $valorDeposito;
        }
    }

    //MUESTRA EN MAIL POLÍTICA DE CANCELACION CORRESPONDIENTE AL ALOJAMIENTO
    static function politicaCancelacion($alojamientoPedido){
        switch ($alojamientoPedido->Alojamiento->politica_cancelacion) {
            case 'F':
                return '<p>Cuentas con una política de cancelación <b>Flexible</b>.<br>
                    El ALOJADO recibirá un reembolso total de la reserva, si cancela 24 horas antes del Check In.</p>';
                break;
            case 'M':
                return '<p>Cuentas con una política de cancelación <b>Moderada</b>.<br>
                    El ALOJADO recibirá un reembolso total de la reserva, si cancela 7 días antes del Check In.</p>';
                break;
            case 'E':
                return '<p>Cuentas con una política de cancelación <b>Estrícta</b>.<br>
                    El ALOJADO recibirá un reembolso del 50% del total de la reserva, si cancela 15 días antes del Check In.</p>';
                break;
            case 'S':
                return '<p>Cuentas con una política de cancelación <b>Muy Estrícta</b>.<br>
                    Si el ALOJADO cancela la reserva no se le hará ningún reembolso.</p>';
                break;
        }
    }
    
    //SI SE REALIZÓ EL PAGO EN DOS VECES MUESTRA LA DIVISIÓN DEL PAGO Y LOS NÚMEROS DE TRANSACCIÓN
    static function secondPay($alojamientoPedido){
        if($alojamientoPedido->numero_transaccion2 != null){
            $secondPay = '<p><b>El pago total de la reserva ha sido efectuado</b></p>
            <p><b>Total de la reserva:</b> '.$alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total
            ) .'</p>
            <p><b>Primer pago aprobado:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número transacción:</b> '.$alojamientoPedido->numero_transaccion .'</p>
            <p><b>Segundo pago aprobado:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número transacción:</b> '.$alojamientoPedido->numero_transaccion2 . '</p><hr/>';
            return $secondPay;
        }
    }

    static function asuntoSufijo($alojamientoPedido){
        $sufijo = ' – ' . $alojamientoPedido->fecha_desde;
        $fechaDesdeDate = \Carbon\Carbon::parse(
            $alojamientoPedido->fecha_desde
        );
        $fechaDesdeFormateada = $fechaDesdeDate->format('d/m/Y');
        $fechaHastaDate = \Carbon\Carbon::parse(
            $alojamientoPedido->fecha_hasta
        );
        $fechaHastaFormateada = $fechaHastaDate->format('d/m/Y');
        $sufijo .=
            ' - ' . $fechaDesdeFormateada . ' al ' . $fechaHastaFormateada;
        return $sufijo;
    }
    //FORMATEO DE FECHA (EJ: Lunes, 29 de Agosto de 2022 Check In: 20:46:00 hs)
    static function dateFormater($fecha){
        $fechaParse = \Carbon\Carbon::parse($fecha);
        $fechaFormateada =
            $fechaParse->translatedFormat('d') .
            ' de ' .
            ucfirst($fechaParse->monthName) .
            ' de ' .
            $fechaParse->format('Y');
        return $fechaFormateada;
    }
    //INFORMACIÓN DE USUARIOS PARA MAILS DE ADMINISTRACIÓN
    static function usersInfo($alojamientoPedido){
        $usersInfo ='
        <hr/>
        <p><b>Contacto Propietario</b></p>
        <p>Nombre: ' . $alojamientoPedido->Alojamiento->Propietario->name . ' ' . $alojamientoPedido->Alojamiento->Propietario->apellido . '</p>
        <p>Correo: ' . $alojamientoPedido->Alojamiento->Propietario->email . '</p>
        <p>Teléfono: ' . $alojamientoPedido->Alojamiento->Propietario->celular . '</p>
        <br>
        <p><b>Contacto Huesped</b></p>
        <p>Nombre: ' . $alojamientoPedido->Huesped->name . ' ' . $alojamientoPedido->Huesped->apellido . '</p>
        <p>Correo: ' . $alojamientoPedido->Huesped->email . '</p>
        <p>Teléfono: ' . $alojamientoPedido->Huesped->celular . '</p>
        <hr/>
        <br>';
        return $usersInfo;
    }
    //TITULO Y FOTO
    static function titleAndPic($alojamientoInactivo){
        if($alojamientoInactivo->numero_transaccion2 != null){
            $secondPay = '<p><b>El pago total de la reserva ha sido efectuado</b></p>
            <p><b>Total de la reserva:</b> '.$alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total
            ) .'</p>
            <p><b>Primer pago aprobado:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número transacción:</b> '.$alojamientoPedido->numero_transaccion .'</p>
            <p><b>Segundo pago aprobado:</b> '. $alojamientoPedido->Alojamiento->precioFormateadoMoneda(
                $alojamientoPedido->valor_total / 2
            )  .'</p>
            <p><b>Número transacción:</b> '.$alojamientoPedido->numero_transaccion2 . '</p><hr/>';
            return $secondPay;
        }
    }
}
