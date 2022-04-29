<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Mail;
use App\AlojamientoPedido;

class CronJobReservas extends Command
{
    protected $signature = 'command:CronJobReservas';
    protected $description = 'Alertas asociadas a reservas';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $ahora = \Carbon\Carbon::now()->toDateTimeString();

        // Reservas vencidas, se debe liberar las fechas

        $alojamientosPedidosVencidos = AlojamientoPedido::
        where('alojamientos_pedidos.estado', 'SO')
        ->whereRaw('HOUR(TIMEDIFF(fecha_pedido, \'' . $ahora . '\')) > ?', 24)
        ->get();

        foreach ($alojamientosPedidosVencidos as $reservaVencida) {
            $reservaVencida->estado = 'RE';
            $reservaVencida->fecha_rechazo = \Carbon\Carbon::now();
            $reservaVencida->save();
            app('\App\Http\Controllers\AlojamientosPedidosController')->cancelarReserva($reservaVencida);
        }

        // Reservas pasadas 12 horas, se debe avisar

        $alojamientosPedidosDemorados = AlojamientoPedido::
        where('alojamientos_pedidos.estado', 'SO')
        ->whereRaw('HOUR(TIMEDIFF(fecha_pedido, \'' . $ahora . '\')) > ?', 12)
        ->whereNull('fecha_reclamo_aceptacion')
        ->get();

        foreach ($alojamientosPedidosDemorados as $reservaDemorada) {
            $reservaDemorada->fecha_reclamo_aceptacion = \Carbon\Carbon::now();
            $reservaDemorada->save();
            $liquidacion = app('\App\Http\Controllers\AlojamientosPedidosController')->liquidacion($reservaDemorada, $reservaDemorada->fecha_desde, $reservaDemorada->fecha_hasta, 'PROPIETARIO', false);
            $asunto = 'Reserva pendiente de aprobación' . app('\App\Http\Controllers\AlojamientosPedidosController')->asuntoSufijo($reservaDemorada);
            $titulo = 'Reserva pendiente de aprobación';
            $cuerpo = 'Hola ' . $reservaDemorada->Alojamiento->Propietario->nombreCompleto() . ',

Te recordamos que tienes una reserva pendiente de aprobación de  ' . $reservaDemorada->Huesped->nombreCompleto() . ', te quedan 12 horas para que la aceptarla,

<a href="' . url('/alojamientosPedidos/' . $reservaDemorada->id . '/edit') . '" class="button button-primary" target="_blank" >Aceptar o Rechazar</a>

Podrías ganar $ ' . $reservaDemorada->Alojamiento->precioFormateadoMoneda($reservaDemorada->valor_propietario) . ' si aceptas esta reserva

' . $liquidacion . '

Tienes 12 horas para confirmar la reserva, entre más rápido respondas más tiempo tendrán tus huéspedes para organizar su viaje.
Si tienes alguna pregunta o duda comunícate con nosotros al centro de ayuda <a href="mailto:ayuda@alojacolombia.com">ayuda@alojacolombia.com</a>

Saludos,

Equipo Aloja Colombia,';
            $message = Mail::to($reservaDemorada->Alojamiento->Propietario->email);
            $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));
        }
    }
}
