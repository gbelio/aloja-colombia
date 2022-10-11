<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Mail;
use App\Alojamiento;
use App\AlojamientoPedido;
use App\AlojamientoCalendario;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\MailerController;
use Carbon\Carbon;

class AlojamientosPedidosController extends Controller
{
    // Indice del backend
    public function index(Request $request){
        $time = Carbon::now();
        $estado = $request->input('busqueda');
        $op = $request->input('op');
        if ($op == null) {
            abort('403');
        }
        $alojamientosPedidos = AlojamientoPedido::join(
            'alojamientos as A',
            'A.id',
            '=',
            'alojamientos_pedidos.alojamiento_id'
        );
        // Se muestran o reservas realizadas como huesped (op 2) y las recibidas como propietario (op 1)
        if (!Auth::user()->esAdministrador()) {
            if ($op == 1) {
                $alojamientosPedidos = $alojamientosPedidos->where(
                    'A.propietario_id',
                    \Auth::user()->id
                )->orderBy('alojamientos_pedidos.id','desc');
            } else {
                $alojamientosPedidos = $alojamientosPedidos->where(
                    'huesped_id',
                    \Auth::user()->id
                )->orderBy('alojamientos_pedidos.id','desc');
            }
        }

        if ($estado != '') {
            $alojamientosPedidos = $alojamientosPedidos->where(
                'alojamientos_pedidos.estado',
                $estado
            )->orderBy('alojamientos_pedidos.id','desc');
        }

        $alojamientosPedidos = $alojamientosPedidos
            ->where('alojamientos_pedidos.estado', '<>', 'RE')
            ->select('alojamientos_pedidos.*')
            ->orderBy('alojamientos_pedidos.id', 'desc')
            ->orderBy('alojamientos_pedidos.estado', 'DESC') // SO CO
            ->paginate(20);
        return View('alojamientosPedidos.index')
        ->with('alojamientosPedidos', $alojamientosPedidos)
        ->with('time', $time);
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

    public function edit(Request $request, $id){
        $alojamientoPedido = AlojamientoPedido::find($id);
        $alojamiento = Alojamiento::find($alojamientoPedido->alojamiento_id);
        $this->tieneAcceso($alojamiento, $alojamientoPedido);

        $puedeEditar = false;

        if (
            Auth::user()->esAdministrador() ||
            $alojamiento->propietario_id == \Auth::user()->id
        ) {
            $puedeEditar = true;

            if ($op = $request->input('op') == 2) {
                $puedeEditar = false;
            }
        }
        if ($alojamientoPedido->estado == 'PP') {
            $alojamientoPedido->valor_total_mitad =
                $alojamientoPedido->valor_total / 2;
        }
        $alojamientoPedido->fecha_from = $alojamientoPedido->fecha_desde;
        $alojamientoPedido->fecha_desde = MailerController::dateFormater($alojamientoPedido->fecha_desde);
        $alojamientoPedido->fecha_hasta = MailerController::dateFormater($alojamientoPedido->fecha_hasta);
        return View('alojamientosPedidos.save')
            ->with('puedeEditar', $puedeEditar)
            ->with('alojamientoPedido', $alojamientoPedido)
            ->with(
                'descuentoDescripcion',
                app(
                    '\App\Http\Controllers\AlojamientosController'
                )->descuentoFormateado(
                    $alojamientoPedido->por_descuento,
                    $alojamientoPedido->tipo_descuento
                )
            )
            ->with('method', 'PUT')
            ->with('cantidadHuespedes', $alojamientoPedido->huespedes);
    }

    // Cancelación de reserva por motivos:

    // 1) Motivo manual, rechazo desde pantalla del propietario

    // 2) Motivo automático, pasadas las 24 horas desde job CronJobReservas

    public function cancelarReserva($alojamientoPedido){
        /* MailerController::test($alojamientoPedido);
            MailerController::ownerMailManualCancel($alojamientoPedido);
            MailerController::adminMailManualCancel($alojamientoPedido);
            MailerController::renterMailManualCancel($alojamientoPedido); */

            // Liberar días
        /* $desde = date($alojamientoPedido->fecha_desde);
            $hasta = date($alojamientoPedido->fecha_hasta); */
        $alojamientoPedido->save();
        $bloqueosEliminar = AlojamientoCalendario::where(
            'alojamiento_id',
            $alojamientoPedido->alojamiento_id
        )
            ->where('fecha', '>=', $alojamientoPedido->fecha_desde)
            ->where('fecha', '<', $alojamientoPedido->fecha_hasta)
            ->delete();
    }

    public function update(Request $request, $id){
        $alojamientoPedido = AlojamientoPedido::find($id);
        /* $alojamiento = Alojamiento::find($alojamientoPedido->alojamiento_id); */
        $estadoNuevo = $request->input('navegacion');
        $mensaje = '';
        if ($estadoNuevo == 'CO') {
            $alojamientoPedido->estado = 'CO';
            $alojamientoPedido->fecha_confirmacion = \Carbon\Carbon::now();
            MailerController::ownerMailAccepted($alojamientoPedido);
            MailerController::renterMailAccepted($alojamientoPedido);
            MailerController::adminMailAccepted($alojamientoPedido);
        } elseif ($estadoNuevo == 'RE') {
            $alojamientoPedido->estado = 'RE';
            $alojamientoPedido->fecha_rechazo = \Carbon\Carbon::now();
            $this->cancelarReserva($alojamientoPedido);
            MailerController::ownerMailAutoCancelSO($alojamientoPedido);
            MailerController::adminMailAutoCancelSO($alojamientoPedido);
            MailerController::renterMailAutoCancelSO($alojamientoPedido);
        } elseif ($estadoNuevo == 'CA') {
            $alojamientoPedido->estado = 'RE';
            $alojamientoPedido->fecha_rechazo = \Carbon\Carbon::now();
            $this->cancelarReserva($alojamientoPedido);
            MailerController::ownerMailManualCancel($alojamientoPedido);
            MailerController::adminMailManualCancel($alojamientoPedido);
            MailerController::renterMailManualCancel($alojamientoPedido);
        } 
        $alojamientoPedido->save();
        return Redirect::back()->with('notice', $mensaje);
    }

    static function codigoReserva($alojamientoPedido, $alojamiento){
        $fechaDesdeFormat = date("dmy", strtotime($alojamientoPedido->fecha_desde));
        $fechaHastaFormat = date("dmy", strtotime($alojamientoPedido->fecha_hasta));
        $formatID = str_pad($alojamientoPedido->id, 4, "0", STR_PAD_LEFT);
        $formatCD = "";
        if($alojamiento->ciudad){
            for ($i=0; $i < 3; $i++) { 
                $formatCD .= $alojamiento->ciudad[$i];
            }
        }else{
            $formatCD .= $alojamiento->departamento[$i];
        }
        $codigoReserva = strtoupper(
            $fechaDesdeFormat . 
            $alojamiento->tipo_alojamiento . 
            $alojamiento->politica_cancelacion . 
            $formatID . 
            $formatCD . 
            $fechaHastaFormat);

        return $codigoReserva;
    }
}
