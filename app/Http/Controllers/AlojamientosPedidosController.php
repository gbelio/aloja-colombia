<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Mail;
use App\Alojamiento;
use App\AlojamientoPedido;
use App\AlojamientoCalendario;

use Illuminate\Support\Facades\Redirect;

class AlojamientosPedidosController extends Controller
{   

    // Indice del backend
    public function index(Request $request)
    {

		$estado = $request->input('busqueda');
		$op = $request->input('op');

		if ( $op == null ) {
			abort('403');
		}

		$alojamientosPedidos = AlojamientoPedido::
			join( 'alojamientos as A', 'A.id', '=', 'alojamientos_pedidos.alojamiento_id');

		// Se muestran o reservas realizadas como huesped (op 2) y las recibidas como propietario (op 1)
		if ( !Auth::user()->esAdministrador() ) {
			if ( $op == 1) {
				$alojamientosPedidos = $alojamientosPedidos->where('A.propietario_id', \Auth::user()->id);
			}
			else {
				$alojamientosPedidos = $alojamientosPedidos->where('huesped_id', \Auth::user()->id);
			}
		}

		if ( $estado != '' ) {
			$alojamientosPedidos = $alojamientosPedidos->where('alojamientos_pedidos.estado', $estado);
		}

		$alojamientosPedidos = $alojamientosPedidos
		->where('alojamientos_pedidos.estado', '<>', 'RE')
		->select('alojamientos_pedidos.*')
		->orderBy('alojamientos_pedidos.estado','DESC') // SO CO
		->orderBy('alojamientos_pedidos.id')
		->paginate(20);

        return View('alojamientosPedidos.index')->with('alojamientosPedidos', $alojamientosPedidos);
    }

    public function tieneAcceso($alojamiento, $alojamientoPedido) {

        if ( !Auth::user()->esAdministrador() ) {
          if ( $alojamiento->propietario_id != \Auth::user()->id && $alojamientoPedido->huesped_id != \Auth::user()->id ) {
            abort('403');
          }
        }

    }  

    public function edit(Request $request, $id)
    {
        $alojamientoPedido = AlojamientoPedido::find($id);
        $alojamiento = Alojamiento::find($alojamientoPedido->alojamiento_id);

		$this->tieneAcceso($alojamiento, $alojamientoPedido);

		$puedeEditar = false;
		if ( Auth::user()->esAdministrador() || $alojamiento->propietario_id == \Auth::user()->id ) {
			$puedeEditar = true;
			if ( $op = $request->input('op') == 2 ) {
				$puedeEditar = false;
			}
		}

        return View('alojamientosPedidos.save')
          ->with('puedeEditar', $puedeEditar)
          ->with('alojamientoPedido', $alojamientoPedido)
    	  ->with('descuentoDescripcion', app('\App\Http\Controllers\AlojamientosController')->descuentoFormateado($alojamientoPedido->por_descuento, $alojamientoPedido->tipo_descuento))
          ->with('method', 'PUT');
    }

    public function liquidacion($alojamientoPedido, $fechaDesde, $fechaHasta, $modo, $completo) {

		$fechaDesdeDate = \Carbon\Carbon::parse($fechaDesde); 
		$fechaDesdeDia = ucfirst($fechaDesdeDate->translatedFormat('l')) . ',';
		$fechaDesdeFormateada = $fechaDesdeDate->translatedFormat('d') . ' de ' . ucfirst($fechaDesdeDate->monthName) . ' de ' . $fechaDesdeDate->format('Y');
		$fechaHastaDate = \Carbon\Carbon::parse($fechaHasta); 
		$fechaHastaDia = ucfirst($fechaHastaDate->translatedFormat('l')) . ',';
		$fechaHastaFormateada = $fechaHastaDate->translatedFormat('d') . ' de ' . ucfirst($fechaHastaDate->monthName) . ' de ' . $fechaHastaDate->format('Y');    	

		$liquidacion = '<div class="mail_liquidacion">
<span class="mail_liquidacion_titulo">' . $alojamientoPedido->Alojamiento->titulo .' </span><br/><br/>

Código de la propiedad: <span class="mail_liquidacion_valor">' . $alojamientoPedido->alojamiento_id . ' </span><hr/>

Código de la reserva: <span class="mail_liquidacion_valor">' . $alojamientoPedido->id . ' </span><hr/>

<table style="width: 100%"><tr><td class="mail_liquidacion_fechadesde"> <b>Desde</b>: ' . $fechaDesdeDia .  '<br/>' . $fechaDesdeFormateada . '<br/>Check In: ' . $alojamientoPedido->Alojamiento->check_in . ' hs</td><td class="mail_liquidacion_fechahasta"> <b>Hasta</b>: ' . $fechaHastaDia .  '<br/>' . $fechaHastaFormateada . '<br/>Check Out: ' . $alojamientoPedido->Alojamiento->check_out . ' hs</td></tr></table><hr/>

Huéspedes: <span class="mail_liquidacion_valor">' . $alojamientoPedido->huespedes . '</span>

';

		if ( $completo ) {

			$liquidacion .= '<hr/>

<span class="mail_liquidacion_titulo">Cobro</span><br/><br/>

$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_noche_promedio) . ' x ' . $alojamientoPedido->cantidad_noches . ' noches: <span class="mail_liquidacion_valor">$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_subtotal) . '</span>

Descuento: <span class="mail_liquidacion_valor">- $ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_descuento) . '</span>

Tarifa de limpieza: <span class="mail_liquidacion_valor">$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_limpieza) . '</span>';

	if ( $modo == 'PROPIETARIO' ) {

		$liquidacion .= '

Comisión por servicio: <span class="mail_liquidacion_valor">- $ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_comision_servicio) . '</span>

Total que recibirás: <span class="mail_liquidacion_valor">$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_propietario) . '</span>

';

}

	if ( $modo == 'ADMINISTRADOR' ) {

		$liquidacion .= '

Comisión alojador (3%): <span class="mail_liquidacion_valor">$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_servicio) . '</span>

Comisión alojado (15%): <span class="mail_liquidacion_valor">$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_comision_servicio) . '</span>

Total comisiones: <span class="mail_liquidacion_valor">$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->totalComisiones()) . '</span>

Total a transferir al alojador: <span class="mail_liquidacion_valor">$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_propietario) . '</span>

';

}

	if ( $modo == 'INQUILINO' ) {

		$liquidacion .= '

Tarifa por servicio: <span class="mail_liquidacion_valor">$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_servicio) . '</span>

Total: <span class="mail_liquidacion_valor">$ ' . $alojamientoPedido->Alojamiento->precioFormateadoMoneda($alojamientoPedido->valor_total) . '</span>

';

}

	} // Fin completo


	$liquidacion .= '</div>';


	if ( $completo ) {	

		if ( $modo == 'INQUILINO' || $modo == 'PROPIETARIO' || $modo == 'ADMINISTRADOR' ) {

			$liquidacion .= '

<b>Políticas de cancelación</b><br/>
Los huéspedes recibirán un reembolso del 50% del total de la reserva (menos la tarifa de servicio) , si cancelan 15 días antes del Check In.<br/><i>*Check in: Se contemplan horas y días completas antes de la hora local de llegada del anuncio (indicada en el correo electrónico de confirmación.</i><hr/>

<b>Limpieza del Alojamiento</b><br/>
Este Alojamiento está comprometido a seguir el proceso de limpieza implementado durante la pandemia de COVID-19 y también en el futuro.<hr/>
';

}

	} // Fin completo 2

	return ($liquidacion);

    }

    public function asuntoSufijo ($alojamientoPedido) {
    	$sufijo =  ' – ' . $alojamientoPedido->Alojamiento->titulo;

    	$fechaDesdeDate = \Carbon\Carbon::parse($alojamientoPedido->fecha_desde); 
		$fechaDesdeFormateada = $fechaDesdeDate->format('d/m/Y');
    	$fechaHastaDate = \Carbon\Carbon::parse($alojamientoPedido->fecha_hasta); 
		$fechaHastaFormateada = $fechaHastaDate->format('d/m/Y');

		$sufijo .= ' - ' . $fechaDesdeFormateada . ' al ' . $fechaHastaFormateada;
    	return $sufijo;
    }

    // Cancelación de reserva por motivos:
    // 1) Motivo manual, rechazo desde pantalla del propietario
    // 2) Motivo automático, pasadas las 24 horas desde job CronJobReservas
    public function cancelarReserva($alojamientoPedido) {

			$asunto = 'Reserva no aprobada – ' . $alojamientoPedido->fecha_desde .  ' a ' . $alojamientoPedido->fecha_hasta . ' – ' . $alojamientoPedido->Alojamiento->titulo;
			$titulo = 'Reserva no aprobada';

			// Mail al propietario
			$cuerpo = 'Hola ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() . ', no aceptaste la reserva de ' . $alojamientoPedido->Huesped->nombreCompleto() . ',

Recuerda que entre más reservas aceptes, mas reputación va a tener tu Alojamiento y quedara en los primeros lugares de los motores de búsqueda. Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>

Saludos,

Equipo Aloja Colombia,';

	        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
	        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));  
			
			
			// Mail al inquilino
			$cuerpo = 'Hola ' . $alojamientoPedido->Huesped->nombreCompleto() . ',

Desafortunadamente la reserva no fue aprobada por el Alojador, o no tuvimos respuesta dentro del plazo establecido.

Puedes ingresar nuevamente a la plataforma y buscar otros Alojamientos dentro de las fechas indicadas. Te ofrecemos disculpas que no hayas podido reservar este Alojamiento. Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>

Saludos,

Equipo Aloja Colombia,';

	        $message = Mail::to($alojamientoPedido->Huesped->email);
	        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));  
			

			// Mail al administrador
			$cuerpo = 'Hola Equipo Aloja Colombia,

' . $alojamientoPedido->Huesped->nombreCompleto() . ' no aceptó la reserva de ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() . ',

Saludos,

Equipo Aloja Colombia,';

	        $message = Mail::to(app('\App\Http\Controllers\AlojamientosController')->MAIL_RESERVAS);
	        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));  

	        // Liberar días

        	$desde = date($alojamientoPedido->fecha_desde);
        	$hasta = date($alojamientoPedido->fecha_hasta);

	        $bloqueosEliminar = AlojamientoCalendario::
	        where('alojamiento_id', $alojamientoPedido->alojamiento_id)
	        ->where('fecha', '>=', $alojamientoPedido->fecha_desde)
	        ->where('fecha', '<', $alojamientoPedido->fecha_hasta)
	        ->delete();  
    }

    public function update(Request $request, $id)
    {
    	$alojamientoPedido = AlojamientoPedido::find($id);
    	$estadoNuevo = $request->input('navegacion');

    	$mensaje = '';
    	
    	$alojamientoPedido->estado = $estadoNuevo;

    	if ( $estadoNuevo == 'CO' ) {
    		$alojamientoPedido->fecha_confirmacion = \Carbon\Carbon::now();
    		
    		$mensaje = 'Hemos enviado a la aprobación de la socitud de reserva.<br/>Una vez sea aprobado el pago, se te enviará un correo electrónico con los datos de contacto de tu húesped.<br/>En caso que el pago no sea efectuado durante las próximas 24hs., la reserva se anulará automáticamente y se te notificará a tu correo electrónico.';

			$asunto = 'Reserva Aprobada pendiente de pago' . $this->asuntoSufijo($alojamientoPedido);
			$titulo = 'Reserva Aprobada pendiente de pago';

			// Mail al propietario
			$cuerpo = 'Hola ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() . ', aceptaste la reserva de ' . $alojamientoPedido->Huesped->nombreCompleto() . ',

' . $liquidacion = $this->liquidacion($alojamientoPedido, $alojamientoPedido->fecha_desde, $alojamientoPedido->fecha_hasta, 'PROPIETARIO', true) . '

Esta solicitud fue enviada y estamos a la espera del pago, una vez sea confirmado te lo informaremos. El huésped tiene 24 horas para efectuar el pago, delo contrario la reserva quedara liberada y quedara tu Alojamiento disponible nuevamente. Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>

Saludos,

Equipo Aloja Colombia,';

	        $message = Mail::to($alojamientoPedido->Alojamiento->Propietario->email);
	        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));  
			
			
			// Mail al inquilino
			$cuerpo = 'Hola ' . $alojamientoPedido->Huesped->nombreCompleto() . ',

¡Felicitaciones¡ Tu solicitud de reserva ha sido aprobada,

Para finalizar el proceso de reserva de este Alojamiento, tienes 24 horas a partir de la llegada de este correo para realizar tu pago, pasado este tiempo tu reserva quedará anulada, si deseas retomar la reserva deberás iniciar el proceso nuevamente.

Recuerda que solo lo puedes hacerlo por medio de nuestra plataforma, solo así podremos garantizar tu seguridad y la de tu dinero.

' . $liquidacion = $this->liquidacion($alojamientoPedido, $alojamientoPedido->fecha_desde, $alojamientoPedido->fecha_hasta, 'INQUILINO', true) . '

Una vez efectuado el pago recibirás toda la información de tu Alojador y la ubicación exacta del Alojamiento. Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>

Saludos,

Equipo Aloja Colombia,';

	        $message = Mail::to($alojamientoPedido->Huesped->email);
	        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));  
			

			// Mail al administrador
			$cuerpo = 'Hola Equipo Aloja Colombia,

Hay una reserva de ' . $alojamientoPedido->Huesped->nombreCompleto() . ' pendiente de pago por parte de ' . $alojamientoPedido->Alojamiento->Propietario->nombreCompleto() . ',

' . $liquidacion = $this->liquidacion($alojamientoPedido, $alojamientoPedido->fecha_desde, $alojamientoPedido->fecha_hasta, 'ADMINISTRADOR', true) . '

Saludos,

Equipo Aloja Colombia,';

	        $message = Mail::to(app('\App\Http\Controllers\AlojamientosController')->MAIL_RESERVAS);
	        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));  

    	}
    	else if ( $estadoNuevo == 'RE' ) {

    		$alojamientoPedido->fecha_rechazo = \Carbon\Carbon::now();

    		$this->cancelarReserva($alojamientoPedido);    	

    	}

    	$alojamientoPedido->save();

    	return Redirect::back()->with('notice', $mensaje);

    }

}