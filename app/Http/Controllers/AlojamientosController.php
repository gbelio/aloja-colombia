<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Storage;
use DB;
use DateTime;
use Mail;
use App\Alojamiento;
use App\AlojamientoCuarto;
use App\AlojamientoCalendario;
use App\AlojamientoFoto;
use App\AlojamientoPedido;
use App\Temporada;
use App\User;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\AlojamientosPedidosController;
use App\Http\Controllers\AlojamientosController;
use App\Http\Controllers\MailerController;
//MERCADOPAGO
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Http\Controllers\Exception;
require_once __DIR__ . '/../../../vendor/autoload.php';
use MercadoPago;

MercadoPago\SDK::setAccessToken(config('services.mercadopago.token'));

class AlojamientosController extends Controller
{
    public function saveImages($id){
        $alojamientoFoto = AlojamientoFoto::where('alojamiento_id', $id)
            ->orderBy('num_foto', 'DESC')->first();
        $alojamientoFoto->archivo = $path;
        $alojamientoFoto->nombre = $nombre;
        $alojamientoFoto->save();
        $alojamientoFoto = AlojamientoFoto::find($id);
        $alojamientoFoto->num_foto = $count;
        $alojamientoFoto->save();
    }

    public function images($id){
        $alojamientoFotos = AlojamientoFoto::where('alojamiento_id', $id)->orderBy('num_foto', 'ASC')->get();
        return view('alojamientos.images')
            ->with('alojamientoFotos', $alojamientoFotos);
    }

    public function imagesUpdate(){
        
        $idArray = explode(",", $_POST['ids']);
        
            $count = 1;
            foreach($idArray as $id){
                $alojamientoFoto = AlojamientoFoto::find($id);
                $alojamientoFoto->num_foto = $count;
                $alojamientoFoto->save();
                $count++;
            }
            return true;
    }

    // Página de búsqueda abierta al público
    public function busqueda(Request $request){
        $opcion = $request->input('opcion');
        $alojamientos = Alojamiento::where('estado', 'A');
        $resultadoLeyenda = '';
        // Búsqueda normal
        if ($opcion == null) {
            $lugar = $request->input('l');
            /* if ($lugar) { */
                $huespedes = $request->input('h');
                $desde = date($request->input('fd'));
                $hasta = date($request->input('fh'));
                $resultadoLeyenda = $lugar;
                $alojamientos = $alojamientos->where(function ($q) use (
                    $lugar
                ) {
                    $q
                        ->where('municipio', 'like', '%' . $lugar . '%')
                        ->orWhere('ciudad', 'like', '%' . $lugar . '%')
                        ->orWhere('departamento', 'like', '%' . $lugar . '%')
                        ->orWhere('barrio', 'like', '%' . $lugar . '%');
                });
                if ($hasta != '' && $desde != '') {
                    $alojamientos = $alojamientos->whereNotExists(function (
                        $q
                    ) use ($desde, $hasta) {
                        $q
                            ->select(DB::raw(1))
                            ->from('alojamientos_calendario')
                            ->whereRaw(
                                'alojamientos_calendario.alojamiento_id = alojamientos.id'
                            )
                            ->whereBetween('fecha', [$desde, $hasta]);
                    });
                }
                if ($huespedes != null) {
                    $alojamientos = $alojamientos->where(
                        'huespedes',
                        '>=',
                        $huespedes
                    );
                }
            /* } */
            // Opción errónea
            /* else {
                $alojamientos = $alojamientos->where('id', 0);
            } */
        }
        // Búsqueda por playa, ciudad, naturaleza o campo
        else {
            if ($opcion == 'pl') {
                $alojamientos = $alojamientos->where('sitio_playa', 1);
                $resultadoLeyenda = 'Cerca de la playa';
            } elseif ($opcion == 'na') {
                $alojamientos = $alojamientos->where(function ($q) {
                    $q
                        ->where('sitio_rio', 1)
                        ->orWhere('sitio_sendero_caminar', 1)
                        ->orWhere('sitio_pesca', 1)
                        ->orWhere('sitio_sendero_ecologico', 1);
                });
                $resultadoLeyenda = 'Conectate con la naturaleza';
            } elseif ($opcion == 'ci') {
                $alojamientos = $alojamientos->where(function ($q) {
                    $q
                        ->where('tipo_alojamiento', 'CS')
                        ->orWhere('tipo_alojamiento', 'AP');
                });
                $resultadoLeyenda = 'Ciudades con encanto';
            } elseif ($opcion == 'ca') {
                $alojamientos = $alojamientos->where(function ($q) {
                    $q
                        ->where('tipo_alojamiento', 'FN')
                        ->orWhere('tipo_alojamiento', 'CB')
                        ->orWhere('tipo_alojamiento', 'CL');
                });
                $resultadoLeyenda = 'Lugares campestres';
            }
            // Opción errónea
            else {
                $alojamientos = $alojamientos->where('id', 0);
            }
        }
        // Filtros adicionales
        $tipo = $request->input('t');
        $pd = str_replace('.', '', $request->input('pd'));
        $ph = str_replace('.', '', $request->input('ph'));
        if ($tipo != null) {
            $alojamientos = $alojamientos->where('tipo_alojamiento', $tipo);
        }
        if ($pd != null) {
            $alojamientos = $alojamientos->where('precio_baja', '>=', $pd);
        }
        if ($ph != null) {
            $alojamientos = $alojamientos->where('precio_baja', '<=', $ph);
        }
        $alojamientos = $alojamientos->paginate(20);
        return View('alojamientos.busqueda')
            ->with('resultadoLeyenda', $resultadoLeyenda)
            ->with('alojamientos', $alojamientos);
    }

    public function search(Request $request)
    {
        $totalAlojamientos = 0;
        $alojamientos = Alojamiento::query()
            ->where('codigo_alojamiento', 'like', '%'.$request->clave.'%')
            ->Paginate(100);
        if (count($alojamientos) != 0){
            $response = 'Estos son los resultados para "'.$request->clave.'".';
            $error="";
            $totalAlojamientos = $alojamientos->count();
        }else{
            $alojamientos=[];
            $response = "Por favor, ingrese un código de propiedad existente.";
            $error= 'Propiedad no encontrada para clave: "'.$request->clave.'".';
        }
        $totalInactivos = 0;
        $totalActivos = 0;
        $totalIncompletos = 0;
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
                $totalInactivos++;
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
                $totalActivos++;
            }
        } 
        return view('statistics.properties')
            ->with('error', $error)
            ->with('response', $response)
            ->with('totalActivos', $totalActivos)
            ->with('totalInactivos', $totalInactivos)
            ->with('totalIncompletos', $totalIncompletos)
            ->with('totalAlojamientos', $totalAlojamientos)
            ->with('alojamientos', $alojamientos);
    }

    public function verificarDisponibilidad(
        $id,
        $alojamiento,
        $desdeParam,
        $hastaParam,
        $huespedesParam,
        &$precioValor,
        &$precioTotal,
        &$precioLimpieza,
        &$tarifaServicio,
        &$deposito,
        &$descuento,
        &$totalGeneral,
        &$diasTotales,
        &$precioTitulo,
        &$descuentoTipo,
        &$descuentoPor,
        &$errorCod,
        &$diasBaja,
        &$diasMedia,
        &$diasAlta,
        &$valorBaja,
        &$valorMedia,
        &$valorAlta) {
        $disponible = true;
        $desde = date($desdeParam);
        $hasta = date('Y-m-d', strtotime('-1 day', strtotime($hastaParam))); // Se resta 1 día al hasta porque no se cobra el día del checkout
        if($alojamiento->tipo_alquiler == 'HU' && $huespedesParam < $alojamiento->huespedes_min){
            $huespedes = $alojamiento->huespedes_min;
        }else{
            $huespedes = $huespedesParam;
        }
        $precioTitulo = 'Noche desde';
        $precioValor = $alojamiento->precio_baja;
        $precioTotal = 0;
        $diasTotales = 0;
        $precioLimpieza = $alojamiento->precio_limpieza;
        $tarifaServicio = 0;
        $descuento = 0;
        $totalGeneral = 0;
        $deposito = 0;
        $errorCod = '';
        $valorBaja = $alojamiento->precio_baja;
        $valorMedia = $alojamiento->precio_media;
        $valorAlta = $alojamiento->precio_alta;
        $descuentoTipo = '';
        $disponibilidad = Alojamiento::where('id', $id)
            ->where('huespedes', '>=', $huespedes)
            ->whereNotExists(function ($q) use ($desde, $hasta) {
                $q
                    ->select(DB::raw(1))
                    ->from('alojamientos_calendario')
                    ->whereRaw(
                        'alojamientos_calendario.alojamiento_id = alojamientos.id'
                    )
                    ->whereBetween('fecha', [$desde, $hasta]);
            })
            ->first();
        if (is_null($disponibilidad)) {
            $disponible = false;
        } else {
            $precioTitulo = 'Precio por noche';
            $diasAltaQuery = Temporada::whereBetween('fecha', [$desde, $hasta])
                ->where('temporada', 'A')
                ->get();
            $diasAlta = count($diasAltaQuery);
            $diasMediaQuery = Temporada::whereBetween('fecha', [$desde, $hasta])
                ->where('temporada', 'M')
                ->get();
            $diasMedia = count($diasMediaQuery);
            $dateDesde = new DateTime($desde);
            $dateHasta = new DateTime($hasta);
            $total = $dateHasta->diff($dateDesde);
            $diasTotales = $total->format('%a') + 1;
            $diasBaja = $diasTotales - $diasMedia - $diasAlta;
            if (
                $alojamiento->alquiler_minimo != null &&
                $alojamiento->alquiler_minimo != 0 &&
                $diasAlta > 0 &&
                $diasTotales < $alojamiento->alquiler_minimo
            ) {
                $disponible = false;
                $errorCod = 'MINIMO';
            } else {
                $precioValor =
                    ($alojamiento->precio_baja * $diasBaja +
                        $alojamiento->precio_media * $diasMedia +
                        $alojamiento->precio_alta * $diasAlta) /
                    $diasTotales;
                if($alojamiento->tipo_alquiler == 'TO'){
                    $precioTotal =
                        ($alojamiento->precio_baja * $diasBaja +
                        $alojamiento->precio_media * $diasMedia +
                        $alojamiento->precio_alta * $diasAlta);
                }else{
                    $precioTotal =
                        ($alojamiento->precio_baja * $diasBaja +
                        $alojamiento->precio_media * $diasMedia +
                        $alojamiento->precio_alta * $diasAlta)*$huespedes;
                }
                $semanas = $diasTotales / 7;
                $quincenas = $diasTotales / 14;
                $meses = $diasTotales / 28;
                if ($alojamiento->descuento_mensual != null && $meses > 1) {
                    $descuento =
                        ($precioTotal * $alojamiento->descuento_mensual) / 100;
                    $descuentoTipo = 'M';
                    $descuentoPor = $alojamiento->descuento_mensual;
                }
                if (
                    $descuento == 0 &&
                    $alojamiento->descuento_quincenal != null &&
                    $quincenas > 1
                ) {
                    $descuento =
                        ($precioTotal * $alojamiento->descuento_quincenal) /
                        100;
                    $descuentoTipo = 'Q';
                    $descuentoPor = $alojamiento->descuento_quincenal;
                }
                if (
                    $descuento == 0 &&
                    $alojamiento->descuento_semanal != null &&
                    $semanas > 1
                ) {
                    $descuento =
                        ($precioTotal * $alojamiento->descuento_semanal) / 100;
                    $descuentoTipo = 'S';
                    $descuentoPor = $alojamiento->descuento_semanal;
                }
                $tarifaServicio =
                    (($precioTotal - $descuento + $precioLimpieza) * 15) / 100;
                $totalGeneral =
                    $precioTotal -
                    $descuento +
                    $precioLimpieza +
                    $tarifaServicio;
                $deposito = $alojamiento->precio_deposito;
            }
        }
        return $disponible;
    }

    public function descuentoFormateado($valor, $tipo)
    {
        $f = number_format($valor, 0, ',', '.') . '% Descuento ';
        switch ($tipo) {
            case 'S':
                $f .= 'Semanal';
                break;

            case 'Q':
                $f .= 'Quincenal';
                break;

            case 'M':
                $f .= 'Mensual';
                break;

            default:
                $f .= 'Mensual';
                break;
        }
        return $f;
    }

    // Página individual abierta al público

    public function show(Request $request, $id){

        // Parámetros
        $opcionParam = $request->input('opcion');
        $huespedesParam = $request->input('h');
        $desdeParam = $request->input('fd');
        $hastaParam = $request->input('fh');
        $alojamiento = Alojamiento::find($id);
        // Si la propiedad no está publicada, sólo debe ser visible por administradores o propietarios
        if (
            $alojamiento->estado == 'I' &&
            !Auth::user()->esAdministrador() &&
            $alojamiento->propietario_id != \Auth::user()->id
        ) {
            abort('403');
        }
        $precioTitulo = 'Noche desde'; // Default para casos de opcion no nula
        $precioValor = $alojamiento->precio_baja; // Default para casos de opcion no nula
        $precioTotal = 0;
        $diasTotales = 0;
        $precioLimpieza = 0;
        $tarifaServicio = 0;
        $descuento = 0;
        $descuentoPor = 0;
        $descuentoTipo = '';
        $totalGeneral = 0;
        $deposito = 0;
        $errorCod = '';
        if ($opcionParam == null && $huespedesParam != null && $desdeParam != null) {
            $disponible = $this->verificarDisponibilidad(
                $id,
                $alojamiento,
                $desdeParam,
                $hastaParam,
                $huespedesParam,
                $precioValor,
                $precioTotal,
                $precioLimpieza,
                $tarifaServicio,
                $deposito,
                $descuento,
                $totalGeneral,
                $diasTotales,
                $precioTitulo,
                $descuentoTipo,
                $descuentoPor,
                $errorCod,
                $diasBaja,
                $diasMedia,
                $diasAlta,
                $valorBaja,
                $valorMedia,
                $valorAlta
            );
        } else {
            $disponible = false;
        }
        $alojamientoFotos = AlojamientoFoto::where(
            'alojamiento_id',
            $alojamiento->id
        )
            ->orderBy('num_foto')
            ->get();
        $mesMax = '01';
        $anioMax = '2021';
        $temporadaMax = Temporada::select(
            DB::raw("LPAD(MONTH(`fecha`)-1, 2, '0') month"),
            DB::raw('YEAR(fecha) year')
        )
            ->where('fecha', '>=', \Carbon\Carbon::now())
            ->orderBy('fecha', 'DESC')
            ->first();
        if (!is_null($temporadaMax)) {
            $mesMax = $temporadaMax->month;
            $anioMax = $temporadaMax->year;
        }
        $bloqueos = AlojamientoCalendario::where('alojamiento_id', $id)
            ->where('fecha', '>=', \Carbon\Carbon::today())
            ->where('bloqueada', 1)
            ->pluck('fecha')
            ->toArray();
        return View('alojamientos.show')
            ->with('bloqueos', $bloqueos)
            ->with('mesMax', $mesMax)
            ->with('anioMax', $anioMax)
            ->with('disponible', $disponible)
            ->with('precioTitulo', $precioTitulo)
            ->with('precioValor', $precioValor)
            ->with('precioTotal', $precioTotal)
            ->with('precioLimpieza', $precioLimpieza)
            ->with('tarifaServicio', $tarifaServicio)
            ->with('deposito', $deposito)
            ->with('descuento', $descuento)
            ->with('totalGeneral', $totalGeneral)
            ->with('diasTotales', $diasTotales)
            ->with(
                'descuentoDescripcion',
                $this->descuentoFormateado($descuentoPor, $descuentoTipo)
            )
            ->with('errorCod', $errorCod)
            ->with('alojamientoFotos', $alojamientoFotos)
            ->with('alojamiento', $alojamiento)
            ->with('cantidadHuespedes', $huespedesParam);
    }

    public function reservar(Request $request, $id){
        // Parámetros
        $huespedesParam = $request->input('h');
        $desdeParam = $request->input('fd');
        $hastaParam = $request->input('fh');
        $alojamiento = Alojamiento::find($id);
        $precioTitulo = '';
        $precioValor = 0;
        $precioTotal = 0;
        $diasTotales = 0;
        $precioLimpieza = 0;
        $tarifaServicio = 0;
        $descuento = 0;
        $descuentoPor = 0;
        $descuentoTipo = '';
        $totalGeneral = 0;
        $deposito = 0;
        $errorCod = '';
        $diasBaja = 0;
        $diasMedia = 0;
        $diasAlta = 0;
        $valorBaja = 0;
        $valorMedia = 0;
        $valorAlta = 0;
        $disponible = $this->verificarDisponibilidad(
            $id,
            $alojamiento,
            $desdeParam,
            $hastaParam,
            $huespedesParam,
            $precioValor,
            $precioTotal,
            $precioLimpieza,
            $tarifaServicio,
            $deposito,
            $descuento,
            $totalGeneral,
            $diasTotales,
            $precioTitulo,
            $descuentoTipo,
            $descuentoPor,
            $errorCod,
            $diasBaja,
            $diasMedia,
            $diasAlta,
            $valorBaja,
            $valorMedia,
            $valorAlta
        );
        $alojamientoFotos = AlojamientoFoto::where(
            'alojamiento_id',
            $alojamiento->id
        )
            ->orderBy('num_foto')
            ->get();
        $codigo_reserva = null;
        if ($disponible && $errorCod == '') {
            $errorCod = 'RESERVADO';
            $tarifaServicioPropietario =
                (($precioTotal - $descuento + $precioLimpieza) * 3) / 100;
            $valorPropietario =
                $precioTotal -
                $descuento +
                $precioLimpieza -
                $tarifaServicioPropietario;
            // Creación de pedido
            $alojamientoPedido = new AlojamientoPedido();
            $alojamientoPedido->alojamiento_id = $id;
            $alojamientoPedido->huesped_id = \Auth::user()->id;
            $alojamientoPedido->alojamiento_id = $id;
            $alojamientoPedido->fecha_pedido = \Carbon\Carbon::now();
            $alojamientoPedido->fecha_desde = $desdeParam;
            $alojamientoPedido->fecha_hasta = $hastaParam;
            $alojamientoPedido->huespedes = $huespedesParam;
            $alojamientoPedido->valor_noche_promedio = $precioValor;
            $alojamientoPedido->valor_noche_promedio_baja = $valorBaja;
            $alojamientoPedido->valor_noche_promedio_media = $valorMedia;
            $alojamientoPedido->valor_noche_promedio_alta = $valorAlta;
            $alojamientoPedido->cantidad_noches = $diasTotales;
            $alojamientoPedido->cantidad_noches_baja = $diasBaja;
            $alojamientoPedido->cantidad_noches_media = $diasMedia;
            $alojamientoPedido->cantidad_noches_alta = $diasAlta;
            $alojamientoPedido->valor_subtotal = $precioTotal;
            $alojamientoPedido->valor_limpieza = $precioLimpieza;
            $alojamientoPedido->valor_servicio = $tarifaServicio;
            $alojamientoPedido->valor_descuento = $descuento;
            $alojamientoPedido->tipo_descuento = $descuentoTipo;
            $alojamientoPedido->por_descuento = $descuentoPor;
            $alojamientoPedido->valor_total = $totalGeneral;
            $alojamientoPedido->valor_comision_servicio = $tarifaServicioPropietario;
            $alojamientoPedido->valor_propietario = $valorPropietario;
            $alojamientoPedido->valor_deposito = $deposito;
            $alojamientoPedido->estado = 'SO';
            $alojamientoPedido->save();
            $alojamientoPedido->codigo_reserva = AlojamientosPedidosController::codigoReserva($alojamientoPedido, $alojamiento);
            $alojamientoPedido->save();
            $codigo_reserva = $alojamientoPedido->codigo_reserva;
            // Bloqueo de días
            $fecha = date($desdeParam);
            $hasta = date($hastaParam);
            while ($fecha < $hasta) {
                $AlojamientoCalendarioNuevo = new AlojamientoCalendario();
                $AlojamientoCalendarioNuevo->alojamiento_id = $id;
                $AlojamientoCalendarioNuevo->bloqueada = 1;
                $AlojamientoCalendarioNuevo->fecha = $fecha;
                $AlojamientoCalendarioNuevo->save();
                $fecha = date('Y-m-d', strtotime('+1 day', strtotime($fecha)));
            }
            // Mails
            MailerController::ownerMailPendingAprobation($alojamiento, $alojamientoPedido);
            MailerController::renterMailPendingAprobation($alojamiento, $alojamientoPedido);
            MailerController::adminMailPendingAprobation($alojamiento, $alojamientoPedido);
        }
        return View('alojamientos.show')
            ->with('disponible', $disponible)
            ->with('precioTitulo', $precioTitulo)
            ->with('precioValor', $precioValor)
            ->with('precioTotal', $precioTotal)
            ->with('precioLimpieza', $precioLimpieza)
            ->with('tarifaServicio', $tarifaServicio)
            ->with('deposito', $deposito)
            ->with('descuento', $descuento)
            ->with('totalGeneral', $totalGeneral)
            ->with('diasTotales', $diasTotales)
            ->with('errorCod', $errorCod)
            ->with('alojamientoFotos', $alojamientoFotos)
            ->with('codigo_reserva', $codigo_reserva)
            ->with('alojamiento', $alojamiento)
            ->with('cantidadHuespedes', $huespedesParam);
    }

    // Indice del backend

    public function index(Request $request){
        $busqueda = $request->input('busqueda');
        $tipoAP = '';
        $tipoCS = '';
        $tipoCB = '';
        $tipoFN = '';
        $tipoGL = '';
        $tipoFH = '';
        $tipoHT = '';
        if (
            $busqueda != null &&
            strpos('apartamento', strtolower($busqueda)) !== false
        ) {
            $tipoAP = 'AP';
        }
        if (
            $busqueda != null &&
            strpos('casa', strtolower($busqueda)) !== false
        ) {
            $tipoCS = 'CS';
        }
        if (
            $busqueda != null &&
            strpos('cabaña', strtolower($busqueda)) !== false
        ) {
            $tipoCB = 'CB';
        }
        if (
            $busqueda != null &&
            strpos('finca', strtolower($busqueda)) !== false
        ) {
            $tipoFN = 'FN';
        }
        if (
            $busqueda != null &&
            strpos('glamping', strtolower($busqueda)) !== false
        ) {
            $tipoGL = 'GL';
        }
        if (
            $busqueda != null &&
            strpos('finca hotel', strtolower($busqueda)) !== false
        ) {
            $tipoFH = 'FH';
        }
        if (
            $busqueda != null &&
            strpos('hotel', strtolower($busqueda)) !== false
        ) {
            $tipoHT = 'HT';
        }

        if (!Auth::user()->esAdministrador()) {
            if ($busqueda == null) {
                $alojamientos = Alojamiento::where(
                    'propietario_id',
                    \Auth::user()->id
                )->paginate(20);
            } else {
                $alojamientos = Alojamiento::where(
                    'propietario_id',
                    \Auth::user()->id
                )
                    ->where(function ($q) use (
                        $busqueda,
                        $tipoAP,
                        $tipoCS,
                        $tipoCB,
                        $tipoFN,
                        $tipoGL,
                        $tipoHT,
                        $tipoFH
                    ) {
                        $q
                            ->where('direccion', 'like', '%' . $busqueda . '%')
                            ->orWhere('ciudad', 'like', '%' . $busqueda . '%')
                            ->orWhere(
                                'departamento',
                                'like',
                                '%' . $busqueda . '%'
                            )
                            ->orWhere('barrio', 'like', '%' . $busqueda . '%')
                            ->orWhere('titulo', 'like', '%' . $busqueda . '%')
                            ->orWhere('id', 'like', '%' . $busqueda . '%')
                            ->orWhere('tipo_alojamiento', $tipoAP)
                            ->orWhere('tipo_alojamiento', $tipoCS)
                            ->orWhere('tipo_alojamiento', $tipoCB)
                            ->orWhere('tipo_alojamiento', $tipoFN)
                            ->orWhere('tipo_alojamiento', $tipoGL)
                            ->orWhere('tipo_alojamiento', $tipoFH)
                            ->orWhere('tipo_alojamiento', $tipoHT);
                    })
                    ->paginate(20);
            }
        } else {
            if ($busqueda == null) {
                $alojamientos = Alojamiento::paginate(20);
            } else {
                $alojamientos = Alojamiento::where(function ($q) use (
                    $busqueda,
                    $tipoAP,
                    $tipoCS,
                    $tipoCB,
                    $tipoFN,
                    $tipoGL,
                    $tipoHT,
                    $tipoFH
                ) {
                    $q
                        ->where('direccion', 'like', '%' . $busqueda . '%')

                        ->orWhere('ciudad', 'like', '%' . $busqueda . '%')

                        ->orWhere('departamento', 'like', '%' . $busqueda . '%')

                        ->orWhere('barrio', 'like', '%' . $busqueda . '%')

                        ->orWhere('titulo', 'like', '%' . $busqueda . '%')

                        ->orWhere('id', 'like', '%' . $busqueda . '%')

                        ->orWhere('tipo_alojamiento', $tipoAP)

                        ->orWhere('tipo_alojamiento', $tipoCS)

                        ->orWhere('tipo_alojamiento', $tipoCB)

                        ->orWhere('tipo_alojamiento', $tipoFN)

                        ->orWhere('tipo_alojamiento', $tipoGL)

                        ->orWhere('tipo_alojamiento', $tipoFH)

                        ->orWhere('tipo_alojamiento', $tipoHT);
                })->paginate(20);
            }
        }
        return View('alojamientos.index')->with('alojamientos', $alojamientos);
    }

    public function tieneAcceso($alojamiento){
        if (!Auth::user()->esAdministrador()) {
            if ($alojamiento->propietario_id != \Auth::user()->id) {
                abort('403');
            }
        }
    }

    public function create(){
        $alojamiento = new Alojamiento();
        $propietarios = User::orderBy('name')->get();
        return View('alojamientos.save')
            ->with('alojamiento', $alojamiento)
            ->with('propietarios', $propietarios)
            ->with('method', 'POST');
    }

    public function store(Request $request){
        
        if ($request->lat == null || $request->lng == null){
            return Redirect::back()->withErrors(['msg' => 'Busque una ubicación correcta']);
        }
        if ($request->ciudad == null &&  $request->departamento == null){
            return Redirect::back()->withErrors(['msg' => 'Primero busque la ubicación, luego arrastre el puntero para más precisión']);
        }
        $alojamiento = new Alojamiento();
        if (!Auth::user()->esAdministrador()) {
            $alojamiento->propietario_id = Auth::user()->id;
        } else {
            $alojamiento->propietario_id = $request->propietario_id;
            $alojamiento->destacada = $request->has('destacada');
        }
        $alojamiento->tipo_alojamiento = $request->tipo_alojamiento;
        $alojamiento->direccion = $request->direccion;
        $alojamiento->barrio = $request->barrio;
        $alojamiento->ciudad = $request->ciudad;
        $alojamiento->municipio = $request->municipio;
        $alojamiento->departamento = $request->departamento;
        $alojamiento->mapa_locacion = $request->location;
        $alojamiento->mapa_latitud = $request->lat;
        $alojamiento->mapa_longitud = $request->lng;
        $alojamiento->save();
        AlojamientosController::codigoAlojamiento($alojamiento);
        if ($request->navegacion == 'save') {
            return Redirect::to('/alojamientos')->with(
                'notice',
                'El alojamiento ha sido creado con éxito.'
            );
        } elseif ($request->navegacion == 'sig') {
            return Redirect::to(
                '/alojamientos/' . $alojamiento->id . '/edit?paso=2'
            );
        }
    }

    public function edit(Request $request, $id){
        $alojamiento = Alojamiento::find($id);
        $this->tieneAcceso($alojamiento);
        $propietarios = User::orderBy('name')->get();
        $alojamientoCuartos = AlojamientoCuarto::where('alojamiento_id', $id)
            ->orderBy('num_cuarto')
            ->get();
        $temporadaMedia = Temporada::where(
            'fecha',
            '>=',
            \Carbon\Carbon::today()
        )
            ->where('temporada', 'M')
            ->pluck('fecha')
            ->toArray();
        $temporadaAlta = Temporada::where(
            'fecha',
            '>=',
            \Carbon\Carbon::today()
        )
            ->where('temporada', 'A')
            ->pluck('fecha')
            ->toArray();
        $mesMax = '01';
        $anioMax = '2021';
        $temporadaMax = Temporada::select(
            DB::raw("LPAD(MONTH(`fecha`)-1, 2, '0') month"),
            DB::raw('YEAR(fecha) year')
        )
            ->where('fecha', '>=', \Carbon\Carbon::now())
            ->orderBy('fecha', 'DESC')
            ->first();
        if (!is_null($temporadaMax)) {
            $mesMax = $temporadaMax->month;
            $anioMax = $temporadaMax->year;
        }
        $bloqueos = AlojamientoCalendario::where('alojamiento_id', $id)
            ->where('fecha', '>=', \Carbon\Carbon::today())
            ->where('bloqueada', 1)
            ->pluck('fecha')
            ->toArray();
        return View('alojamientos.save')
            ->with('propietarios', $propietarios)
            ->with('alojamiento', $alojamiento)
            ->with('alojamientoCuartos', $alojamientoCuartos)
            ->with('temporadaMedia', $temporadaMedia)
            ->with('temporadaAlta', $temporadaAlta)
            ->with('bloqueos', $bloqueos)
            ->with('mesMax', $mesMax)
            ->with('anioMax', $anioMax)
            ->with('method', 'PUT');
    }

    public function update(Request $request, $id){
        $alojamiento = Alojamiento::find($id);
        $paso = $request->paso;
        if ($paso == 1) {
            if (Auth::user()->esAdministrador()) {
                $alojamiento->propietario_id = $request->propietario_id;
                $alojamiento->destacada = $request->has('destacada');
            }
            $alojamiento->tipo_alojamiento = $request->tipo_alojamiento;
            $alojamiento->direccion = $request->direccion;
            $alojamiento->barrio = $request->barrio;
            $alojamiento->ciudad = $request->ciudad;
            $alojamiento->municipio = $request->municipio;
            $alojamiento->departamento = $request->departamento;
            // http://www.expertphp.in/article/autocomplete-search-address-form-using-google-map-and-get-data-into-form-example
            $alojamiento->mapa_locacion = $request->location;
            $alojamiento->mapa_latitud = $request->lat;
            $alojamiento->mapa_longitud = $request->lng;
        } elseif ($paso == 2) {
            $alojamiento->huespedes = $request->huespedes;
            $alojamiento->cuartos = $request->cuartos;
            $alojamiento->banios_completos = $request->banios_completos;
            $alojamiento->banios_sin_ducha = $request->banios_sin_ducha;
        } elseif ($paso == 3) {
            $alojamiento->servicio_wifi = $request->has('servicio_wifi');
            $alojamiento->servicio_tv = $request->has('servicio_tv');
            $alojamiento->servicio_cable = $request->has('servicio_cable');
            $alojamiento->servicio_sonido = $request->has('servicio_sonido');
            $alojamiento->servicio_aa = $request->has('servicio_aa');
            $alojamiento->servicio_vent = $request->has('servicio_vent');
            $alojamiento->servicio_agua = $request->has('servicio_agua');
            $alojamiento->servicio_lav = $request->has('servicio_lav');
            $alojamiento->servicio_sec = $request->has('servicio_sec');
            $alojamiento->servicio_sec_pelo = $request->has(
                'servicio_sec_pelo'
            );
            $alojamiento->servicio_plancha = $request->has('servicio_plancha');
            $alojamiento->servicio_toallas = $request->has('servicio_toallas');
            $alojamiento->servicio_sabanas = $request->has('servicio_sabanas');
            $alojamiento->servicio_cocina = $request->has('servicio_cocina');
            $alojamiento->servicio_nevera = $request->has('servicio_nevera');
            $alojamiento->servicio_utensillos = $request->has(
                'servicio_utensillos'
            );
            $alojamiento->servicio_horno_elec = $request->has(
                'servicio_horno_elec'
            );
            $alojamiento->servicio_micro = $request->has('servicio_micro');
            $alojamiento->servicio_piscina = $request->has('servicio_piscina');
            $alojamiento->servicio_jacuzzi = $request->has('servicio_jacuzzi');
            $alojamiento->servicio_asoleadoras = $request->has(
                'servicio_asoleadoras'
            );
            $alojamiento->servicio_sombrillas = $request->has(
                'servicio_sombrillas'
            );
            $alojamiento->servicio_kiosko = $request->has('servicio_kiosko');
            $alojamiento->servicio_hamacas = $request->has('servicio_hamacas');
            $alojamiento->servicio_bbq = $request->has('servicio_bbq');
            $alojamiento->servicio_horno_len = $request->has(
                'servicio_horno_len'
            );
            $alojamiento->servicio_estufa_len = $request->has(
                'servicio_estufa_len'
            );
            $alojamiento->servicio_verdes = $request->has('servicio_verdes');
            $alojamiento->servicio_gimnasio = $request->has(
                'servicio_gimnasio'
            );
            $alojamiento->servicio_chimenea = $request->has(
                'servicio_chimenea'
            );
            $alojamiento->servicio_balcon = $request->has('servicio_balcon');
            $alojamiento->servicio_ascensor = $request->has(
                'servicio_ascensor'
            );
            $alojamiento->servicio_parqueadero = $request->has(
                'servicio_parqueadero'
            );
            $alojamiento->servicio_cancha_futbol = $request->has(
                'servicio_cancha_futbol'
            );
            $alojamiento->servicio_billar = $request->has('servicio_billar');
            $alojamiento->servicio_ping_pong = $request->has(
                'servicio_ping_pong'
            );
            $alojamiento->servicio_tejo = $request->has('servicio_tejo');
            $alojamiento->servicio_rana = $request->has('servicio_rana');
            $alojamiento->servicio_juegos_mesa = $request->has(
                'servicio_juegos_mesa'
            );
            $alojamiento->servicio_extintor = $request->has(
                'servicio_extintor'
            );
            $alojamiento->servicio_humo = $request->has('servicio_humo');
            $alojamiento->servicio_alarma = $request->has('servicio_alarma');
            $alojamiento->servicio_botiquin = $request->has(
                'servicio_botiquin'
            );
            $alojamiento->servicio_monoxido = $request->has(
                'servicio_monoxido'
            );
            $alojamiento->servicio_caja_seg = $request->has(
                'servicio_caja_seg'
            );
            $alojamiento->servicio_desayuno = $request->has(
                'servicio_desayuno'
            );
            $alojamiento->servicio_almuerzo = $request->has(
                'servicio_almuerzo'
            );
            $alojamiento->servicio_cena = $request->has('servicio_cena');
            $alojamiento->servicio_adicional_nombre_1 =
                $request->servicio_adicional_nombre_1;
            $alojamiento->servicio_adicional_nombre_2 =
                $request->servicio_adicional_nombre_2;
            $alojamiento->servicio_adicional_nombre_3 =
                $request->servicio_adicional_nombre_3;
            $alojamiento->servicio_adicional_nombre_4 =
                $request->servicio_adicional_nombre_4;
            $alojamiento->servicio_adicional_nombre_5 =
                $request->servicio_adicional_nombre_5;
        } elseif ($paso == 4) {
            $alojamiento->sitio_playa = $request->has('sitio_playa');
            $alojamiento->sitio_playa_distancia =
                $request->sitio_playa_distancia;
            $alojamiento->sitio_rio = $request->has('sitio_rio');
            $alojamiento->sitio_rio_distancia = $request->sitio_rio_distancia;
            $alojamiento->sitio_parque = $request->has('sitio_parque');
            $alojamiento->sitio_parque_distancia =
                $request->sitio_parque_distancia;
            $alojamiento->sitio_sendero_caminar = $request->has(
                'sitio_sendero_caminar'
            );
            $alojamiento->sitio_sendero_caminar_distancia =
                $request->sitio_sendero_caminar_distancia;
            $alojamiento->sitio_sendero_ecologico = $request->has(
                'sitio_sendero_ecologico'
            );
            $alojamiento->sitio_sendero_ecologico_distancia =
                $request->sitio_sendero_ecologico_distancia;
            $alojamiento->sitio_ruta_bici = $request->has('sitio_ruta_bici');
            $alojamiento->sitio_ruta_bici_distancia =
                $request->sitio_ruta_bici_distancia;
            $alojamiento->sitio_act_tur = $request->has('sitio_act_tur');
            $alojamiento->sitio_act_tur_detalle_1 =
                $request->sitio_act_tur_detalle_1;
            $alojamiento->sitio_act_tur_detalle_2 =
                $request->sitio_act_tur_detalle_2;
            $alojamiento->sitio_act_tur_detalle_3 =
                $request->sitio_act_tur_detalle_3;
            $alojamiento->sitio_act_tur_detalle_4 =
                $request->sitio_act_tur_detalle_4;
            $alojamiento->sitio_act_tur_detalle_5 =
                $request->sitio_act_tur_detalle_5;
            $alojamiento->sitio_parque_tem = $request->has('sitio_parque_tem');
            $alojamiento->sitio_parque_tem_distancia =
                $request->sitio_parque_tem_distancia;
            $alojamiento->sitio_parque_tem_nombre =
                $request->sitio_parque_tem_nombre;
            $alojamiento->sitio_parque_div = $request->has('sitio_parque_div');
            $alojamiento->sitio_parque_div_nombre =
                $request->sitio_parque_div_nombre;
            $alojamiento->sitio_parque_div_distancia =
                $request->sitio_parque_div_distancia;
            $alojamiento->sitio_parque_acua = $request->has(
                'sitio_parque_acua'
            );
            $alojamiento->sitio_parque_acua_nombre =
                $request->sitio_parque_acua_nombre;
            $alojamiento->sitio_parque_acua_distancia =
                $request->sitio_parque_acua_distancia;
            $alojamiento->sitio_pesca = $request->has('sitio_pesca');
            $alojamiento->sitio_pesca_distancia =
                $request->sitio_pesca_distancia;
            $alojamiento->sitio_act_dep = $request->has('sitio_act_dep');
            $alojamiento->sitio_act_dep_detalle_1 =
                $request->sitio_act_dep_detalle_1;
            $alojamiento->sitio_act_dep_detalle_2 =
                $request->sitio_act_dep_detalle_2;
            $alojamiento->sitio_act_dep_detalle_3 =
                $request->sitio_act_dep_detalle_3;
            $alojamiento->sitio_act_dep_detalle_4 =
                $request->sitio_act_dep_detalle_4;
            $alojamiento->sitio_act_dep_detalle_5 =
                $request->sitio_act_dep_detalle_5;
            $alojamiento->sitio_sup = $request->has('sitio_sup');
            $alojamiento->sitio_sup_nombre = $request->sitio_sup_nombre;
            $alojamiento->sitio_sup_distancia = $request->sitio_sup_distancia;
            $alojamiento->sitio_drog = $request->has('sitio_drog');
            $alojamiento->sitio_drog_nombre = $request->sitio_drog_nombre;
            $alojamiento->sitio_drog_distancia = $request->sitio_drog_distancia;
            $alojamiento->sitio_centro_com = $request->has('sitio_centro_com');
            $alojamiento->sitio_centro_com_nombre_1 =
                $request->sitio_centro_com_nombre_1;
            $alojamiento->sitio_centro_com_nombre_2 =
                $request->sitio_centro_com_nombre_2;
            $alojamiento->sitio_centro_com_nombre_3 =
                $request->sitio_centro_com_nombre_3;
            $alojamiento->sitio_centro_com_nombre_4 =
                $request->sitio_centro_com_nombre_4;
            $alojamiento->sitio_centro_com_nombre_5 =
                $request->sitio_centro_com_nombre_5;
            $alojamiento->sitio_rest = $request->has('sitio_rest');
            $alojamiento->sitio_rest_nombre_1 = $request->sitio_rest_nombre_1;
            $alojamiento->sitio_rest_nombre_2 = $request->sitio_rest_nombre_2;
            $alojamiento->sitio_rest_nombre_3 = $request->sitio_rest_nombre_3;
            $alojamiento->sitio_rest_nombre_4 = $request->sitio_rest_nombre_4;
            $alojamiento->sitio_rest_nombre_5 = $request->sitio_rest_nombre_5;
            $alojamiento->sitio_gimnasio = $request->has('sitio_gimnasio');
            $alojamiento->sitio_gimnasio_distancia =
                $request->sitio_gimnasio_distancia;
            $alojamiento->sitio_iglesia = $request->has('sitio_iglesia');
            $alojamiento->sitio_iglesia_distancia =
                $request->sitio_iglesia_distancia;
            $alojamiento->sitio_hospital = $request->has('sitio_hospital');
            $alojamiento->sitio_hospital_nombre =
                $request->sitio_hospital_nombre;
            $alojamiento->sitio_hospital_distancia =
                $request->sitio_hospital_distancia;
            $alojamiento->sitio_transporte = $request->has('sitio_transporte');
            $alojamiento->sitio_adicional_nombre_1 =
                $request->sitio_adicional_nombre_1;
            $alojamiento->sitio_adicional_nombre_2 =
                $request->sitio_adicional_nombre_2;
            $alojamiento->sitio_adicional_nombre_3 =
                $request->sitio_adicional_nombre_3;
            $alojamiento->sitio_adicional_nombre_4 =
                $request->sitio_adicional_nombre_4;
            $alojamiento->sitio_adicional_nombre_5 =
                $request->sitio_adicional_nombre_5;
        }
        if ($paso == 6) {
            $alojamiento->descripcion = $request->descripcion;
            $alojamiento->zona = $request->zona;
            $alojamiento->titulo = $request->titulo;
        }
        if ($paso == 7) {
            $alojamiento->regla_mascotas = $request->has('regla_mascotas');
            $alojamiento->regla_fumadores = $request->has('regla_fumadores');
            $alojamiento->regla_fiestas = $request->has('regla_fiestas');
            $alojamiento->regla_adicional_1 = $request->regla_adicional_1;
            $alojamiento->regla_adicional_2 = $request->regla_adicional_2;
            $alojamiento->regla_adicional_3 = $request->regla_adicional_3;
            $alojamiento->regla_adicional_4 = $request->regla_adicional_4;
            $alojamiento->regla_adicional_5 = $request->regla_adicional_5;
            $alojamiento->check_in = $request->check_in;
            $alojamiento->check_out = $request->check_out;
        }
        if ($paso == 8) {
            $alojamiento->precio_alta = str_replace(
                '.',
                '',
                $request->precio_alta
            );
            $alojamiento->precio_baja = str_replace(
                '.',
                '',
                $request->precio_baja
            );
            $alojamiento->precio_media = str_replace(
                '.',
                '',
                $request->precio_media
            );
            if ($request->precio_limpieza != null) {
                $alojamiento->precio_limpieza = str_replace(
                    '.',
                    '',
                    $request->precio_limpieza
                );
            } else {
                $alojamiento->precio_limpieza = 0;
            }
            if (
                $request->has('deposito') == true &&
                $request->precio_deposito != null
            ) {
                $alojamiento->precio_deposito = str_replace(
                    '.',
                    '',
                    $request->precio_deposito
                );
            } else {
                $alojamiento->precio_deposito = null;
            }
            if ($request->tipo_alquiler == 'HU') {
                $alojamiento->huespedes_min = $request->huespedes_min;
                $alojamiento->tipo_alquiler = $request->tipo_alquiler;
            }else{
                $alojamiento->tipo_alquiler = 'TO';
                $alojamiento->huespedes_min = 1;
            }
            $alojamiento->alquiler_minimo = $request->alquiler_minimo;
            $alojamiento->descuento_semanal = $request->descuento_semanal;
            $alojamiento->descuento_quincenal = $request->descuento_quincenal;
            $alojamiento->descuento_mensual = $request->descuento_mensual;
            $alojamiento->particularidades_fechas =
                $request->particularidades_fechas;
        }
        if ($paso == 9) {
            $alojamiento->politica_cancelacion = $request->politica_cancelacion;
            if ($request->bloqueosBajas != null) {
                $bloqueosBajas = explode(',', $request->bloqueosBajas);
                foreach ($bloqueosBajas as $fecha) {
                    $AlojamientoCalendarioBaja = AlojamientoCalendario::where(
                        'fecha',
                        $fecha
                    )
                        ->where('alojamiento_id', $id)
                        ->first();
                    if (!is_null($AlojamientoCalendarioBaja)) {
                        $AlojamientoCalendarioBaja->delete();
                    }
                }
            }
            if ($request->bloqueosAltas != null) {
                $bloqueosAltas = explode(',', $request->bloqueosAltas);
                foreach ($bloqueosAltas as $fecha) {
                    $AlojamientoCalendarioNuevo = new AlojamientoCalendario();
                    $AlojamientoCalendarioNuevo->alojamiento_id = $id;
                    $AlojamientoCalendarioNuevo->bloqueada = 1;
                    $AlojamientoCalendarioNuevo->fecha = $fecha;
                    $AlojamientoCalendarioNuevo->save();
                }
            }
        }
        if ($paso == 10) {
            $alojamiento->cuenta_nombre = $request->cuenta_nombre;
            $alojamiento->cuenta_doc = $request->cuenta_doc;
            $alojamiento->cuenta_banco = $request->cuenta_banco;
            $alojamiento->cuenta_tipo = $request->cuenta_tipo;
            $alojamiento->cuenta_nro = $request->cuenta_nro;
        }
        if ($paso == 11 && $request->navegacion != 'ant') {
            if ($request->navegacion == 'activar') {
                $alojamiento->estado = 'A';
                $alojamiento->save();
                return Redirect::to('/alojamientos')->with(
                    'notice',
                    'El alojamiento ha sido publicado con éxito.'
                );
            } else {
                $alojamiento->estado = 'I';
                $alojamiento->save();
                return Redirect::to('/alojamientos')->with(
                    'notice',
                    'El alojamiento ha sido inactivado con éxito.'
                );
            }
        }
        AlojamientosController::codigoAlojamiento($alojamiento);
        $alojamiento->save();
        if ($paso == 2) {
            for ($iCuarto = 1; $iCuarto <= $alojamiento->cuartos; $iCuarto++) {
                $alojamientoCuarto = AlojamientoCuarto::where(
                    'alojamiento_id',
                    $id
                )
                    ->where('num_cuarto', $iCuarto)
                    ->first();
                if (is_null($alojamientoCuarto)) {
                    $alojamientoCuarto = new AlojamientoCuarto();
                    $alojamientoCuarto->alojamiento_id = $id;
                    $alojamientoCuarto->num_cuarto = $iCuarto;
                }
                $alojamientoCuarto->camas_king = $request->camas_king[$iCuarto];
                $alojamientoCuarto->camas_queen =
                    $request->camas_queen[$iCuarto];
                $alojamientoCuarto->camas_doble =
                    $request->camas_doble[$iCuarto];
                $alojamientoCuarto->camas_semi_doble =
                    $request->camas_semi_doble[$iCuarto];
                $alojamientoCuarto->camas_sencilla =
                    $request->camas_sencilla[$iCuarto];
                $alojamientoCuarto->camas_camarote =
                    $request->camas_camarote[$iCuarto];
                $alojamientoCuarto->camas_auxiliar =
                    $request->camas_auxiliar[$iCuarto];
                $alojamientoCuarto->camas_sofa = $request->camas_sofa[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_1 =
                    $request->camas_otro_tipo_1[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_2 =
                    $request->camas_otro_tipo_2[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_3 =
                    $request->camas_otro_tipo_3[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_4 =
                    $request->camas_otro_tipo_4[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_5 =
                    $request->camas_otro_tipo_5[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_nombre_1 =
                    $request->camas_otro_tipo_nombre_1[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_nombre_2 =
                    $request->camas_otro_tipo_nombre_2[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_nombre_3 =
                    $request->camas_otro_tipo_nombre_3[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_nombre_4 =
                    $request->camas_otro_tipo_nombre_4[$iCuarto];
                $alojamientoCuarto->camas_otro_tipo_nombre_5 =
                    $request->camas_otro_tipo_nombre_5[$iCuarto];
                $alojamientoCuarto->save();
            }
            $alojamientoCuartosBorrar = AlojamientoCuarto::where(
                'alojamiento_id',
                $id
            )
                ->where('num_cuarto', '>', $alojamiento->cuartos)
                ->where('num_cuarto', '<>', '31')
                ->get();
            foreach ($alojamientoCuartosBorrar as $alojamientoCuartoBorrar) {
                $alojamientoCuartoBorrar->delete();
            }
            // Cuarto 31 reservado para espacios compartidos
            $alojamientoCuarto = AlojamientoCuarto::where('alojamiento_id', $id)
                ->where('num_cuarto', '31')
                ->first();
            if (is_null($alojamientoCuarto)) {
                $alojamientoCuarto = new AlojamientoCuarto();
                $alojamientoCuarto->alojamiento_id = $id;
                $alojamientoCuarto->num_cuarto = 31;
            }
            $alojamientoCuarto->camas_king = $request->camas_king[31];
            $alojamientoCuarto->camas_queen = $request->camas_queen[31];
            $alojamientoCuarto->camas_doble = $request->camas_doble[31];
            $alojamientoCuarto->camas_semi_doble = $request->camas_semi_doble[31];
            $alojamientoCuarto->camas_sencilla = $request->camas_sencilla[31];
            $alojamientoCuarto->camas_camarote = $request->camas_camarote[31];
            $alojamientoCuarto->camas_auxiliar = $request->camas_auxiliar[31];
            $alojamientoCuarto->camas_sofa = $request->camas_sofa[31];
            $alojamientoCuarto->camas_otro_tipo_1 =
                $request->camas_otro_tipo_1[31];
            $alojamientoCuarto->camas_otro_tipo_2 =
                $request->camas_otro_tipo_2[31];
            $alojamientoCuarto->camas_otro_tipo_3 =
                $request->camas_otro_tipo_3[31];
            $alojamientoCuarto->camas_otro_tipo_4 =
                $request->camas_otro_tipo_4[31];
            $alojamientoCuarto->camas_otro_tipo_5 =
                $request->camas_otro_tipo_5[31];
            $alojamientoCuarto->camas_otro_tipo_nombre_1 =
                $request->camas_otro_tipo_nombre_1[31];
            $alojamientoCuarto->camas_otro_tipo_nombre_2 =
                $request->camas_otro_tipo_nombre_2[31];
            $alojamientoCuarto->camas_otro_tipo_nombre_3 =
                $request->camas_otro_tipo_nombre_3[31];
            $alojamientoCuarto->camas_otro_tipo_nombre_4 =
                $request->camas_otro_tipo_nombre_4[31];
            $alojamientoCuarto->camas_otro_tipo_nombre_5 =
                $request->camas_otro_tipo_nombre_5[31];
            $alojamientoCuarto->save();
        }
        if ($paso == 5) {
            for ($iFoto = 1; $iFoto <= 30; $iFoto++) {
                if (
                    $request->hasFile('foto' . $iFoto) &&
                    $request->file('foto' . $iFoto)->isValid()
                ) {
                    $file = $request->file('foto' . $iFoto);
                    $nombre = $file->getClientOriginalName();
                    $alojamientoFoto = AlojamientoFoto::where(
                        'alojamiento_id',
                        $id
                    )
                        ->where('num_foto', $iFoto)
                        ->first();
                    if (is_null($alojamientoFoto)) {
                        $alojamientoFoto = new AlojamientoFoto();
                        $alojamientoFoto->alojamiento_id = $id;
                        $alojamientoFoto->num_foto = $iFoto;
                    } else {
                        // Elimino la anterior si existe
                        if ($alojamientoFoto->archivo != null) {
                            $archivoEliminar = $alojamientoFoto->archivo;
                            if (
                                Storage::disk('public_uploads')->exists(
                                    $archivoEliminar
                                )
                            ) {
                                Storage::disk('public_uploads')->delete(
                                    $archivoEliminar
                                );
                            }
                        }
                    }
                    $path = $request->file('foto' . $iFoto)->storeAs(
                        'propiedades/' . $id . '/foto' . $iFoto,
                        $nombre,
                        'public_uploads'
                    );
                    $alojamientoFoto->archivo = $path;
                    $alojamientoFoto->nombre = $nombre;
                    $alojamientoFoto->save();
                }
                // Si no hay documento nuevo y eligió borrarlo
                else {
                    $alojamientoFoto = AlojamientoFoto::where(
                        'alojamiento_id',
                        $id
                    )
                        ->where('num_foto', $iFoto)
                        ->first();
                    if (!is_null($alojamientoFoto)) {
                        if ($request->input('fotoBorrar' . $iFoto) == 'SI') {
                            if ($alojamientoFoto->archivo != null) {
                                $archivoEliminar = $alojamientoFoto->archivo;
                                if (
                                    Storage::disk('public_uploads')->exists(
                                        $archivoEliminar
                                    )
                                ) {
                                    Storage::disk('public_uploads')->delete(
                                        $archivoEliminar
                                    );
                                }
                            }
                            $alojamientoFoto->delete();
                        }
                    }
                }
            }
        }
        if ($request->navegacion == 'save') {
            
            return Redirect::to('/alojamientos')->with(
                'notice',
                'El alojamiento ha sido editado con éxito.'
            );
        } else {
            if ($request->navegacion == 'sig') {
                $paso++;
            } else {
                $paso--;
            }
            if($request->navegacion == 'saveImages'){
                $lastPic = AlojamientoFoto::where('alojamiento_id', $id)
                    ->orderBy('num_foto', 'DESC')->first();
                    $iFoto = 1;
                if(!is_null($lastPic)){
                    $iFoto = $lastPic->num_foto + 1;
                }
                $id = $request->alojamiento_id;
                foreach ($request->file() as $files){
                    foreach($files as $image){
                        if($image->getClientMimeType() == 'image/jpeg' || $image->getClientMimeType() == 'image/png' || $image->getClientMimeType() == 'image/gif'){
                            $alojamientoFoto = new AlojamientoFoto();
                            $alojamientoFoto->alojamiento_id = $id;
                            $alojamientoFoto->num_foto = $iFoto;
                            $nombre = $image->getClientOriginalName();
                            $path = $image->storeAs(
                                'propiedades/' . $id . '/foto' . $iFoto,
                                $nombre,
                                'public_uploads'
                            );
                            $alojamientoFoto->archivo = $path;
                            $alojamientoFoto->nombre = $nombre;
                            $alojamientoFoto->save();
                            $iFoto++;
                        }else{
                            $formatError = 'Solo serán guardados los archivos que sean imagenes';
                        }
                    }
                };
                if(isset($formatError)){
                    return Redirect::back()->withErrors($formatError);
                }
                return Redirect::back();
            }
            return Redirect::to(
                '/alojamientos/' . $alojamiento->id . '/edit?paso=' . $paso
            );
        }
    }

    public function destroy($id){
        $alojamiento = Alojamiento::find($id);
        $mensaje = 'El alojamiento ha sido eliminado con éxito.';
        $tipo = 'notice';
        try {
            DB::table('alojamientos_cuartos')
                ->where('alojamiento_id', $id)
                ->delete();
            DB::table('alojamientos_fotos')
                ->where('alojamiento_id', $id)
                ->delete();
            DB::table('alojamientos_calendario')
                ->where('alojamiento_id', $id)
                ->delete();
            DB::table('alojamientos_pedidos')
                ->where('alojamiento_id', $id)
                ->delete();
            $alojamiento->delete();
            Storage::disk('public_uploads')->deleteDirectory(
                'propiedades/' . $id
            );
        } catch (\Illuminate\Database\QueryException $e) {
            $mensaje =
                'El alojamiento no puede eliminarse porque existen datos relacionados al mismo. Elimine primero los datos relacionados. Luego vuelva a intentar.';
            $tipo = 'error';
        }
        return Redirect::back()->with($tipo, $mensaje);
    }

    public function activar(Request $request, $id){
        $alojamiento = Alojamiento::find($id);
        $alojamientoFotoPrincipal = AlojamientoFoto::where(
            'alojamiento_id',
            $alojamiento->id
        )
            ->where('num_foto', 1)
            ->first();
        $fotoPrincipal = false;
        if (!is_null($alojamientoFotoPrincipal)) {
            $fotoPrincipal = true;
        }
        if (
            $alojamiento->direccion == null ||
            $alojamiento->huespedes == null ||
            !$fotoPrincipal ||
            $alojamiento->descripcion == null ||
            $alojamiento->check_in == null ||
            $alojamiento->precio_alta == null ||
            $alojamiento->cuenta_nombre == null
        ) {
            return Redirect::back()->with(
                'error',
                'No se puede activar el alojamiento. Falta completar información obligatoria.'
            );
        }
        $alojamiento->estado = 'A';
        $alojamiento->save();
        return Redirect::back()->with(
            'notice',
            'El alojamiento ha sido activado'
        );
    }

    public function inactivar(Request $request, $id){
        $alojamiento = Alojamiento::find($id);
        $alojamiento->estado = 'I';
        $alojamiento->notification = null;
        $alojamiento->save();
        return Redirect::back()->with(
            'notice',
            'El alojamiento ha sido inactivado'
        );
    }

    static function codigoAlojamiento($alojamiento){
        //CREACIÓN DE CÓDIGO DE ALOJAMIENTO
        $formatID = str_pad($alojamiento->id, 4, "0", STR_PAD_LEFT);
        $formatCD = "";
        if($alojamiento->ciudad){
            for ($i=0; $i < 3; $i++) { 
                $formatCD .= $alojamiento->ciudad[$i];
            }
        }else{
            for ($i=0; $i < 3; $i++) { 
                $formatCD .= $alojamiento->departamento[$i];
            }
        }
        $alojamiento->codigo_alojamiento = strtoupper(
            $alojamiento->tipo_alojamiento . 
            $alojamiento->politica_cancelacion . $formatID . $formatCD);
        $alojamiento->save();
    }
}
