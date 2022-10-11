@extends('layouts.app')
@section('content')
    <div class="container-fluid busqueda">
        @if ($alojamientos->total() == 1)
            <div class="busqueda-resultado">{{ $alojamientos->total() }} alojamiento encontrado</div>
        @else
            @if ($alojamientos->total() == 2)
                <div class="busqueda-resultado">Más de {{ $alojamientos->total() - 1 }} alojamiento encontrado</div>
            @else
                @if ($alojamientos->total() == 0)
                    <div class="busqueda-resultado">No se encontraron alojamientos</div>
                @else
                    <div class="busqueda-resultado">Más de {{ $alojamientos->total() - 1 }} alojamientos encontrados</div>
                @endif
            @endif
        @endif
        <div class="busqueda-parametros">{{ $resultadoLeyenda }}</div>
        {!! Form::open([
            'url' => 'alojamientos/busqueda',
            'method' => 'GET',
            'id' => 'filtros-busqueda',
            'onsubmit' => 'return validarDatos();',
        ]) !!}
        {!! Form::hidden('opcion', app('request')->input('opcion')) !!}
        {!! Form::hidden('l', app('request')->input('l')) !!}
        {!! Form::hidden('fd', app('request')->input('fd')) !!}
        {!! Form::hidden('fh', app('request')->input('fh')) !!}
        {!! Form::hidden('h', app('request')->input('h')) !!}
        <select name="t"class="form-control">
            @if (app('request')->input('t') == null)
                <option value="">---</option>
                <option value="AP">Apartamento</option>
                <option value="CS">Casa</option>
                <option value="CB">Cabaña</option>
                <option value="FN">Finca</option>
                <option value="GL">Glamping</option>
                <option value="FH">Finca Hotel</option>
                <option value="HT">Hotel</option>
            @else
                @if (app('request')->input('t') == 'AP')
                    <option value="">---</option>
                    <option selected="selected" value="AP">Apartamento</option>
                    <option value="CS">Casa</option>
                    <option value="CB">Cabaña</option>
                    <option value="FN">Finca</option>
                    <option value="GL">Glamping</option>
                    <option value="FH">Finca Hotel</option>
                    <option value="HT">Hotel</option>
                @else
                    @if (app('request')->input('t') == 'CS')
                        <option value="">---</option>
                        <option value="AP">Apartamento</option>
                        <option selected="selected" value="CS">Casa</option>
                        <option value="CB">Cabaña</option>
                        <option value="FN">Finca</option>
                        <option value="GL">Glamping</option>
                        <option value="FH">Finca Hotel</option>
                        <option value="HT">Hotel</option>
                    @else
                        @if (app('request')->input('t') == 'CB')
                            <option value="">---</option>
                            <option value="AP">Apartamento</option>
                            <option value="CS">Casa</option>
                            <option selected="selected" value="CB">Cabaña</option>
                            <option value="FN">Finca</option>
                            <option value="GL">Glamping</option>
                            <option value="FH">Finca Hotel</option>
                            <option value="HT">Hotel</option>
                        @else
                            @if (app('request')->input('t') == 'CB')
                                <option value="">---</option>
                                <option value="AP">Apartamento</option>
                                <option value="CS">Casa</option>
                                <option selected="selected" value="CB">Cabaña</option>
                                <option value="FN">Finca</option>
                                <option value="GL">Glamping</option>
                                <option value="FH">Finca Hotel</option>
                                <option value="HT">Hotel</option>
                            @else
                                @if (app('request')->input('t') == 'FN')
                                    <option value="">---</option>
                                    <option value="AP">Apartamento</option>
                                    <option value="CS">Casa</option>
                                    <option value="CB">Cabaña</option>
                                    <option selected="selected" value="FN">Finca</option>
                                    <option value="GL">Glamping</option>
                                    <option value="FH">Finca Hotel</option>
                                    <option value="HT">Hotel</option>
                                @else
                                    @if (app('request')->input('t') == 'GL')
                                        <option value="">---</option>
                                        <option value="AP">Apartamento</option>
                                        <option value="CS">Casa</option>
                                        <option value="CB">Cabaña</option>
                                        <option value="FN">Finca</option>
                                        <option selected="selected" value="GL">Glamping</option>
                                        <option value="FH">Finca Hotel</option>
                                        <option value="HT">Hotel</option>
                                    @else
                                        @if (app('request')->input('t') == 'FH')
                                            <option value="">---</option>
                                            <option value="AP">Apartamento</option>
                                            <option value="CS">Casa</option>
                                            <option value="CB">Cabaña</option>
                                            <option value="FN">Finca</option>
                                            <option value="GL">Glamping</option>
                                            <option selected="selected" value="FH">Finca Hotel</option>
                                            <option value="HT">Hotel</option>
                                        @else
                                            <option value="">---</option>
                                            <option value="AP">Apartamento</option>
                                            <option value="CS">Casa</option>
                                            <option value="CB">Cabaña</option>
                                            <option value="FN">Finca</option>
                                            <option value="GL">Glamping</option>
                                            <option value="FH">Finca Hotel</option>
                                            <option selected="selected" value="HT">Hotel</option>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        @endif
                    @endif
                @endif
            @endif
        </select>
        {!! Form::tel('pd', str_replace('.', '', app('request')->input('pd')), [
            'id' => 'pd',
            'placeholder' => 'Precio desde',
            'class' => 'form-control',
        ]) !!}
        {!! Form::tel('ph', str_replace('.', '', app('request')->input('ph')), [
            'id' => 'ph',
            'placeholder' => 'Precio hasta',
            'class' => 'form-control',
        ]) !!}
        <button class="btn boton_submit_filtros" type="submit" id="submit-filtros">Filtrar</button>
        {!! Form::close() !!}
        <!--<div class="card-columns">-->
        <div class="row busqueda-detalle-row">
            @foreach ($alojamientos as $alojamiento)
                <?php
                $alojamientoFoto = App\AlojamientoFoto::where('alojamiento_id', $alojamiento->id)
                    ->where('num_foto', 1)
                    ->first();
                ?>
                <div class="col-md-4 busqueda-detalle-col">
                    <div class="card">
                        <div class="card-body">
                            <div class="busqueda-detalle-contenedor">
                                <img src="{{ URL::to('/uploads/' . $alojamientoFoto->archivo) }}" style="width: 100%" />
                                <div class="busqueda-detalle">
                                    <div class="busqueda-detalle-titulo">{{ $alojamiento->titulo }}</div>
                                    <div class="busqueda-detalle-descripcion">{{ $alojamiento->tipoFormateado() }} -
                                        {{ $alojamiento->ciudad }}</div>
                                    <div class="busqueda-detalle-huespedes">{{ $alojamiento->leyendaHuespedesCuartos() }}
                                    </div>
                                    <div class="busqueda-detalle-precio-titulo">Noche desde</div>
                                    <div class="busqueda-detalle-precio">$
                                        {{ $alojamiento->precioFormateado($alojamiento->precio_baja) }}</div>
                                    {!! link_to(
                                        'alojamientos/' . $alojamiento->id . '?' . http_build_query(app('request')->query()),
                                        'Ver Alojamiento',
                                        ['style' => '', 'class' => ' btn busqueda-boton-ver'],
                                    ) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <center class="paginador">{!! $alojamientos->appends(Request::only(['opcion', 'l', 'fd', 'fh', 'h', 't', 'pd', 'ph']))->render() !!}</center>
    </div>
@endsection
@push('head')
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
@endpush
@push('footer')
    <script type="text/javascript">
        onload = "prepararFormatoCurrency()";
        document.addEventListener("DOMContentLoaded", () => {
            prepararFormatoCurrency();
        });
        function prepararFormatoCurrency() {
            const autoNumericOptionsColombia = {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                currencySymbol: '',
                minimumValue: '1',
                maximumValue: '999999999999',
                decimalPlaces: 0,
            };
            new AutoNumeric('#pd', autoNumericOptionsColombia);
            new AutoNumeric('#ph', autoNumericOptionsColombia);
        }
    </script>
@endpush
