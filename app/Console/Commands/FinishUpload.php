<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Mail;
use App\AlojamientoPedido;
use App\Alojamiento;
use App\Http\Controllers\MailerController;
use App\Http\Controllers\AlojamientosPedidosController;

class FinishUpload extends Command

{
    protected $signature = 'command:FinishUpload';
    protected $description = 'Alertas asociadas a reservas';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $month = \Carbon\Carbon::now()->subDays(30)->toDateTimeString();
        $twoWeek = \Carbon\Carbon::now()->subDays(15)->toDateTimeString();
        $alojamientosInactivos = Alojamiento::
        where('estado', '<>', 'A')
        ->where('updated_at', '<', " $twoWeek ")
        ->get();
        foreach ($alojamientosInactivos as $alojamientoInactivo) {
            if($alojamientoInactivo->mapa_locacion == null ||
            $alojamientoInactivo->huespedes == null ||
            $alojamientoInactivo->descripcion == null ||
            $alojamientoInactivo->check_in == null ||
            $alojamientoInactivo->precio_alta == null ||
            $alojamientoInactivo->cuenta_nombre == null){
                if($alojamientoInactivo->notification == null){
                    MailerController::ownerMailFU($alojamientoInactivo);
                    $alojamientoInactivo->notification = "first";
                    $alojamientoInactivo->save();
                }elseif($alojamientoInactivo->notification == "first"){
                    MailerController::ownerMailFU($alojamientoInactivo);
                    $alojamientoInactivo->notification = "second";
                    $alojamientoInactivo->save();
                }
            }elseif ($alojamientoInactivo->updated_at < $month && $alojamientoInactivo->notification != "third") {
                MailerController::ownerMailActivate($alojamientoInactivo);
                $alojamientoInactivo->notification = "third";
                $alojamientoInactivo->save();
            }
        }
    }
}
