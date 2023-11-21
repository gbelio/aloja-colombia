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
        foreach ($alojamientosPedidosNoRechazados as $alojamientoPedido) {
            switch ($alojamientoPedido->estado) {
                case 'SO':
                    if (((strtotime($ahora) - strtotime($alojamientoPedido->fecha_pedido))/60)/60 >= 12 && !$alojamientoPedido->fecha_reclamo_aceptacion){
                        $alojamientoPedido->fecha_reclamo_aceptacion = \Carbon\Carbon::now();
                        $alojamientoPedido->save();
                        MailerController::ownerMailPending($alojamientoPedido);
                    }elseif (((strtotime($ahora) - strtotime($alojamientoPedido->fecha_pedido))/60)/60 >= 48){
                        $alojamientoPedido->estado = 'RE';
                        $alojamientoPedido->fecha_rechazo = \Carbon\Carbon::now();
                        $alojamientoPedido->save();
                        app('\App\Http\Controllers\AlojamientosPedidosController')->cancelarReserva($alojamientoPedido);
                        MailerController::ownerMailAutoCancelSO($alojamientoPedido);
                        MailerController::adminMailAutoCancelSO($alojamientoPedido);
                        MailerController::renterMailAutoCancelSO($alojamientoPedido);
                    }
                    break;

                case 'CO':
                    if(((strtotime($ahora) - strtotime($alojamientoPedido->fecha_confirmacion))/60)/60 >= 48){
                        $alojamientoPedido->estado = 'RE';
                        $alojamientoPedido->fecha_rechazo = \Carbon\Carbon::now();
                        $alojamientoPedido->save();
                        app('\App\Http\Controllers\AlojamientosPedidosController')->cancelarReserva($alojamientoPedido);
                        MailerController::ownerMailAutoCancelCO($alojamientoPedido);
                        MailerController::adminMailAutoCancelCO($alojamientoPedido);
                        MailerController::renterMailAutoCancelCO($alojamientoPedido);
                    }
                    break;

                case 'PC':
                    if(((strtotime($alojamientoPedido->fecha_desde) - strtotime($ahora))/60)/60 <= 24 && !$alojamientoPedido->fecha_veinticuatro){
                        $alojamientoPedido->fecha_veinticuatro = \Carbon\Carbon::now();
                        if(!$alojamientoPedido->fecha_siete){
                            $alojamientoPedido->fecha_siete = \Carbon\Carbon::now();
                        }
                        $alojamientoPedido->save();
                        MailerController::renterMailOne($alojamientoPedido);
                        MailerController::ownerMailOne($alojamientoPedido);
                    }elseif (((strtotime($alojamientoPedido->fecha_desde) - strtotime($ahora))/60)/60 <= 168 && !$alojamientoPedido->fecha_siete){
                        $alojamientoPedido->fecha_siete = \Carbon\Carbon::now();
                        $alojamientoPedido->save();
                        MailerController::renterMailSeven($alojamientoPedido);
                        MailerController::ownerMailSeven($alojamientoPedido);
                    } elseif (((strtotime($alojamientoPedido->fecha_desde) - strtotime($ahora))/60)/60 <= 18 && !$alojamientoPedido->fecha_dieciocho){
                        $alojamientoPedido->fecha_dieciocho = \Carbon\Carbon::now();
                        $alojamientoPedido->save();
                        MailerController::renterMailEighteen($alojamientoPedido);
                        MailerController::ownerMailEighteen($alojamientoPedido);
                    }
                    break;
            }
        };
    }
}
