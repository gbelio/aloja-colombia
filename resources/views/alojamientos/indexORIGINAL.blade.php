@extends('layouts.app')
@section('content')
    <div class="container-fluid formulario formulario_index">
        <div class="form-row" style="margin: 0">
            <div class="imagenPanelPC col-xl-7">
                <video autoplay="autoplay" muted="muted" loop="loop" id="myVideo">
                    <source src="{{ url('/img/pasos.mp4') }}">
                </video>
            </div>
            <div class="seccionFormulario col-xl-5">
                <br />
                @if (Session::has('notice'))
                    <div class="alert alert-success">
                        {{ Session::get('notice') }}
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">
                        {{ Session::get('error') }}
                    </div>
                @endif
                <h1 class="rojo h1_principal"> Un gusto tenerte por aquí {{ Auth::user()->nombreCompleto() }}</h1>
                <br />
                <center>
                    {!! link_to('alojamientos/create?paso=1', 'Crear alojamiento', [
                        'style' => 'float: left',
                        'class' => ' btn boton_accion',
                    ]) !!}
                </center>
                <br /><br />
                <div class="form-row">
                    <div class="col-md-12">
                        {!! Form::open(['url' => 'alojamientos', 'class' => 'navbar-form navbar-left', 'method' => 'GET']) !!}
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-md-12">
                                    {!! Form::text('busqueda', app('request')->input('busqueda'), [
                                        'class' => 'form-control',
                                        'placeholder' => 'Buscar...',
                                    ]) !!}
                                    {{ Form::button('<i class="fa fa-search" aria-hidden="true"></i>', ['class' => 'btn boton_accion', 'style' => 'float: right; position: relative; top: -35px; right: 5px;', 'type' => 'submit']) }}
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                <br /><br />
                <h1>Alojamientos</h1>
                <br /><br />
                @foreach ($alojamientos as $alojamiento)
                    <?php
                    
                    $estiloFoto = 'margin-top: -35px;';
                    
                    $alojamientoFoto = \App\AlojamientoFoto::where('alojamiento_id', $alojamiento->id)
                        ->where('num_foto', 1)
                        ->first();
                    
                    ?>
                    <h1 class="indice_h1">Código: {{ $alojamiento->codigo_alojamiento }}</h1>
                    <h2 class="indice_h2">{{ $alojamiento->titulo }}</h2>
                    @if (!is_null($alojamientoFoto))
                        <?php
                        
                        $estiloFoto = 'margin-top: -80px;';
                        
                        ?>
                        <div><img src="{{ URL::to('/uploads/' . $alojamientoFoto->archivo) }}" style="width: 100%" /></div>
                    @endif
                    <br />
                    <div style="{{ $estiloFoto }} position: relative; z-index: 1000; padding: 15px; ">
                        {!! Form::open([
                            'onsubmit' => "return confirm('Se eliminará esta propiedad. ¿Desea continuar?')",
                            'url' => 'alojamientos/' . $alojamiento->id,
                            'method' => 'DELETE',
                        ]) !!}
                        {!! Form::button('Eliminar', [
                            'type' => 'submit',
                            'class' => ' btn boton_accion boton_eliminar',
                            'style' => 'float: left; margin-right: 5px;',
                        ]) !!}
                        {!! Form::close() !!}
                        {!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=11', 'Editar', [
                            'style' => 'float: left',
                            'class' => ' btn boton_accion',
                        ]) !!}
                        @if ($alojamiento->estado == 'A')
                            {!! link_to('#javascript:void;', 'Activa', [
                                'onClick' =>
                                    'if (confirm("Se inactivará el alojamiento. ¿Desea continuar?")) { window.location="' .
                                    url('alojamientos') .
                                    '/' .
                                    $alojamiento->id .
                                    '/inactivar"; } else { return (false); }',
                                'class' => 'btn boton_accion',
                                'style' => 'float: right',
                            ]) !!}
                        @else
                            {!! link_to('#javascript:void;', 'Inactiva', [
                                'onClick' =>
                                    'if (confirm("Se activará el alojamiento. ¿Desea continuar?")) { window.location="' .
                                    url('alojamientos') .
                                    '/' .
                                    $alojamiento->id .
                                    '/activar"; } else { return (false); }',
                                'class' => 'btn boton_accion boton_eliminar',
                                'style' => 'float: right',
                            ]) !!}
                        @endif
                    </div>
                    <br /><br />
                    <div>
                        <table class="indice_tabla">
                            <tr>
                                <td>
                                    <h1 class="indice_h1">Baja</h1><span
                                        class="indice_valor">${{ $alojamiento->precioFormateado($alojamiento->precio_baja) }}</span>
                                </td>
                                <td>
                                    <h1 class="indice_h1">Media</h1><span
                                        class="indice_valor">${{ $alojamiento->precioFormateado($alojamiento->precio_media) }}</span>
                                </td>
                                <td>
                                    <h1 class="indice_h1">Alta</h1><span
                                        class="indice_valor">${{ $alojamiento->precioFormateado($alojamiento->precio_alta) }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br />
                    <hr class="negro_fondo" /><br />
                @endforeach
                {!! $alojamientos->appends(Request::only(['busqueda']))->render() !!}
            </div>
        </div>
    </div>
@endsection
