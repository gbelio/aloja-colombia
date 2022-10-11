<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Mail;
use App\AlojamientoPedido;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\AlojamientosPedidosController;

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
        $week = \Carbon\Carbon::now()->subDays(7)->toDateTimeString();
        $alojamientosPedidosNoRechazados = AlojamientoPedido::
        where('estado', '<>', 'RE')
        ->where('fecha_desde', '>', " $week  ")
        ->get();
        foreach ($alojamientosPedidosNoRechazados as $alojamiento) {
            switch ($alojamiento->estado) {
                case 'SO':
                    if (((strtotime($ahora) - strtotime($alojamiento->fecha_pedido))/60)/60 >= 12 && !$alojamiento->fecha_reclamo_aceptacion){
                        $alojamiento->fecha_reclamo_aceptacion = \Carbon\Carbon::now();
                        $alojamiento->save();
                        MailerController::ownerMailPending($alojamiento);
                    }elseif (((strtotime($ahora) - strtotime($alojamiento->fecha_pedido))/60)/60 >= 24){
                        $alojamiento->estado = 'RE';
                        $alojamiento->fecha_rechazo = \Carbon\Carbon::now();
                        $alojamiento->save();
                        app('\App\Http\Controllers\AlojamientosPedidosController')->cancelarReserva($alojamiento);
                        MailerController::ownerMailAutoCancelSO($alojamientoPedido);
                        MailerController::adminMailAutoCancelSO($alojamientoPedido);
                        MailerController::renterMailAutoCancelSO($alojamientoPedido);
                    }
                    break;

                case 'CO':
                    if(((strtotime($ahora) - strtotime($alojamiento->fecha_confirmacion))/60)/60 >= 24){
                        $alojamiento->estado = 'RE';
                        $alojamiento->fecha_rechazo = \Carbon\Carbon::now();
                        $alojamiento->save();
                        app('\App\Http\Controllers\AlojamientosPedidosController')->cancelarReserva($alojamiento);
                        MailerController::ownerMailAutoCancelCO($alojamientoPedido);
                        MailerController::adminMailAutoCancelCO($alojamientoPedido);
                        MailerController::renterMailAutoCancelCO($alojamientoPedido);
                    }
                    break;

                case 'PC':
                    if(((strtotime($alojamiento->fecha_desde) - strtotime($ahora))/60)/60 <= 24 && !$alojamiento->fecha_veinticuatro){
                        $alojamiento->fecha_veinticuatro = \Carbon\Carbon::now();
                        if(!$alojamiento->fecha_siete){
                            $alojamiento->fecha_siete = \Carbon\Carbon::now();
                        }
                        $alojamiento->save();
                        MailerController::renterMailOne($alojamiento);
                        MailerController::ownerMailOne($alojamiento);
                    }elseif (((strtotime($alojamiento->fecha_desde) - strtotime($ahora))/60)/60 <= 168 && !$alojamiento->fecha_siete){
                        $alojamiento->fecha_siete = \Carbon\Carbon::now();
                        $alojamiento->save();
                        MailerController::renterMailSeven($alojamiento);
                        MailerController::ownerMailSeven($alojamiento);
                    } elseif (((strtotime($alojamiento->fecha_desde) - strtotime($ahora))/60)/60 <= 18 && !$alojamiento->fecha_dieciocho){
                        $alojamiento->fecha_dieciocho = \Carbon\Carbon::now();
                        $alojamiento->save();
                        MailerController::renterMailEighteen($alojamiento);
                        MailerController::ownerMailEighteen($alojamiento);
                    }
                    break;
            }
        };
    }
}
