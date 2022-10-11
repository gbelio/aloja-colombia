@extends('layouts.app')
@section('content')
<div class="container-fluid formulario formulario_index">
    <div class="form-row" style="margin: 0; justify-content: flex-end;">
        <div class="imagenPanelPC col-xl-7 cb-slideshow cb-slideshow-section">
            <li><span>Image 01</span></li>
            <li><span>Image 02</span></li>
            <li><span>Image 03</span></li>
            <li><span>Image 04</span></li>
            <li><span>Image 05</span></li>
        </div>
        <div class="seccionFormulario col-md-5">
            @if(Session::has('notice'))
            <div class="alert alert-success">
                {{ Session::get('notice') }}
            </div>
            @endif
            @if(Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
            @endif
            <br />
            @if ( app('request')->input('op') == 1)
            <h1 class="rojo h1_principal"> Listado de reservas </h1>
            @else
            <h1 class="rojo h1_principal"> Mis viajes </h1>
            @endif
            <br />
            <div class="form-row">
                <div class="col-md-12">
                    {!! Form::open(array('url' => 'alojamientosPedidos', 'class' => 'navbar-form navbar-left', 'method'
                        => 'GET')) !!}
                    {!! Form::hidden('op', app('request')->input('op')) !!}
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-md-12">
                                <select name="busqueda" id="busqueda" class="form-control">
                                    @if( app('request')->input('busqueda') == null )
                                    <option selected="selected" value="">Todas</option>
                                    <option value="SO">Solicitadas</option>
                                    <option value="CO">Confirmadas</option>
                                    @else
                                    @if( app('request')->input('busqueda') == 'SO' )
                                    <option value="">Todas</option>
                                    <option selected="selected" value="SO">Solicitadas</option>
                                    <option value="CO">Confirmadas</option>
                                    @else
                                    <option value="">Todas</option>
                                    <option value="SO">Solicitadas</option>
                                    <option selected="selected" value="CO">Confirmadas</option>
                                    @endif
                                    @endif
                                </select>
                                {{ Form::button('<i class="fa fa-search" aria-hidden="true"></i>', ['class' => 'btn boton_accion', 'style' => 'float: right; position: relative; top: -35px; right: 5px;', 'type' => 'submit']) }}
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            @foreach ($alojamientosPedidos as $alojamientoPedido)
            <?php
                $estiloFoto = "margin-top: -35px;";
                $alojamientoFoto = \App\AlojamientoFoto::where('alojamiento_id', $alojamientoPedido->alojamiento_id)->where('num_foto', 1)->first();
            ?>
            @if ( $alojamientoPedido->estado == 'SO' )
            <div class="indice_fecha">Solicitud: {{$alojamientoPedido->created_at}} hs</div>
            @endif
            <div class="indice_recuadro">Código Reserva: {{$alojamientoPedido->codigo_reserva}}</div>
            <h1 class="indice_h1">Código Alojamiento: {{$alojamientoPedido->Alojamiento->codigo_alojamiento}}</h1>
            <h2 class="indice_h2">{{$alojamientoPedido->Alojamiento->titulo}}</h2>
            @if ( !is_null($alojamientoFoto) )
            <?php
                $estiloFoto = "margin-top: -80px;";
            ?>
            <div><img src="{{ URL::to('/uploads/' . $alojamientoFoto->archivo) }}" style="width: 100%" /></div>
            @endif
            <br />
            <div style="{{$estiloFoto}} position: relative; z-index: 1000; padding: 4px; display:flex;justify-content: space-between;flex-direction: row-reverse;">
                @if ( $alojamientoPedido->estado == 'SO' )
                @if ( $alojamientoPedido->Alojamiento->propietario_id == \Auth::user()->id ||
                Auth::user()->esAdministrador() )
                {!! link_to('alojamientosPedidos/'.$alojamientoPedido->id . '/edit?op=' . app('request')->input('op'),
                    'Confirmar / Rechazar', ['style' => 'float: left', 'class' => ' btn boton_accion']) !!}
                @else
                {!! link_to('alojamientosPedidos/'.$alojamientoPedido->id . '/edit?op=' . app('request')->input('op'),
                    'Ver reserva', ['style' => '', 'class' => ' btn boton_accion']) !!}
                @endif
                <div class="btn boton_accion boton_eliminar" style="float: right;">Pendiente</div>
                @else
                @if ( $alojamientoPedido->estado == 'CO' || $alojamientoPedido->estado == 'PP' )
                {!! link_to('alojamientosPedidos/'.$alojamientoPedido->id . '/edit?op=' . app('request')->input('op'),
                'PAGO PENDIENTE', ['style' => 'border-color:red; border: 1px solid;font-size: 13px; padding: 3px;', 'class' => ' btn boton_accion boton_alternativo']) !!}
                @endif
                @if ( $alojamientoPedido->estado == 'RE' )
                <div class="btn boton_accion boton_eliminar" style="float: right;">Rechazada</div>
                @endif
                @if ( $alojamientoPedido->estado == 'PC' )
                {{-- <div class="btn boton_accion boton_alternativo"
                    style="float: right; color: white; background-color:darkgreen !important">Pago realizado</div> --}}
                {!! link_to('alojamientosPedidos/'.$alojamientoPedido->id . '/edit?op=' . app('request')->input('op'),
                'Pago Realizado', ['style' => 'font-size: 13px; padding: 4px;color: white; background-color:darkgreen !important;', 'class' => ' btn boton_accion boton_alternativo']) !!}
                @endif
                @if($time < $alojamientoPedido->fecha_hasta && $alojamientoPedido->huesped_id == \Auth::user()->id)
                {!! Form::open(array('url' => 'alojamientosPedidos/' . $alojamientoPedido->id, 
                'method' => 'patch', 'enctype' => 'multipart/form-data')) !!}
                <button type="submit" onclick="return confirm('Se cancelará la reserva. ¿Desea continuar?')" value="CA" name="navegacion"
                    style="font-size: 12px;" class="btn boton_accion boton_eliminar">
                    Cancelar Reserva
                </button>
                {!! Form::close() !!}
                @endif
                {!! link_to('alojamientosPedidos/'.$alojamientoPedido->id . '/edit?op=' . app('request')->input('op'),
                    'Ver reserva', ['style' => 'font-size: 12px;padding: 5px;', 'class' => ' btn boton_accion']) !!}
                @endif
            </div>
            <hr class="negro_fondo" /><br />
            <br />
            @endforeach
            {!! $alojamientosPedidos->appends(Request::only(['busqueda','op']))->render() !!}
        </div>
    </div>
</div>
@endsection
