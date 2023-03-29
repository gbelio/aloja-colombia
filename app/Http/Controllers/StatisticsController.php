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

    public function usersInfo(){
        $users = User::query()
        ->orderByDesc('created_at')
        ->Paginate(10);
        $propietariosTotal = Alojamiento::query()
            ->get()
            ->groupBy('propietario_id')
            ->count();
        $alojamientosTotal = Alojamiento::where('estado','<>','I')
            ->count();
        $alojamientosInactivos = Alojamiento::where('estado','=','I')
            ->count();
        $usuariosTotal = User::count() - 1;
        $huespedesTotal = $usuariosTotal - $propietariosTotal;
        return view('statistics.users')
            ->with('users', $users)
            ->with('alojamientosTotal', $alojamientosTotal)
            ->with('propietariosTotal', $propietariosTotal)
            ->with('alojamientosInactivos', $alojamientosInactivos)
            ->with('huespedesTotal', $huespedesTotal)
            ->with('usuariosTotal', $usuariosTotal);
    }

    public function propertiesInfo(){
        $alojamientos = Alojamiento::query()
            ->Paginate(10);
        $totalAlojamientos = Alojamiento::count();
        $totalInactivos = Alojamiento::where('estado', 'I')->count();
        $totalActivos = Alojamiento::where('estado', 'A')->count();
        $totalIncompletos = Alojamiento::where('mapa_locacion', null)
            ->orWhere('mapa_locacion', null)
            ->orWhere('huespedes', null)
            ->orWhere('descripcion', null)
            ->orWhere('check_in', null)
            ->orWhere('precio_alta', null)
            ->orWhere('cuenta_nombre', null)
            ->count();
        if (count($alojamientos) != 0){
        }else{
            $alojamientos=[];
        }
        foreach ($alojamientos as $alojamiento) {

            if($alojamiento->tipo_alquiler = "TO"){
                $alojamiento->tipo_alquiler = "TOTAL";
            }else{
                $alojamiento->tipo_alquiler = "CANT. HUESPED";
            }

            switch ($alojamiento->tipo_alojamiento) {
                case 'AP':
                    $alojamiento->tipo_alojamiento = 'Apartamento';
                    break;
                case 'CS':
                    $alojamiento->tipo_alojamiento = 'Casa';
                    break;
                case 'CB':
                    $alojamiento->tipo_alojamiento = 'Cabaña';
                    break;
                case 'FN':
                    $alojamiento->tipo_alojamiento = 'Finca';
                    break;
                case 'GL':
                    $alojamiento->tipo_alojamiento = 'Glamping';
                    break;
            }

            switch ($alojamiento->notification) {
                case 'NULL':
                    $alojamiento->notification = 'Sin notificar';
                    break;
                case 'first':
                    $alojamiento->notification = '1. Notificación Completar';
                    break;
                case 'second':
                    $alojamiento->notification = '2. Notificación Completar';
                    break;
                case 'third':
                    $alojamiento->notification = 'Notificación Activar';
                    break;
            }
            
            if($alojamiento->estado == "I"){
                if($alojamiento->mapa_locacion == null ||
                    $alojamiento->huespedes == null ||
                    $alojamiento->descripcion == null ||
                    $alojamiento->check_in == null ||
                    $alojamiento->precio_alta == null ||
                    $alojamiento->cuenta_nombre == null){
                        $alojamiento->carga = "Incompleta";
                        $alojamiento->estado = "Inactivo";
                    }else{
                        $alojamiento->carga = "Completa";
                        $alojamiento->estado = "Inactivo";
                }
            }else{
                $alojamiento->carga = "Completa";
                $alojamiento->estado = "Activo";
            }
        } 
        return view('statistics.properties')
            ->with('totalActivos', $totalActivos)
            ->with('totalInactivos', $totalInactivos)
            ->with('totalIncompletos', $totalIncompletos)
            ->with('totalAlojamientos', $totalAlojamientos)
            ->with('alojamientos', $alojamientos);
    }

    public function userProperty($id){
        $totalAlojamientos = Alojamiento::where('propietario_id', $id)->count();
        $totalInactivos = Alojamiento::where('estado', 'I')->where('propietario_id', $id)->count();
        $totalActivos = Alojamiento::where('estado', 'A')->where('propietario_id', $id)->count();
        $totalIncompletos = 0;
        $user = User::find($id);
        $alojamientos = Alojamiento::where('propietario_id', '=', $id)->get();
        foreach ($alojamientos as $alojamiento) {
            if($alojamiento->tipo_alquiler = "TO"){
                $alojamiento->tipo_alquiler = "TOTAL";
            }else{
                $alojamiento->tipo_alquiler = "CANT. HUESPED";
            }

            switch ($alojamiento->tipo_alojamiento) {
                case 'AP':
                    $alojamiento->tipo_alojamiento = 'Apartamento';
                    break;
                case 'CS':
                    $alojamiento->tipo_alojamiento = 'Casa';
                    break;
                case 'CB':
                    $alojamiento->tipo_alojamiento = 'Cabaña';
                    break;
                case 'FN':
                    $alojamiento->tipo_alojamiento = 'Finca';
                    break;
                case 'GL':
                    $alojamiento->tipo_alojamiento = 'Glamping';
                    break;
            }

            switch ($alojamiento->notification) {
                case 'NULL':
                    $alojamiento->notification = 'Sin notificar';
                    break;
                case 'first':
                    $alojamiento->notification = '1. Notificación Completar';
                    break;
                case 'second':
                    $alojamiento->notification = '2. Notificación Completar';
                    break;
                case 'third':
                    $alojamiento->notification = 'Notificación Activar';
                    break;
            }
            
            if($alojamiento->estado == "I"){
                if($alojamiento->mapa_locacion == null ||
                    $alojamiento->huespedes == null ||
                    $alojamiento->descripcion == null ||
                    $alojamiento->check_in == null ||
                    $alojamiento->precio_alta == null ||
                    $alojamiento->cuenta_nombre == null){
                        $totalIncompletos++;
                        $alojamiento->carga = "Incompleta";
                        $alojamiento->estado = "Inactivo";
                    }else{
                        $alojamiento->carga = "Completa";
                        $alojamiento->estado = "Inactivo";
                }
            }else{
                $alojamiento->carga = "Completa";
                $alojamiento->estado = "Activo";
            }
        } 
        return view('statistics.property')
            ->with('user', $user)
            ->with('totalActivos', $totalActivos)
            ->with('totalInactivos', $totalInactivos)
            ->with('totalIncompletos', $totalIncompletos)
            ->with('totalAlojamientos', $totalAlojamientos)
            ->with('alojamientos', $alojamientos);
    }
}
