@extends('layouts.app')
@section('content')
    <div class="__data">
        <h1 style="text-align: center;">RESERVAS</h1>
        <br>
        <br>
        <div class="__cards" style="display: flex;justify-content: space-evenly;">
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Alojamientos registrados</h2>
                <p>{{$alojamientosTotal}}</p>
            </div>
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Reservas abonadas</h2>
                <p>{{$alojamientosPedidosTotal}}</p>
            </div>
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Pendientes de pago</h2>
                <p>{{$alojamientoPedidoPendientesPago}}</p>
            </div>
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Reservas rechazadas</h2>
                <p>{{$alojamientosRechazadosTotal}}</p>
            </div>
            @isset($usuariosTotal)
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Usuarios registrados</h2>
                <p>{{$usuariosTotal}}</p>
            </div>
            @endisset
        </div>
    </div>
        <br>
        <br>
        <div class="__tabla" style="max-width: 90vw; margin:auto;">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th scope="col">Huespedes</th>
                        <th scope="col">Llegada</th>
                        <th scope="col">Salida</th>
                        <th scope="col">Reservado</th>
                        <th scope="col">Anuncio</th>
                        <th scope="col">Cód. Reserva</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alojamientosPedidos as $alojamientoPedido)
                    @switch($alojamientoPedido->estado)
                        @case('SO')
                            @php $alojamientoPedido->estado = 'Solicitada';@endphp
                            @break
                        @case('CO')
                            @php $alojamientoPedido->estado = 'Confirmada';@endphp
                        @break
                        @case('RE')
                            @php $alojamientoPedido->estado = 'Rechazada';@endphp
                        @break
                        @case('PP')
                            @php $alojamientoPedido->estado = 'Pago Parcial';@endphp
                        @break
                        @case('PC')
                            @php $alojamientoPedido->estado = 'Pago Completo';@endphp
                        @break
                    @endswitch
                    <tr>
                    <td>{{$alojamientoPedido->huesped->name}} {{$alojamientoPedido->huesped->apellido}}
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                                <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                                <title>Cantidad de huespedes</title>
                            </svg>
                            <p style="display: inline-block;text-align: center;border: 1px solid red;width: 25px;heigth: 25px !important;border-radius: 5px;">{{$alojamientoPedido->huespedes}}</p>
                        </div>
                    </td>
                    <td>{{$alojamientoPedido->fecha_desde}}</td>
                    <td>{{$alojamientoPedido->fecha_hasta}}</td>
                    <td>{{$alojamientoPedido->fecha_pedido}}</td>
                    <td>{{$alojamientoPedido->Alojamiento->titulo}}</td>
                    <td>{{$alojamientoPedido->codigo_reserva}}</td>
                    <td>{{$alojamientoPedido->estado}}</td>
                    <td>{{$alojamientoPedido->valor_total}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="__pagination" style="display: flex;justify-content: space-evenly;">
                {{ $alojamientosPedidos->links() }}
            </div>
            <br>
            <br>
            <br>
            <br>
        </div>
    </div>
</body>
@endsection