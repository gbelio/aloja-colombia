<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AlojamientoPedido;
use App\Alojamiento;
use App\Http\Controllers\MailerController;
use App\User;
use Auth;
use DB;
class StatisticsController extends Controller
{
    public function showAll(){
        $alojamientosPedidos = AlojamientoPedido::query()
            ->orderByDesc('fecha_desde')
            ->Paginate(10);
        foreach ($alojamientosPedidos as $alojamientoPedido) {
            $alojamientoPedido->fecha_desde = MailerController::dateFormater($alojamientoPedido->fecha_desde);
            $alojamientoPedido->fecha_hasta = MailerController::dateFormater($alojamientoPedido->fecha_hasta);
            $alojamientoPedido->fecha_pedido = MailerController::dateFormater($alojamientoPedido->fecha_pedido);
        }
        $alojamientosTotal = Alojamiento::where('estado','<>','I')
            ->count();
        $alojamientosPedidosTotal = AlojamientoPedido::query()
            ->where('estado','<>','RE')
            ->count();
        $alojamientosRechazadosTotal = AlojamientoPedido::query()
            ->where('estado','=','RE')
            ->count();
        $alojamientoPedidoPendientesPago = AlojamientoPedido::query()
            ->where('estado','=','CO')
            ->orWhere('estado','=','PP')
            ->count();
        $usuariosTotal = User::count() - 1;
        return view('statistics.show')
            ->with('alojamientosPedidos', $alojamientosPedidos)
            ->with('alojamientosPedidosTotal', $alojamientosPedidosTotal)
            ->with('alojamientosTotal', $alojamientosTotal)
            ->with('usuariosTotal', $usuariosTotal)
            ->with('alojamientosRechazadosTotal', $alojamientosRechazadosTotal)
            ->with('alojamientoPedidoPendientesPago', $alojamientoPedidoPendientesPago);
    }

    public function show($id){
        /* $alojamientos = Alojamiento::query()
        ->where('propietario_id','=',$id)->get();
        $alojamientosPedidosUser = [];
        $i=0;
        $alojamientosPedidos = AlojamientoPedido::all();
        foreach ($alojamientosPedidos as $alojamientoPedido){
            foreach ($alojamientos as $alojamiento) {
                if ($alojamientoPedido->alojamiento_id == $alojamiento->id){
                    $alojamientosPedidosUser[$i] = $alojamientoPedido;
                    $i++;
                    switch ($alojamientoPedido->estado) {
                        case 'RE':
                            $alojamientosRechazadosTotal++;
                            break;
                        case 'PC':
                            $alojamientosPedidosTotal++;
                            break;
                            case 'CO':
                                $alojamientoPedidoPendientesPago++;
                                break;
                                case 'PP':
                                    $alojamientoPedidoPendientesPago++;
                            break;
                    }
                }
            }
        } */
        /* $alojamientosTotal = Alojamiento::query()
        ->where('propietario_id','=',$id)
        ->count(); */
        
        /* if(Auth::user()->id != $id){
            return redirect('/');
        } */

        $alojamientosPedidosPaginados = AlojamientoPedido::query()
            ->join('alojamientos', 'alojamientos.id', '=', 'alojamientos_pedidos.alojamiento_id')
            ->where('alojamientos.propietario_id', '=', $id)
            ->select('alojamientos_pedidos.*')
            ->Paginate(10);
        
        $alojamientosPedidosConteo = AlojamientoPedido::query()
            ->join('alojamientos', 'alojamientos.id', '=', 'alojamientos_pedidos.alojamiento_id')
            ->where('alojamientos.propietario_id', '=', $id)
            ->select('alojamientos_pedidos.alojamiento_id', 'alojamientos_pedidos.estado')
            ->get();
        
        $alojamientosRechazadosTotal = 0;
        $alojamientosPedidosTotal = 0;
        $alojamientoPedidoPendientesPago = 0;
        $alojamientosTotal = 0;
        $alojamientoCheck = 0;

        foreach ($alojamientosPedidosConteo as $alojamientoPedido) {
            if($alojamientoCheck != $alojamientoPedido->alojamiento_id){
                $alojamientosTotal++;
                $alojamientoCheck = $alojamientoPedido->alojamiento_id;
            }
            switch ($alojamientoPedido->estado) {
                case 'RE':
                    $alojamientosRechazadosTotal++;
                    break;
                case 'PC':
                    $alojamientosPedidosTotal++;
                    break;
                case 'CO':
                    $alojamientoPedidoPendientesPago++;
                    break;
                case 'PP':
                    $alojamientoPedidoPendientesPago++;
                    break;
            }
        }

        foreach ($alojamientosPedidosPaginados as $alojamientoPedido) {
            $alojamientoPedido->fecha_desde = MailerController::dateFormater($alojamientoPedido->fecha_desde);
            $alojamientoPedido->fecha_hasta = MailerController::dateFormater($alojamientoPedido->fecha_hasta);
            $alojamientoPedido->fecha_pedido = MailerController::dateFormater($alojamientoPedido->fecha_pedido);
        }

        return view('statistics.show')
            ->with('alojamientosPedidos', $alojamientosPedidosPaginados)
            ->with('alojamientosPedidosTotal', $alojamientosPedidosTotal)
            ->with('alojamientosTotal', $alojamientosTotal)
            ->with('alojamientosRechazadosTotal', $alojamientosRechazadosTotal)
            ->with('alojamientoPedidoPendientesPago', $alojamientoPedidoPendientesPago);
    }
}
