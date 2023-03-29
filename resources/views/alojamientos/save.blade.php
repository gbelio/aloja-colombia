@extends('layouts.app')
@section('content')
    <?php
    $paso = app('request')->input('paso');
    ?>
    <div class="container-fluid formulario">
        <div class="form-row" style="margin: 0">
            <div class="imagenPanelPC col-xl-7">
                <video autoplay="autoplay" muted="muted" loop="loop" id="myVideo">
                    <source src="{{ url('/img/pasos.mp4') }}">
                </video>
            </div>
            <div class="seccionFormulario col-xl-5">
                @if (count($errors) > 0)
                    <div class="alert alert-danger" style="">
                        <ul style="padding-top: 15px;">
                            @foreach ($errors->all() as $error)
                                <li><b>{{ $error }}</b></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="avance">
                    <div class="avance_real" style="width: {{ ($paso * 100) / 11 }}%"></div>
                </div>
                {!! Form::open([
                    'url' => 'alojamientos/' . $alojamiento->id,
                    'method' => $method,
                    'enctype' => 'multipart/form-data',
                ]) !!}
                {!! Form::hidden('paso', $paso) !!}
                <br />
                @if ($paso == 1)
                    <h1> Bienvenido!</h1>
                    <h4>Comencemos a crear la publicación de tu alojamiento </h4>
                    <br />
                    @if (Auth::user()->esAdministrador())
                        <h2>Propietario</h2>
                        <div class="form-group">
                            <select name="propietario_id" id="propietario_id" class="form-control">
                                @foreach ($propietarios as $item)
                                    @if ($alojamiento->id != null)
                                        @if ($item->id == $alojamiento->propietario_id)
                                            <option selected="selected" value="{{ $item->id }}">
                                                {{ $item->nombreCompleto() }} </option>
                                        @else
                                            <option value="{{ $item->id }}"> {{ $item->nombreCompleto() }} </option>
                                        @endif
                                    @else
                                        <option value="{{ $item->id }}"> {{ $item->nombreCompleto() }} </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <h2>Destacar en página de inicio</h2>
                        <div class="form-group custom-control custom-switch">
                            {!! Form::checkbox('destacada', $alojamiento->destacada, $alojamiento->destacada, [
                                'class' => 'custom-control-input',
                                'id' => 'destacada',
                            ]) !!}
                            {!! Form::label('destacada', 'Destacada', ['class' => 'custom-control-label', 'for' => 'destacada']) !!}
                        </div>
                        <br />
                    @endif
                    <h2>¿Cuéntanos qué tipo de Alojamiento tienes?</h2>
                    <div class="form-group">
                        {!! Form::radio('tipo_alojamiento', 'AP', $alojamiento->tipo_alojamiento == 'AP' ? true : false, [
                            'required' => 'required',
                            'id' => 'apartamento',
                        ]) !!}
                        {!! Form::label('apartamento', 'Apartamento') !!}
                        <br />
                        {!! Form::radio('tipo_alojamiento', 'CS', $alojamiento->tipo_alojamiento == 'CS' ? true : false, [
                            'required' => 'required',
                            'id' => 'casa',
                        ]) !!}
                        {!! Form::label('casa', 'Casa') !!}
                        <br />
                        {!! Form::radio('tipo_alojamiento', 'CB', $alojamiento->tipo_alojamiento == 'CB' ? true : false, [
                            'required' => 'required',
                            'id' => 'cabana',
                        ]) !!}
                        {!! Form::label('cabana', 'Cabaña') !!}
                        <br />
                        {!! Form::radio('tipo_alojamiento', 'FN', $alojamiento->tipo_alojamiento == 'FN' ? true : false, [
                            'required' => 'required',
                            'id' => 'finca',
                        ]) !!}
                        {!! Form::label('finca', 'Finca') !!}
                        <br />
                        {!! Form::radio('tipo_alojamiento', 'GL', $alojamiento->tipo_alojamiento == 'GL' ? true : false, [
                            'required' => 'required',
                            'id' => 'glamping',
                        ]) !!}
                        {!! Form::label('glamping', 'Glamping') !!}
                        <br />
                    </div>
                    <br />
                    <h2>¿Dónde se encuentra ubicado tu alojamiento?</h2>
                    <h4>Ubica tu propiedad usando el puntero de google maps o escribiendo tu dirección.</h4>
                    <div class="form-group">
                        <input type="text" name="searchInput" id="searchInput" class="form-control" required
                            placeholder="Buscar en Google maps" value="{{ $alojamiento->mapa_locacion }}">
                        <small class="form-text text-muted">* Corrige la ubicación de ser necesario moviendo el puntero.
                            <br />* Sólo los húespedes confirmados podrán verlo para llegar a tu alojamiento<br />* P. ej.:
                            Carrera 11 # 82 - 71</small>
                    </div>
                    <br />
                    <div class="form_area" style="display: none;">
                        <input type="text" name="location" id="location" value="{{ $alojamiento->mapa_locacion }}">
                        <input type="text" name="lat" id="lat" value="{{ $alojamiento->mapa_latitud }}">
                        <input type="text" name="lng" id="lng" value="{{ $alojamiento->mapa_longitud }}">
                    </div>
                    <div class="map" id="map" style="width: 100%; height: 300px;"></div>
                    <br />
                    <br />
                    <br />
                    <h2>Completa la información adicional de tu alojamiento</h2>
                    <h4>Solo los huéspedes que tengan confirmada la reserva podrán ver esta información.</h4>
                    <div class="form-group">
                        {!! Form::text('direccion', $alojamiento->direccion, [
                            'class' => 'form-control',
                            'placeholder' => 'Número de apartamento o casa',
                            'maxlength' => '10',
                        ]) !!}
                        <small class="form-text text-muted">* Campo opcional</small>
                    </div>
                    <div class="form-group">
                        {!! Form::text('barrio', $alojamiento->barrio, [
                            'class' => 'form-control',
                            'placeholder' => 'Barrio',
                            'maxlength' => '50',
                        ]) !!}
                        <small class="form-text text-muted">* Campo opcional</small>
                    </div>
                    <div class="form-group">
                        {!! Form::text('ciudad', $alojamiento->ciudad, [
                            'class' => 'form-control',
                            'readonly',
                            'placeholder' => 'Ciudad',
                            'required' => 'required',
                            'maxlength' => '150',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('municipio', $alojamiento->municipio, [
                            'class' => 'form-control',
                            'readonly',
                            'placeholder' => 'Municipio / Vereda',
                            'required' => 'required',
                            'maxlength' => '150',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('departamento', $alojamiento->departamento, [
                            'class' => 'form-control',
                            'readonly',
                            'placeholder' => 'Departamento',
                            'maxlength' => '150',
                        ]) !!}
                    </div>
                @endif
                @if ($paso == 2)
                    <h2>¿Cuántas personas se pueden alojar en tu propiedad?</h2>
                    <div class="form-group form-numero">
                        {!! Form::label('huespedes', 'Huéspedes') !!}
                        <button type="button" style="float: left;" onclick="numero('huespedes', '-', 1, 50);"
                            class="btn numeroBoton">-</button>
                        @if (!is_null($alojamiento->huespedes))
                            {!! Form::number('huespedes', $alojamiento->huespedes, [
                                'step' => '1',
                                'min' => '1',
                                'max' => '50',
                                'class' => 'form-control numeroValor',
                                'required',
                            ]) !!}
                        @else
                            {!! Form::number('huespedes', 0, [
                                'step' => '1',
                                'min' => '1',
                                'max' => '50',
                                'class' => 'form-control numeroValor',
                                'required',
                            ]) !!}
                        @endif
                        <button type="button" style="float: left;" onclick="numero('huespedes', '+', 1, 50);"
                            class="btn numeroBoton">+</button>
                        <br />
                    </div>
                    <br /><br />
                    <h2>¿Cuántas habitaciones tiene tu propiedad?</h2>
                    <div class="form-group form-numero">
                        {!! Form::label('cuartos', 'Habitaciones') !!}
                        <button type="button" style="float: left;" onclick="numero('cuartos', '-', 1, 30);"
                            class="btn numeroBoton">-</button>
                        @if (!is_null($alojamiento->cuartos))
                            {!! Form::number('cuartos', $alojamiento->cuartos, [
                                'onchange' => 'mostrarOcultar("cuarto", $("#cuartos").val(), 30)',
                                'step' => '1',
                                'min' => '1',
                                'max' => '30',
                                'class' => 'form-control numeroValor',
                                'required',
                            ]) !!}
                        @else
                            {!! Form::number('cuartos', 0, [
                                'onchange' => 'mostrarOcultar("cuarto", $("#cuartos").val(), 30)',
                                'step' => '1',
                                'min' => '1',
                                'max' => '30',
                                'class' => 'form-control numeroValor',
                                'required',
                            ]) !!}
                        @endif
                        <button type="button" style="float: left;" onclick="numero('cuartos', '+', 1, 30);"
                            class="btn numeroBoton">+</button>
                        <br />
                    </div>
                    <br /><br />
                    <h2>¿Cuántas camas tiene tu propiedad?</h2>
                    @for ($iCuarto = 1; $iCuarto <= 31; $iCuarto++)
                        <?php
                        
                        $alojamientoCuarto = App\AlojamientoCuarto::where('alojamiento_id', $alojamiento->id)
                            ->where('num_cuarto', $iCuarto)
                            ->first();
                        
                        $visibilidadCuarto = 'inline';
                        
                        if ($iCuarto > $alojamiento->cuartos) {
                            $visibilidadCuarto = 'none';
                        }
                        
                        $leyendaCuarto = 'Cuarto ' . $iCuarto;
                        
                        if ($iCuarto == 31) {
                            $leyendaCuarto = 'Espacios compartidos';
                        
                            $visibilidadCuarto = 'inline';
                        }
                        
                        $visibilidadCamaOtra1 = 'none';
                        
                        $visibilidadCamaOtra2 = 'none';
                        
                        $visibilidadCamaOtra3 = 'none';
                        
                        $visibilidadCamaOtra4 = 'none';
                        
                        $visibilidadCamaOtra5 = 'none';
                        
                        if (!is_null($alojamientoCuarto)) {
                            if ($alojamientoCuarto->camas_otro_tipo_1 != null && $alojamientoCuarto->camas_otro_tipo_1 > 0) {
                                $visibilidadCamaOtra1 = 'block';
                            }
                        
                            if ($alojamientoCuarto->camas_otro_tipo_2 != null && $alojamientoCuarto->camas_otro_tipo_2 > 0) {
                                $visibilidadCamaOtra2 = 'block';
                            }
                        
                            if ($alojamientoCuarto->camas_otro_tipo_3 != null && $alojamientoCuarto->camas_otro_tipo_3 > 0) {
                                $visibilidadCamaOtra3 = 'block';
                            }
                        
                            if ($alojamientoCuarto->camas_otro_tipo_4 != null && $alojamientoCuarto->camas_otro_tipo_4 > 0) {
                                $visibilidadCamaOtra4 = 'block';
                            }
                        
                            if ($alojamientoCuarto->camas_otro_tipo_5 != null && $alojamientoCuarto->camas_otro_tipo_5 > 0) {
                                $visibilidadCamaOtra5 = 'block';
                            }
                        }
                        
                        ?>
                        <div id="cuarto{{ $iCuarto }}" style="display: {{ $visibilidadCuarto }}">
                            <br />
                            <h4 style="float: left;">{{ $leyendaCuarto }} </h4> <button id="botonCamas{{ $iCuarto }}"
                                type="button" onclick="botonCamas({{ $iCuarto }})" class="btn btn-light"
                                style="float: right;">Editar camas</button><br /><br />
                            <h5 id="leyendaCamas{{ $iCuarto }}"></h5>
                            <div id="editarCamas{{ $iCuarto }}" style="display: none;">
                                <div class="form-group form-numero">
                                    {!! Form::label('camas_king[' . $iCuarto . ']', 'Cama king') !!}
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_king[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_king[' . $iCuarto . ']', $alojamientoCuarto->camas_king, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_king[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_king[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero">
                                    {!! Form::label('camas_queen[' . $iCuarto . ']', 'Cama queen') !!}
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_queen[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_queen[' . $iCuarto . ']', $alojamientoCuarto->camas_queen, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_queen[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_queen[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero">
                                    {!! Form::label('camas_doble[' . $iCuarto . ']', 'Cama doble') !!}
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_doble[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_doble[' . $iCuarto . ']', $alojamientoCuarto->camas_doble, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_doble[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_doble[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero">
                                    {!! Form::label('camas_semi_doble[' . $iCuarto . ']', 'Cama semi doble') !!}
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_semi_doble[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_semi_doble[' . $iCuarto . ']', $alojamientoCuarto->camas_semi_doble, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_semi_doble[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_semi_doble[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero">
                                    {!! Form::label('camas_sencilla[' . $iCuarto . ']', 'Cama sencilla') !!}
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_sencilla[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_sencilla[' . $iCuarto . ']', $alojamientoCuarto->camas_sencilla, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_sencilla[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_sencilla[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero">
                                    {!! Form::label('camas_camarote[' . $iCuarto . ']', 'Camarote') !!}
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_camarote[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_camarote[' . $iCuarto . ']', $alojamientoCuarto->camas_camarote, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_camarote[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_camarote[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero">
                                    {!! Form::label('camas_auxiliar[' . $iCuarto . ']', 'Cama auxiliar') !!}
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_auxiliar[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_auxiliar[' . $iCuarto . ']', $alojamientoCuarto->camas_auxiliar, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_auxiliar[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_auxiliar[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero">
                                    {!! Form::label('camas_sofa[' . $iCuarto . ']', 'Sofá cama') !!}
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_sofa[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_sofa[' . $iCuarto . ']', $alojamientoCuarto->camas_sofa, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_sofa[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_sofa[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero" id="camas_otro_tipo_1_div{{ $iCuarto }}"
                                    style="display: {{ $visibilidadCamaOtra1 }}">
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::text('camas_otro_tipo_nombre_1[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_nombre_1, [
                                            'class' => 'form-control numeroInputLabel',
                                        ]) !!}
                                    @else
                                        {!! Form::text('camas_otro_tipo_nombre_1[' . $iCuarto . ']', '', ['class' => 'form-control numeroInputLabel']) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_1[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_otro_tipo_1[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_1, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_otro_tipo_1[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_1[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero" id="camas_otro_tipo_2_div{{ $iCuarto }}"
                                    style="display: {{ $visibilidadCamaOtra2 }}">
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::text('camas_otro_tipo_nombre_2[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_nombre_2, [
                                            'class' => 'form-control numeroInputLabel',
                                        ]) !!}
                                    @else
                                        {!! Form::text('camas_otro_tipo_nombre_2[' . $iCuarto . ']', '', ['class' => 'form-control numeroInputLabel']) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_2[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_otro_tipo_2[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_2, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_otro_tipo_2[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_2[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero" id="camas_otro_tipo_3_div{{ $iCuarto }}"
                                    style="display: {{ $visibilidadCamaOtra3 }}">
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::text('camas_otro_tipo_nombre_3[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_nombre_3, [
                                            'class' => 'form-control numeroInputLabel',
                                        ]) !!}
                                    @else
                                        {!! Form::text('camas_otro_tipo_nombre_3[' . $iCuarto . ']', '', ['class' => 'form-control numeroInputLabel']) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_3[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_otro_tipo_3[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_3, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_otro_tipo_3[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_3[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero" id="camas_otro_tipo_4_div{{ $iCuarto }}"
                                    style="display: {{ $visibilidadCamaOtra4 }}">
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::text('camas_otro_tipo_nombre_4[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_nombre_4, [
                                            'class' => 'form-control numeroInputLabel',
                                        ]) !!}
                                    @else
                                        {!! Form::text('camas_otro_tipo_nombre_4[' . $iCuarto . ']', '', ['class' => 'form-control numeroInputLabel']) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_4[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_otro_tipo_4[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_4, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_otro_tipo_4[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_4[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                <div class="form-group form-numero" id="camas_otro_tipo_5_div{{ $iCuarto }}"
                                    style="display: {{ $visibilidadCamaOtra5 }}">
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::text('camas_otro_tipo_nombre_5[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_nombre_5, [
                                            'class' => 'form-control numeroInputLabel',
                                        ]) !!}
                                    @else
                                        {!! Form::text('camas_otro_tipo_nombre_5[' . $iCuarto . ']', '', ['class' => 'form-control numeroInputLabel']) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_5[{{ $iCuarto }}]', '-', 0, 15);"
                                        class="btn numeroBoton">-</button>
                                    @if (!is_null($alojamientoCuarto))
                                        {!! Form::number('camas_otro_tipo_5[' . $iCuarto . ']', $alojamientoCuarto->camas_otro_tipo_5, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @else
                                        {!! Form::number('camas_otro_tipo_5[' . $iCuarto . ']', 0, [
                                            'step' => '1',
                                            'min' => '0',
                                            'max' => '15',
                                            'class' => 'form-control numeroValor',
                                        ]) !!}
                                    @endif
                                    <button type="button" style="float: left;"
                                        onclick="numero('camas_otro_tipo_5[{{ $iCuarto }}]', '+', 0, 15);"
                                        class="btn numeroBoton">+</button>
                                    <br />
                                </div>
                                <br />
                                @if ($visibilidadCamaOtra1 == 'none' ||
                                    $visibilidadCamaOtra2 == 'none' ||
                                    $visibilidadCamaOtra3 == 'none' ||
                                    $visibilidadCamaOtra4 == 'none' ||
                                    $visibilidadCamaOtra5 == 'none')
                                    <button type="button" id="camas_otro_tipo_boton{{ $iCuarto }}"
                                        onclick="agregarCama({{ $iCuarto }})" class="btn btn-success">Agregar
                                        otra</button>
                                @endif
                            </div>
                            <hr />
                        </div>
                    @endfor
                    <br /><br />
                    <h2>¿Cuántas baños tiene tu propiedad?</h2>
                    <div class="form-group form-numero">
                        {!! Form::label('banios_completos', 'Baños completos') !!}
                        <button type="button" style="float: left;" onclick="numero('banios_completos', '-', 0, 15);"
                            class="btn numeroBoton">-</button>
                        @if (!is_null($alojamiento->banios_completos))
                            {!! Form::number('banios_completos', $alojamiento->banios_completos, [
                                'step' => '1',
                                'min' => '0',
                                'max' => '15',
                                'class' => 'form-control numeroValor',
                                'required',
                            ]) !!}
                        @else
                            {!! Form::number('banios_completos', 0, [
                                'step' => '1',
                                'min' => '0',
                                'max' => '15',
                                'class' => 'form-control numeroValor',
                                'required',
                            ]) !!}
                        @endif
                        <button type="button" style="float: left;" onclick="numero('banios_completos', '+', 0, 15);"
                            class="btn numeroBoton">+</button>
                        <br />
                    </div>
                    <br /><br />
                    <div class="form-group form-numero">
                        {!! Form::label('banios_sin_ducha', 'Baños sin ducha') !!}
                        <button type="button" style="float: left;" onclick="numero('banios_sin_ducha', '-', 0, 15);"
                            class="btn numeroBoton">-</button>
                        @if (!is_null($alojamiento->banios_sin_ducha))
                            {!! Form::number('banios_sin_ducha', $alojamiento->banios_sin_ducha, [
                                'step' => '1',
                                'min' => '0',
                                'max' => '15',
                                'class' => 'form-control numeroValor',
                                'required',
                            ]) !!}
                        @else
                            {!! Form::number('banios_sin_ducha', 0, [
                                'step' => '1',
                                'min' => '0',
                                'max' => '15',
                                'class' => 'form-control numeroValor',
                                'required',
                            ]) !!}
                        @endif
                        <button type="button" style="float: left;" onclick="numero('banios_sin_ducha', '+', 0, 15);"
                            class="btn numeroBoton">+</button>
                        <br />
                    </div>
                    <br />
                @endif
                @if ($paso == 3)
                    <h2>Servicios de la propiedad</h2>
                    <h2>Qué servicios ofreces en tu propiedad?</h2>
                    <br />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_wifi', $alojamiento->servicio_wifi, $alojamiento->servicio_wifi, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_wifi',
                        ]) !!}
                        {!! Form::label('servicio_wifi', 'WIFI', ['class' => 'custom-control-label', 'for' => 'servicio_wifi']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_tv', $alojamiento->servicio_tv, $alojamiento->servicio_tv, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_tv',
                        ]) !!}
                        {!! Form::label('servicio_tv', 'Televisor', ['class' => 'custom-control-label', 'for' => 'servicio_tv']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_cable', $alojamiento->servicio_cable, $alojamiento->servicio_cable, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_cable',
                        ]) !!}
                        {!! Form::label('servicio_cable', 'TV por cable', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_cable',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_sonido', $alojamiento->servicio_sonido, $alojamiento->servicio_sonido, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_sonido',
                        ]) !!}
                        {!! Form::label('servicio_sonido', 'Reproductor de sonido', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_sonido',
                        ]) !!}
                    </div>
                    <hr class="" />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_aa', $alojamiento->servicio_aa, $alojamiento->servicio_aa, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_aa',
                        ]) !!}
                        {!! Form::label('servicio_aa', 'Aire acondicionado', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_aa',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_vent', $alojamiento->servicio_vent, $alojamiento->servicio_vent, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_vent',
                        ]) !!}
                        {!! Form::label('servicio_vent', 'Ventilador', ['class' => 'custom-control-label', 'for' => 'servicio_vent']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_agua', $alojamiento->servicio_agua, $alojamiento->servicio_agua, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_agua',
                        ]) !!}
                        {!! Form::label('servicio_agua', 'Agua caliente', ['class' => 'custom-control-label', 'for' => 'servicio_agua']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_lav', $alojamiento->servicio_lav, $alojamiento->servicio_lav, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_lav',
                        ]) !!}
                        {!! Form::label('servicio_lav', 'Lavadora', ['class' => 'custom-control-label', 'for' => 'servicio_lav']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_sec', $alojamiento->servicio_sec, $alojamiento->servicio_sec, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_sec',
                        ]) !!}
                        {!! Form::label('servicio_sec', 'Secadora', ['class' => 'custom-control-label', 'for' => 'servicio_sec']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_sec_pelo', $alojamiento->servicio_sec_pelo, $alojamiento->servicio_sec_pelo, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_sec_pelo',
                        ]) !!}
                        {!! Form::label('servicio_sec_pelo', 'Secadora de pelo', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_sec_pelo',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_plancha', $alojamiento->servicio_plancha, $alojamiento->servicio_plancha, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_plancha',
                        ]) !!}
                        {!! Form::label('servicio_plancha', 'Plancha', ['class' => 'custom-control-label', 'for' => 'servicio_plancha']) !!}
                    </div>
                    <hr class="" />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_toallas', $alojamiento->servicio_toallas, $alojamiento->servicio_toallas, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_toallas',
                        ]) !!}
                        {!! Form::label('servicio_toallas', 'Toallas', ['class' => 'custom-control-label', 'for' => 'servicio_toallas']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_sabanas', $alojamiento->servicio_sabanas, $alojamiento->servicio_sabanas, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_sabanas',
                        ]) !!}
                        {!! Form::label('servicio_sabanas', 'Sábanas', ['class' => 'custom-control-label', 'for' => 'servicio_sabanas']) !!}
                    </div>
                    <hr class="" />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_cocina', $alojamiento->servicio_cocina, $alojamiento->servicio_cocina, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_cocina',
                        ]) !!}
                        {!! Form::label('servicio_cocina', 'Cocina', ['class' => 'custom-control-label', 'for' => 'servicio_cocina']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_nevera', $alojamiento->servicio_nevera, $alojamiento->servicio_nevera, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_nevera',
                        ]) !!}
                        {!! Form::label('servicio_nevera', 'Nevera', ['class' => 'custom-control-label', 'for' => 'servicio_nevera']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_utensillos', $alojamiento->servicio_utensillos, $alojamiento->servicio_utensillos, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_utensillos',
                        ]) !!}
                        {!! Form::label('servicio_utensillos', 'Utensillos de cocina', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_utensillos',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_horno_elec', $alojamiento->servicio_horno_elec, $alojamiento->servicio_horno_elec, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_horno_elec',
                        ]) !!}
                        {!! Form::label('servicio_horno_elec', 'Horno eléctrico', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_horno_elec',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_micro', $alojamiento->servicio_micro, $alojamiento->servicio_micro, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_micro',
                        ]) !!}
                        {!! Form::label('servicio_micro', 'Microondas', ['class' => 'custom-control-label', 'for' => 'servicio_micro']) !!}
                    </div>
                    <hr class="" />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_piscina', $alojamiento->servicio_piscina, $alojamiento->servicio_piscina, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_piscina',
                        ]) !!}
                        {!! Form::label('servicio_piscina', 'Piscina', ['class' => 'custom-control-label', 'for' => 'servicio_piscina']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_jacuzzi', $alojamiento->servicio_jacuzzi, $alojamiento->servicio_jacuzzi, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_jacuzzi',
                        ]) !!}
                        {!! Form::label('servicio_jacuzzi', 'Jacuzzi', ['class' => 'custom-control-label', 'for' => 'servicio_jacuzzi']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox(
                            'servicio_asoleadoras',
                            $alojamiento->servicio_asoleadoras,
                            $alojamiento->servicio_asoleadoras,
                            ['class' => 'custom-control-input', 'id' => 'servicio_asoleadoras'],
                        ) !!}
                        {!! Form::label('servicio_asoleadoras', 'Asoleadoras', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_asoleadoras',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_sombrillas', $alojamiento->servicio_sombrillas, $alojamiento->servicio_sombrillas, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_sombrillas',
                        ]) !!}
                        {!! Form::label('servicio_sombrillas', 'Sombrillas', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_sombrillas',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_kiosko', $alojamiento->servicio_kiosko, $alojamiento->servicio_kiosko, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_kiosko',
                        ]) !!}
                        {!! Form::label('servicio_kiosko', 'Kiosko', ['class' => 'custom-control-label', 'for' => 'servicio_kiosko']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_hamacas', $alojamiento->servicio_hamacas, $alojamiento->servicio_hamacas, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_hamacas',
                        ]) !!}
                        {!! Form::label('servicio_hamacas', 'Hamacas', ['class' => 'custom-control-label', 'for' => 'servicio_hamacas']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_bbq', $alojamiento->servicio_bbq, $alojamiento->servicio_bbq, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_bbq',
                        ]) !!}
                        {!! Form::label('servicio_bbq', 'BBQ', ['class' => 'custom-control-label', 'for' => 'servicio_bbq']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_horno_len', $alojamiento->servicio_horno_len, $alojamiento->servicio_horno_len, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_horno_len',
                        ]) !!}
                        {!! Form::label('servicio_horno_len', 'Horno de leña / Barro', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_horno_len',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_estufa_len', $alojamiento->servicio_estufa_len, $alojamiento->servicio_estufa_len, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_estufa_len',
                        ]) !!}
                        {!! Form::label('servicio_estufa_len', 'Estufa de Leña', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_estufa_len',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_verdes', $alojamiento->servicio_verdes, $alojamiento->servicio_verdes, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_verdes',
                        ]) !!}
                        {!! Form::label('servicio_verdes', 'Zonas Verdes', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_verdes',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_gimnasio', $alojamiento->servicio_gimnasio, $alojamiento->servicio_gimnasio, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_gimnasio',
                        ]) !!}
                        {!! Form::label('servicio_gimnasio', 'Gimnasio', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_gimnasio',
                        ]) !!}
                    </div>
                    <hr class="" />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_chimenea', $alojamiento->servicio_chimenea, $alojamiento->servicio_chimenea, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_chimenea',
                        ]) !!}
                        {!! Form::label('servicio_chimenea', 'Chimenea Interior', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_chimenea',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_balcon', $alojamiento->servicio_balcon, $alojamiento->servicio_balcon, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_balcon',
                        ]) !!}
                        {!! Form::label('servicio_balcon', 'Balcón', ['class' => 'custom-control-label', 'for' => 'servicio_balcon']) !!}
                    </div>
                    <hr class="" />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_ascensor', $alojamiento->servicio_ascensor, $alojamiento->servicio_ascensor, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_ascensor',
                        ]) !!}
                        {!! Form::label('servicio_ascensor', 'Ascensor', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_ascensor',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox(
                            'servicio_parqueadero',
                            $alojamiento->servicio_parqueadero,
                            $alojamiento->servicio_parqueadero,
                            ['class' => 'custom-control-input', 'id' => 'servicio_parqueadero'],
                        ) !!}
                        {!! Form::label('servicio_parqueadero', 'Parqueadero', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_parqueadero',
                        ]) !!}
                    </div>
                    <hr class="" />
                    <h4 class="verde">Recreación</h4>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox(
                            'servicio_cancha_futbol',
                            $alojamiento->servicio_cancha_futbol,
                            $alojamiento->servicio_cancha_futbol,
                            ['class' => 'custom-control-input', 'id' => 'servicio_cancha_futbol'],
                        ) !!}
                        {!! Form::label('servicio_cancha_futbol', 'Cancha de fútbol', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_cancha_futbol',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_billar', $alojamiento->servicio_billar, $alojamiento->servicio_billar, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_billar',
                        ]) !!}
                        {!! Form::label('servicio_billar', 'Billar / Pool', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_billar',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_ping_pong', $alojamiento->servicio_ping_pong, $alojamiento->servicio_ping_pong, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_ping_pong',
                        ]) !!}
                        {!! Form::label('servicio_ping_pong', 'Ping Pong', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_ping_pong',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_tejo', $alojamiento->servicio_tejo, $alojamiento->servicio_tejo, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_tejo',
                        ]) !!}
                        {!! Form::label('servicio_tejo', 'Mini tejo', ['class' => 'custom-control-label', 'for' => 'servicio_tejo']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_rana', $alojamiento->servicio_rana, $alojamiento->servicio_rana, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_rana',
                        ]) !!}
                        {!! Form::label('servicio_rana', 'Rana', ['class' => 'custom-control-label', 'for' => 'servicio_rana']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox(
                            'servicio_juegos_mesa',
                            $alojamiento->servicio_juegos_mesa,
                            $alojamiento->servicio_juegos_mesa,
                            ['class' => 'custom-control-input', 'id' => 'servicio_juegos_mesa'],
                        ) !!}
                        {!! Form::label('servicio_juegos_mesa', 'Juegos de mesa', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_juegos_mesa',
                        ]) !!}
                    </div>
                    <hr class="" />
                    <h4 class="verde">Seguridad</h4>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_extintor', $alojamiento->servicio_extintor, $alojamiento->servicio_extintor, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_extintor',
                        ]) !!}
                        {!! Form::label('servicio_extintor', 'Extintor de fuego', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_extintor',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_humo', $alojamiento->servicio_humo, $alojamiento->servicio_humo, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_humo',
                        ]) !!}
                        {!! Form::label('servicio_humo', 'Detector de humo', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_humo',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_alarma', $alojamiento->servicio_alarma, $alojamiento->servicio_alarma, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_alarma',
                        ]) !!}
                        {!! Form::label('servicio_alarma', 'Alarma', ['class' => 'custom-control-label', 'for' => 'servicio_alarma']) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_botiquin', $alojamiento->servicio_botiquin, $alojamiento->servicio_botiquin, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_botiquin',
                        ]) !!}
                        {!! Form::label('servicio_botiquin', 'Botiquín primeros auxilios', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_botiquin',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_monoxido', $alojamiento->servicio_monoxido, $alojamiento->servicio_monoxido, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_monoxido',
                        ]) !!}
                        {!! Form::label('servicio_monoxido', 'Detector de monóxido de carbono', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_monoxido',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('servicio_caja_seg', $alojamiento->servicio_caja_seg, $alojamiento->servicio_caja_seg, [
                            'class' => 'custom-control-input',
                            'id' => 'servicio_caja_seg',
                        ]) !!}
                        {!! Form::label('servicio_caja_seg', 'Caja de seguridad', [
                            'class' => 'custom-control-label',
                            'for' => 'servicio_caja_seg',
                        ]) !!}
                    </div>
                    @if ($alojamiento->tipo_alojamiento == 'GL' ||
                        $alojamiento->tipo_alojamiento == 'FH' ||
                        $alojamiento->tipo_alojamiento == 'HT')
                        <hr class="" />
                        <h4 class="verde">¿Ofrece algún tipo de comida tu alojamiento?</h4>
                        <div class="form-group custom-control custom-checkbox">
                            {!! Form::checkbox('servicio_desayuno', $alojamiento->servicio_desayuno, $alojamiento->servicio_desayuno, [
                                'class' => 'custom-control-input',
                                'id' => 'servicio_desayuno',
                            ]) !!}
                            {!! Form::label('servicio_desayuno', 'Desayuno', [
                                'class' => 'custom-control-label',
                                'for' => 'servicio_desayuno',
                            ]) !!}
                        </div>
                        <div class="form-group custom-control custom-checkbox">
                            {!! Form::checkbox('servicio_almuerzo', $alojamiento->servicio_almuerzo, $alojamiento->servicio_almuerzo, [
                                'class' => 'custom-control-input',
                                'id' => 'servicio_almuerzo',
                            ]) !!}
                            {!! Form::label('servicio_almuerzo', 'Almuerzo', [
                                'class' => 'custom-control-label',
                                'for' => 'servicio_almuerzo',
                            ]) !!}
                        </div>
                        <div class="form-group custom-control custom-checkbox">
                            {!! Form::checkbox('servicio_cena', $alojamiento->servicio_cena, $alojamiento->servicio_cena, [
                                'class' => 'custom-control-input',
                                'id' => 'servicio_cena',
                            ]) !!}
                            {!! Form::label('servicio_cena', 'Cena', ['class' => 'custom-control-label', 'for' => 'servicio_cena']) !!}
                        </div>
                    @endif
                    <br />
                    <h2>¿Deseas agregar algo que no esté detallado?</h2>
                    <div class="form-group">
                        {!! Form::text('servicio_adicional_nombre_1', $alojamiento->servicio_adicional_nombre_1, [
                            'class' => 'form-control',
                            'placeholder' => 'Servicio adicional 1',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('servicio_adicional_nombre_2', $alojamiento->servicio_adicional_nombre_2, [
                            'class' => 'form-control',
                            'placeholder' => 'Servicio adicional 2',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('servicio_adicional_nombre_3', $alojamiento->servicio_adicional_nombre_3, [
                            'class' => 'form-control',
                            'placeholder' => 'Servicio adicional 3',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('servicio_adicional_nombre_4', $alojamiento->servicio_adicional_nombre_4, [
                            'class' => 'form-control',
                            'placeholder' => 'Servicio adicional 4',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('servicio_adicional_nombre_5', $alojamiento->servicio_adicional_nombre_5, [
                            'class' => 'form-control',
                            'placeholder' => 'Servicio adicional 5',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                @endif
                @if ($paso == 4)
                    <h2>Cuéntanos que sitios de interés están cerca de tu alojamiento</h2>
                    <h3>Esta información es opcional, pero puede ser muy atractiva para tus húespedes.</h3>
                    <br />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_playa', $alojamiento->sitio_playa, $alojamiento->sitio_playa, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_playa',
                            'onClick' => '$(".sitio_playa_distancia").toggle()',
                        ]) !!}
                        {!! Form::label('sitio_playa', 'Playa', ['class' => 'custom-control-label', 'for' => 'sitio_playa']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_playa_distancia', $alojamiento->sitio_playa_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_playa ? 'inline' : 'none'),
                            'class' => 'form-control sitio_playa_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_rio', $alojamiento->sitio_rio, $alojamiento->sitio_rio, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_rio',
                            'onClick' => '$(".sitio_rio_distancia").toggle()',
                        ]) !!}
                        {!! Form::label('sitio_rio', 'Río', ['class' => 'custom-control-label', 'for' => 'sitio_rio']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_rio_distancia', $alojamiento->sitio_rio_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_rio ? 'inline' : 'none'),
                            'class' => 'form-control sitio_rio_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_parque', $alojamiento->sitio_parque, $alojamiento->sitio_parque, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_parque',
                            'onClick' => '$(".sitio_parque_distancia").toggle()',
                        ]) !!}
                        {!! Form::label('sitio_parque', 'Parque', ['class' => 'custom-control-label', 'for' => 'sitio_parque']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_parque_distancia', $alojamiento->sitio_parque_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_parque ? 'inline' : 'none'),
                            'class' => 'form-control sitio_parque_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox(
                            'sitio_sendero_caminar',
                            $alojamiento->sitio_sendero_caminar,
                            $alojamiento->sitio_sendero_caminar,
                            [
                                'class' => 'custom-control-input',
                                'id' => 'sitio_sendero_caminar',
                                'onClick' => '$(".sitio_sendero_caminar_distancia").toggle()',
                            ],
                        ) !!}
                        {!! Form::label('sitio_sendero_caminar', 'Sendero para caminar', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_sendero_caminar',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_sendero_caminar_distancia', $alojamiento->sitio_sendero_caminar_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_sendero_caminar ? 'inline' : 'none'),
                            'class' => 'form-control sitio_sendero_caminar_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox(
                            'sitio_sendero_ecologico',
                            $alojamiento->sitio_sendero_ecologico,
                            $alojamiento->sitio_sendero_ecologico,
                            [
                                'class' => 'custom-control-input',
                                'id' => 'sitio_sendero_ecologico',
                                'onClick' => '$(".sitio_sendero_ecologico_distancia").toggle()',
                            ],
                        ) !!}
                        {!! Form::label('sitio_sendero_ecologico', 'Sendero ecológico', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_sendero_ecologico',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_sendero_ecologico_distancia', $alojamiento->sitio_sendero_ecologico_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_sendero_ecologico ? 'inline' : 'none'),
                            'class' => 'form-control sitio_sendero_ecologico_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_ruta_bici', $alojamiento->sitio_ruta_bici, $alojamiento->sitio_ruta_bici, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_ruta_bici',
                            'onClick' => '$(".sitio_ruta_bici_distancia").toggle()',
                        ]) !!}
                        {!! Form::label('sitio_ruta_bici', 'Ruta para bicicleta', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_ruta_bici',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_ruta_bici_distancia', $alojamiento->sitio_ruta_bici_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_ruta_bici ? 'inline' : 'none'),
                            'class' => 'form-control sitio_ruta_bici_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_act_tur', $alojamiento->sitio_act_tur, $alojamiento->sitio_act_tur, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_act_tur',
                            'onClick' =>
                                '$(".sitio_act_tur_detalle_1").toggle();$(".sitio_act_tur_detalle_2").toggle();$(".sitio_act_tur_detalle_3").toggle();$(".sitio_act_tur_detalle_4").toggle();$(".sitio_act_tur_detalle_5").toggle();',
                        ]) !!}
                        {!! Form::label('sitio_act_tur', 'Actividad turística', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_act_tur',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_tur_detalle_1', $alojamiento->sitio_act_tur_detalle_1, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_tur ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_tur_detalle_1',
                            'placeholder' => 'Detalla actividad 1',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_tur_detalle_2', $alojamiento->sitio_act_tur_detalle_2, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_tur ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_tur_detalle_2',
                            'placeholder' => 'Detalla actividad 2',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_tur_detalle_3', $alojamiento->sitio_act_tur_detalle_3, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_tur ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_tur_detalle_3',
                            'placeholder' => 'Detalla actividad 3',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_tur_detalle_4', $alojamiento->sitio_act_tur_detalle_4, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_tur ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_tur_detalle_4',
                            'placeholder' => 'Detalla actividad 4',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_tur_detalle_5', $alojamiento->sitio_act_tur_detalle_5, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_tur ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_tur_detalle_5',
                            'placeholder' => 'Detalla actividad 5',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <br />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_parque_tem', $alojamiento->sitio_parque_tem, $alojamiento->sitio_parque_tem, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_parque_tem',
                            'onClick' => '$(".sitio_parque_tem_nombre").toggle(); $(".sitio_parque_tem_distancia").toggle(); ',
                        ]) !!}
                        {!! Form::label('sitio_parque_tem', 'Parque temático', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_parque_tem',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_parque_tem_nombre', $alojamiento->sitio_parque_tem_nombre, [
                            'style' => 'display: ' . ($alojamiento->sitio_parque_tem ? 'inline' : 'none'),
                            'class' => 'form-control sitio_parque_tem_nombre',
                            'placeholder' => 'Nombre del parque',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_parque_tem_distancia', $alojamiento->sitio_parque_tem_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_parque_tem ? 'inline' : 'none'),
                            'class' => 'form-control sitio_parque_tem_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_parque_div', $alojamiento->sitio_parque_div, $alojamiento->sitio_parque_div, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_parque_div',
                            'onClick' => '$(".sitio_parque_div_nombre").toggle(); $(".sitio_parque_div_distancia").toggle(); ',
                        ]) !!}
                        {!! Form::label('sitio_parque_div', 'Parque de diversiones', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_parque_div',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_parque_div_nombre', $alojamiento->sitio_parque_div_nombre, [
                            'style' => 'display: ' . ($alojamiento->sitio_parque_div ? 'inline' : 'none'),
                            'class' => 'form-control sitio_parque_div_nombre',
                            'placeholder' => 'Nombre del parque',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_parque_div_distancia', $alojamiento->sitio_parque_div_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_parque_div ? 'inline' : 'none'),
                            'class' => 'form-control sitio_parque_div_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_parque_acua', $alojamiento->sitio_parque_acua, $alojamiento->sitio_parque_acua, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_parque_acua',
                            'onClick' => '$(".sitio_parque_acua_nombre").toggle(); $(".sitio_parque_acua_distancia").toggle(); ',
                        ]) !!}
                        {!! Form::label('sitio_parque_acua', 'Parque acuático', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_parque_acua',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_parque_acua_nombre', $alojamiento->sitio_parque_acua_nombre, [
                            'style' => 'display: ' . ($alojamiento->sitio_parque_acua ? 'inline' : 'none'),
                            'class' => 'form-control sitio_parque_acua_nombre',
                            'placeholder' => 'Nombre del parque',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_parque_acua_distancia', $alojamiento->sitio_parque_acua_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_parque_acua ? 'inline' : 'none'),
                            'class' => 'form-control sitio_parque_acua_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_pesca', $alojamiento->sitio_pesca, $alojamiento->sitio_pesca, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_pesca',
                            'onClick' => '$(".sitio_pesca_distancia").toggle()',
                        ]) !!}
                        {!! Form::label('sitio_pesca', 'Lugar de pesca', ['class' => 'custom-control-label', 'for' => 'sitio_pesca']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_pesca_distancia', $alojamiento->sitio_pesca_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_pesca ? 'inline' : 'none'),
                            'class' => 'form-control sitio_pesca_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_act_dep', $alojamiento->sitio_act_dep, $alojamiento->sitio_act_dep, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_act_dep',
                            'onClick' =>
                                '$(".sitio_act_dep_detalle_1").toggle();$(".sitio_act_dep_detalle_2").toggle();$(".sitio_act_dep_detalle_3").toggle();$(".sitio_act_dep_detalle_4").toggle();$(".sitio_act_dep_detalle_5").toggle();',
                        ]) !!}
                        {!! Form::label('sitio_act_dep', 'Actividad deportiva', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_act_dep',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_dep_detalle_1', $alojamiento->sitio_act_dep_detalle_1, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_dep ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_dep_detalle_1',
                            'placeholder' => 'Detalla actividad 1',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_dep_detalle_2', $alojamiento->sitio_act_dep_detalle_2, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_dep ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_dep_detalle_2',
                            'placeholder' => 'Detalla actividad 2',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_dep_detalle_3', $alojamiento->sitio_act_dep_detalle_3, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_dep ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_dep_detalle_3',
                            'placeholder' => 'Detalla actividad 3',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_dep_detalle_4', $alojamiento->sitio_act_dep_detalle_4, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_dep ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_dep_detalle_4',
                            'placeholder' => 'Detalla actividad 4',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_act_dep_detalle_5', $alojamiento->sitio_act_dep_detalle_5, [
                            'style' => 'display: ' . ($alojamiento->sitio_act_dep ? 'inline' : 'none'),
                            'class' => 'form-control sitio_act_dep_detalle_5',
                            'placeholder' => 'Detalla actividad 5',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <br />
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_sup', $alojamiento->sitio_sup, $alojamiento->sitio_sup, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_sup',
                            'onClick' => '$(".sitio_sup_nombre").toggle(); $(".sitio_sup_distancia").toggle(); ',
                        ]) !!}
                        {!! Form::label('sitio_sup', 'Supermercado', ['class' => 'custom-control-label', 'for' => 'sitio_sup']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_sup_nombre', $alojamiento->sitio_sup_nombre, [
                            'style' => 'display: ' . ($alojamiento->sitio_sup ? 'inline' : 'none'),
                            'class' => 'form-control sitio_sup_nombre',
                            'placeholder' => 'Nombre del supermercado',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_sup_distancia', $alojamiento->sitio_sup_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_sup ? 'inline' : 'none'),
                            'class' => 'form-control sitio_sup_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_drog', $alojamiento->sitio_drog, $alojamiento->sitio_drog, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_drog',
                            'onClick' => '$(".sitio_drog_nombre").toggle(); $(".sitio_drog_distancia").toggle(); ',
                        ]) !!}
                        {!! Form::label('sitio_drog', 'Droguería', ['class' => 'custom-control-label', 'for' => 'sitio_drog']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_drog_nombre', $alojamiento->sitio_drog_nombre, [
                            'style' => 'display: ' . ($alojamiento->sitio_drog ? 'inline' : 'none'),
                            'class' => 'form-control sitio_drog_nombre',
                            'placeholder' => 'Nombre de la droguería',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_drog_distancia', $alojamiento->sitio_drog_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_drog ? 'inline' : 'none'),
                            'class' => 'form-control sitio_drog_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_centro_com', $alojamiento->sitio_centro_com, $alojamiento->sitio_centro_com, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_centro_com',
                            'onClick' =>
                                '$(".sitio_centro_com_nombre_1").toggle();$(".sitio_centro_com_nombre_2").toggle();$(".sitio_centro_com_nombre_3").toggle();$(".sitio_centro_com_nombre_4").toggle();$(".sitio_centro_com_nombre_5").toggle();',
                        ]) !!}
                        {!! Form::label('sitio_centro_com', 'Centro Comercial', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_centro_com',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_centro_com_nombre_1', $alojamiento->sitio_centro_com_nombre_1, [
                            'style' => 'display: ' . ($alojamiento->sitio_centro_com ? 'inline' : 'none'),
                            'class' => 'form-control sitio_centro_com_nombre_1',
                            'placeholder' => 'Nombre del centro comercial 1',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_centro_com_nombre_2', $alojamiento->sitio_centro_com_nombre_2, [
                            'style' => 'display: ' . ($alojamiento->sitio_centro_com ? 'inline' : 'none'),
                            'class' => 'form-control sitio_centro_com_nombre_2',
                            'placeholder' => 'Nombre del centro comercial 2',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_centro_com_nombre_3', $alojamiento->sitio_centro_com_nombre_3, [
                            'style' => 'display: ' . ($alojamiento->sitio_centro_com ? 'inline' : 'none'),
                            'class' => 'form-control sitio_centro_com_nombre_3',
                            'placeholder' => 'Nombre del centro comercial 3',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_centro_com_nombre_4', $alojamiento->sitio_centro_com_nombre_4, [
                            'style' => 'display: ' . ($alojamiento->sitio_centro_com ? 'inline' : 'none'),
                            'class' => 'form-control sitio_centro_com_nombre_4',
                            'placeholder' => 'Nombre del centro comercial 4',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_centro_com_nombre_5', $alojamiento->sitio_centro_com_nombre_5, [
                            'style' => 'display: ' . ($alojamiento->sitio_centro_com ? 'inline' : 'none'),
                            'class' => 'form-control sitio_centro_com_nombre_5',
                            'placeholder' => 'Nombre del centro comercial 5',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_rest', $alojamiento->sitio_rest, $alojamiento->sitio_rest, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_rest',
                            'onClick' =>
                                '$(".sitio_rest_nombre_1").toggle();$(".sitio_rest_nombre_2").toggle();$(".sitio_rest_nombre_3").toggle();$(".sitio_rest_nombre_4").toggle();$(".sitio_rest_nombre_5").toggle();',
                        ]) !!}
                        {!! Form::label('sitio_rest', 'Restaurante', ['class' => 'custom-control-label', 'for' => 'sitio_rest']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_rest_nombre_1', $alojamiento->sitio_rest_nombre_1, [
                            'style' => 'display: ' . ($alojamiento->sitio_rest ? 'inline' : 'none'),
                            'class' => 'form-control sitio_rest_nombre_1',
                            'placeholder' => 'Nombre del restaurante 1',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_rest_nombre_2', $alojamiento->sitio_rest_nombre_2, [
                            'style' => 'display: ' . ($alojamiento->sitio_rest ? 'inline' : 'none'),
                            'class' => 'form-control sitio_rest_nombre_2',
                            'placeholder' => 'Nombre del restaurante 2',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_rest_nombre_3', $alojamiento->sitio_rest_nombre_3, [
                            'style' => 'display: ' . ($alojamiento->sitio_rest ? 'inline' : 'none'),
                            'class' => 'form-control sitio_rest_nombre_3',
                            'placeholder' => 'Nombre del restaurante 3',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_rest_nombre_4', $alojamiento->sitio_rest_nombre_4, [
                            'style' => 'display: ' . ($alojamiento->sitio_rest ? 'inline' : 'none'),
                            'class' => 'form-control sitio_rest_nombre_4',
                            'placeholder' => 'Nombre del restaurante 4',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_rest_nombre_5', $alojamiento->sitio_rest_nombre_5, [
                            'style' => 'display: ' . ($alojamiento->sitio_rest ? 'inline' : 'none'),
                            'class' => 'form-control sitio_rest_nombre_5',
                            'placeholder' => 'Nombre del restaurante 5',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_gimnasio', $alojamiento->sitio_gimnasio, $alojamiento->sitio_gimnasio, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_gimnasio',
                            'onClick' => '$(".sitio_gimnasio_distancia").toggle()',
                        ]) !!}
                        {!! Form::label('sitio_gimnasio', 'Gimnasio', ['class' => 'custom-control-label', 'for' => 'sitio_gimnasio']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_gimnasio_distancia', $alojamiento->sitio_gimnasio_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_gimnasio ? 'inline' : 'none'),
                            'class' => 'form-control sitio_gimnasio_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_iglesia', $alojamiento->sitio_iglesia, $alojamiento->sitio_iglesia, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_iglesia',
                            'onClick' => '$(".sitio_iglesia_distancia").toggle()',
                        ]) !!}
                        {!! Form::label('sitio_iglesia', 'Iglesia', ['class' => 'custom-control-label', 'for' => 'sitio_iglesia']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_iglesia_distancia', $alojamiento->sitio_iglesia_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_iglesia ? 'inline' : 'none'),
                            'class' => 'form-control sitio_iglesia_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_hospital', $alojamiento->sitio_hospital, $alojamiento->sitio_hospital, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_hospital',
                            'onClick' => '$(".sitio_hospital_nombre").toggle(); $(".sitio_hospital_distancia").toggle(); ',
                        ]) !!}
                        {!! Form::label('sitio_hospital', 'Hospital / Clínica', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_hospital',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_hospital_nombre', $alojamiento->sitio_hospital_nombre, [
                            'style' => 'display: ' . ($alojamiento->sitio_hospital ? 'inline' : 'none'),
                            'class' => 'form-control sitio_hospital_nombre',
                            'placeholder' => 'Nombre del hospital / clínica',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_hospital_distancia', $alojamiento->sitio_hospital_distancia, [
                            'style' => 'display: ' . ($alojamiento->sitio_hospital ? 'inline' : 'none'),
                            'class' => 'form-control sitio_hospital_distancia',
                            'placeholder' => '¿A qué distancia se encuentra?',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        {!! Form::checkbox('sitio_transporte', $alojamiento->sitio_transporte, $alojamiento->sitio_transporte, [
                            'class' => 'custom-control-input',
                            'id' => 'sitio_transporte',
                        ]) !!}
                        {!! Form::label('sitio_transporte', 'Transporte público', [
                            'class' => 'custom-control-label',
                            'for' => 'sitio_transporte',
                        ]) !!}
                    </div>
                    <br />
                    <br />
                    <h2>¿Tienes algún sitio de interés o experiencia que desees recomendar a tus húespedes durante la
                        estadía en tu alojamiento?</h2>
                    <h3>(Opcional)</h3>
                    <div class="form-group">
                        {!! Form::text('sitio_adicional_nombre_1', $alojamiento->sitio_adicional_nombre_1, [
                            'class' => 'form-control sitio_adicional_nombre_1',
                            'placeholder' => 'Agregar sitio de interés 1',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_adicional_nombre_2', $alojamiento->sitio_adicional_nombre_2, [
                            'class' => 'form-control sitio_adicional_nombre_2',
                            'placeholder' => 'Agregar sitio de interés 2',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_adicional_nombre_3', $alojamiento->sitio_adicional_nombre_3, [
                            'class' => 'form-control sitio_adicional_nombre_3',
                            'placeholder' => 'Agregar sitio de interés 3',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_adicional_nombre_4', $alojamiento->sitio_adicional_nombre_4, [
                            'class' => 'form-control sitio_adicional_nombre_4',
                            'placeholder' => 'Agregar sitio de interés 4',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::text('sitio_adicional_nombre_5', $alojamiento->sitio_adicional_nombre_5, [
                            'class' => 'form-control sitio_adicional_nombre_5',
                            'placeholder' => 'Agregar sitio de interés 5',
                            'maxlength' => '40',
                        ]) !!}
                    </div>
                @endif
                @if ($paso == 5)
                    <h2>Toma fotos que muestren tu propiedad y que sean atractivas para los húespedes</h2>
                    <h3>Toma fotos con un celular o una cámara. Debes subir al menos una foto para publicar tu anuncio.
                        Podrás agregar nuevas o editarlas más adelante si lo deseas.</h3>
                    <br />
                    <h3>En la plataforma NO esta permitido incluir en ninguno de los campos, ni en las fotografías datos de contacto e información personal como:</h3>
                    <ul>
                        <li style="list-style-type: disc;"><h3>e-mail, teléfono, WhatsApp o links.</h3></li>
                        <li style="list-style-type: disc;"><h3>Marcas de agua, logos, códigos QR o textos.</h3></li>
                    </ul>
                    <h3>De NO cumplir con las condiciones, los administradores podrán modificar la publicación e incluso dar de baja tú propiedad.</h3>
                    <br />
                    <img class="icono" src="{{ url('/img/consejo.svg') }}"><a class="btn boton_accion boton_eliminar"
                        data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
                        aria-controls="collapseExample">Consejos para tomar fotografías</a><br />
                    <div class="collapse" id="collapseExample">
                        <br />
                        - Elige las mejores fotos de tu alojamiento, recuerda que la primera impresión es muy
                        importante.<br /><br />
                        - Puedes tomar tus fotos con un celular con buena nitidez o una cámara.<br /><br />
                        - Las fotos horizontales te ayudarán a tener una visión más amplia de los espacios.<br /><br />
                        - Toma fotos con buena luz, que resalten lo mejor de tu alojamiento, no dejes por fuera ninguno de
                        los espacios que consideres importante para mostrar a tus huéspedes.<br /><br />
                        - Toma fotos variadas, que muestres todos los espacios que pueden disfrutar los huéspedes de tu
                        alojamiento.<br /><br />
                        - Debes subir al menos 1 foto para poder publicar tu alojamiento.<br /><br />
                        - Puedes agregar, quitar o cambiar el orden de tus fotos en el momento que lo desees.<br /><br />
                        - Por último verifica que todas tus fotos hayan salido con buena resolución, que no salgan movidas o
                        borrosas.<br /><br />
                    </div>
                    <br>
                    <br>
                    <br>
                    <h2 style="text-align: center;">¡Agregá todas las fotos de una sola vez!</h2>
                    <br>
                    <div style="display: flex; flex-flow: column wrap;align-items: center;">
                        <input class="__files" type='file' name='files[]' accept="image/gif, image/jpeg, image/jpg, image/png" multiple style="margin-bottom: 1rem;">
                        {!! Form::hidden('alojamiento_id', $alojamiento->id) !!}
                        <button class="btn boton_accion boton_eliminar" type='submit' value="saveImages" name="navegacion">Guardar Fotos</button>
                    </div>
                    <br>
                    <br>
                    <br>
                    <?php
                        $alojamientoFoto = App\AlojamientoFoto::where('alojamiento_id', $alojamiento->id)->first();
                    ?>
                    @if($alojamientoFoto)
                    <a class="btn boton_accion boton_eliminar" style="display: flex; justify-content: space-around;" href="/images/{{$alojamiento->id}}">Reordenar fotos</a>
                    @endif
                    <br>
                    <br>
                    <div class="form-row grupo-foto">
                        <?php
                        
                        $mostrarFotoVacia = true;
                        
                        ?>
                        @for ($iFoto = 1; $iFoto <= 30; $iFoto++)
                            <?php
                            
                            $alojamientoFoto = App\AlojamientoFoto::where('alojamiento_id', $alojamiento->id)
                                ->where('num_foto', $iFoto)
                                ->first();
                            
                            $sourceImagen = '';
                            
                            $boton_foto = 'boton_foto_nueva';
                            
                            if (!is_null($alojamientoFoto)) {
                                $sourceImagen = URL::to('/uploads/' . $alojamientoFoto->archivo);
                            
                                $boton_foto = 'boton_foto';
                            }
                            
                            ?>
                            @if ($sourceImagen == '' || $sourceImagen == null)
                                @if ($mostrarFotoVacia)
                                    <div id="grupoFoto{{ $iFoto }}" class="form-group col-md-6">
                                        <?php
                                        
                                        $mostrarFotoVacia = false;
                                        
                                        ?>
                                    @else
                                        <div id="grupoFoto{{ $iFoto }}" class="form-group col-md-6"
                                            style="display: none;">
                                @endif
                            @else
                                <div id="grupoFoto{{ $iFoto }}" class="form-group col-md-6">
                            @endif
                            <div class="card">
                                @if ($iFoto == 1)
                                    <div class="card-header">Foto principal (horizontal)</div>
                                @else
                                    <div class="card-header">Foto {{ $iFoto }}</div>
                                @endif
                                <div class="card-body">
                                    <img id="fotoVista{{ $iFoto }}" src="{{ $sourceImagen }}"
                                        style="max-width: 100%; width: 100%;" />
                                    <label class="btn boton_accion {{ $boton_foto }}">
                                        Seleccionar foto <input id="foto{{ $iFoto }}" name="foto{{ $iFoto }}"
                                            type="file" hidden
                                            onchange="previewImage('foto{{ $iFoto }}', 'fotoVista{{ $iFoto }}', {{ $iFoto }})">
                                    </label>
                                    {!! Form::hidden('fotoBorrar' . $iFoto, null, ['id' => 'fotoBorrar' . $iFoto]) !!}
                                    @if ($alojamientoFoto != null)
                                        <scan id="bloque_borrar_archivo{{ $iFoto }}">
                                            <a style="float: right;" class="btn boton_accion boton_eliminar"
                                                href="#"
                                                onclick="$('#fotoBorrar{{ $iFoto }}').val('SI'); $('#fotoVista{{ $iFoto }}').attr('src',''); $('#bloque_borrar_archivo{{ $iFoto }}').css('display','none'); $('#grupoFoto{{ $iFoto }} .boton_foto').removeClass('boton_foto').addClass('boton_foto_nueva') ; return false;">Borrar</a>
                                        </scan>
                                    @endif
                                </div>
                            </div>
                    </div>
                @endfor
            </div>
            @endif
            @if ($paso == 6)
                <h2>Describe tu propiedad</h2>
                <h3>Cuéntanos qué es lo que más te gusta de tu alojamiento, qué espacios la caracterizan, descríbela por
                    dentro y por fuera, cuales son las áreas en las que sobresalen de tu propiedad y te hacen sentir
                    orgulloso de ellas.
                    <br />
                    <br />
                    Cuéntale a tus huéspedes porque debería elegir tu propiedad para alojarse.
                </h3>
                <br />
                <h3>
                    Recuerda que <b>NO</b> debes incluir información personal como e-mails, teléfonos, WhatsApp o links. 
                </h3>
                <br />
                <div class="form-group">
                    {!! Form::textarea('descripcion', $alojamiento->descripcion, [
                        'class' => 'form-control',
                        'placeholder' => 'Descripción',
                        'maxlength' => '1500',
                        'required' => 'required',
                    ]) !!}
                    <small class="form-text text-muted">Descripción hasta 1.500 caracteres.</small>
                </div>
                <br />
                <br />
                <h2>Describe la zona o barrio donde se encuentra tu alojamiento</h2>
                <h3>(Opcional)</h3>
                <br />
                <div class="form-group">
                    {!! Form::textarea('zona', $alojamiento->zona, ['class' => 'form-control', 'maxlength' => '1500']) !!}
                    <small class="form-text text-muted">Descripción hasta 1.500 caracteres.</small>
                </div>
                <br />
                <h2>Elije un título llamativo para tu alojamiento</h2>
                <div class="form-group">
                    {!! Form::text('titulo', $alojamiento->titulo, [
                        'class' => 'form-control',
                        'maxlength' => '100',
                        'required' => 'required',
                    ]) !!}
                    <small class="form-text text-muted">Descripción hasta 150 caracteres.</small>
                </div>
                <br />
            @endif
            @if ($paso == 7)
                <h2>Reglas de la casa</h2>
                <br />
                <div class="form-group custom-control custom-switch">
                    {!! Form::checkbox('regla_mascotas', $alojamiento->regla_mascotas, $alojamiento->regla_mascotas, [
                        'class' => 'custom-control-input',
                        'id' => 'regla_mascotas',
                    ]) !!}
                    {!! Form::label('regla_mascotas', 'Acepto mascotas', [
                        'class' => 'custom-control-label',
                        'for' => 'regla_mascotas',
                    ]) !!}
                </div>
                <div class="form-group custom-control custom-switch">
                    {!! Form::checkbox('regla_fumadores', $alojamiento->regla_fumadores, $alojamiento->regla_fumadores, [
                        'class' => 'custom-control-input',
                        'id' => 'regla_fumadores',
                    ]) !!}
                    {!! Form::label('regla_fumadores', 'Apto para fumadores', [
                        'class' => 'custom-control-label',
                        'for' => 'regla_fumadores',
                    ]) !!}
                </div>
                <div class="form-group custom-control custom-switch">
                    {!! Form::checkbox('regla_fiestas', $alojamiento->regla_fiestas, $alojamiento->regla_fiestas, [
                        'class' => 'custom-control-input',
                        'id' => 'regla_fiestas',
                    ]) !!}
                    {!! Form::label('regla_fiestas', 'Se permiten fiestas o eventos', [
                        'class' => 'custom-control-label',
                        'for' => 'regla_fiestas',
                    ]) !!}
                </div>
                <br />
                <h3>Reglas adicionales</h3>
                <div class="form-group">
                    {!! Form::text('regla_adicional_1', $alojamiento->regla_adicional_1, [
                        'class' => 'form-control',
                        'placeholder' => 'Detallar regla 1',
                        'maxlength' => '40',
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::text('regla_adicional_2', $alojamiento->regla_adicional_2, [
                        'class' => 'form-control',
                        'placeholder' => 'Detallar regla 2',
                        'maxlength' => '40',
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::text('regla_adicional_3', $alojamiento->regla_adicional_3, [
                        'class' => 'form-control',
                        'placeholder' => 'Detallar regla 3',
                        'maxlength' => '40',
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::text('regla_adicional_4', $alojamiento->regla_adicional_4, [
                        'class' => 'form-control',
                        'placeholder' => 'Detallar regla 4',
                        'maxlength' => '40',
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::text('regla_adicional_5', $alojamiento->regla_adicional_5, [
                        'class' => 'form-control',
                        'placeholder' => 'Detallar regla 5',
                        'maxlength' => '40',
                    ]) !!}
                </div>
                <br />
                <br />
                <h2>Ckeck In / Check Out</h2>
                <h3>Detalla los horarios de llegada y salida de tu alojamiento.</h3>
                <br />
                <div class="form-group">
                    {!! Form::label('check_in', 'Check In') !!}
                    {!! Form::time('check_in', $alojamiento->check_in, ['class' => 'form-control', 'required' => 'required']) !!}
                    <small class="form-text text-muted">Desde</small>
                </div>
                <div class="form-group">
                    {!! Form::label('check_out', 'Check Out') !!}
                    {!! Form::time('check_out', $alojamiento->check_out, ['class' => 'form-control', 'required' => 'required']) !!}
                    <small class="form-text text-muted">Hasta</small>
                </div>
                <h3>*Te recomendamos ponerte en contacto con tus huéspedes para coordinar los horarios de llegada y salida.
                </h3>
            @endif
            @if ($paso == 8)
                <h2>Solicitudes de reservas</h2>
                <h4>Todas las solicitudes de reserva serán enviadas a tu correo electrónico, las puedes ACEPTAR o RECHAZAR.
                    Tienes 24 horas para contestar sino será cancelada la reserva, entre más rápido respondas más tiempo
                    tendrán tus huéspedes para organizar su viaje. </h4>
                <br />
                <h2>Precio del alojamiento por noche</h2>
                <h4>Los valores deben ingresarse en pesos colombianos. No aceptamos ningún otro tipo de moneda. Si no estas
                    seguro de las temporadas mira aquí nuestro calendario.</h4>
                <br />
                <img class="icono" src="{{ url('/img/tarifas.svg') }}"><a class="btn boton_accion boton_eliminar"
                    data-toggle="collapse" href="#collapseTemporadas" role="button" aria-expanded="false"
                    aria-controls="collapseTemporadas">Ver calendario según temporadas</a>
                <br /><br />
                <div class="collapse" id="collapseTemporadas">
                    <br />
                    <table class="calendar" id="calendar">
                        <thead>
                            <tr>
                                <td colspan="7" style="border: none; padding: 2px;">
                                    <p id="monthAndYear"></p>
                                    <div style="float: right;">
                                        <button class="btn btn-light" id="previous" onclick="previous___()"
                                            type="button">Anterior</button>
                                        <button class="btn btn-light" id="next" onclick="next___()"
                                            type="button">Siguiente</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Dom</th>
                                <th>Lun</th>
                                <th>Mar</th>
                                <th>Mie</th>
                                <th>Jue</th>
                                <th>Vie</th>
                                <th>Sab</th>
                            </tr>
                        </thead>
                        <tbody id="calendar-body">
                        </tbody>
                    </table>
                    <br /><br />
                    <div class=" temporada"><label class="temporada_baja">Temporada Baja</label></div>
                    Comprende todos los días entre semana y fines de semana que no sean puente.<br />
                    <i>Colócale una tarifa atractiva a tú alojamiento para atraer huéspedes cuando haya poca
                        demanda.</i><br /><br />
                    <div class=" temporada"><label class="temporada_media">Temporada Media</label></div>
                    Son todos los puentes festivos del año, semana santa, y las siguientes fechas:<br />
                    - Del 15 de Junio al 20 de Julio<br />
                    - Del 1 de Diciembre al 15 de Diciembre.<br />
                    - Del 16 de Enero al 31 de Enero.<br />
                    <i>Marca la diferencia con tu tarifa y asegura el alquiler de tu alojamiento con tiempo.</i><br /><br />
                    <div class=" temporada"><label class="temporada_alta">Temporada Alta</label></div>
                    Comprende toda la semana santa y la temporada de fin de año del 16 de Diciembre al 15 de Enero.<br />
                    <i>Es el momento de Alojar toda la temporada más demandada del año, ofrece una tarifa competitiva,
                        diferénciate de los demás!</i><br /><br /><br />
                </div>
                <h4>Al precio que coloques se te descontará un 3% en tu liquidación final correspondiente a mantenimiento de
                    la plataforma y gastos administrativos.</h4>
                <br />
                <br />
                <h2>¿Alquiler total o por cantidad huespedes?</h2>
                <H3><b>Total</b>: Es el precio del alojamiento, por la cantidad de noches.</H3>
                <H3><b>Huéspedes</b>: Es el precio del alojamiento por huésped, multiplicado por la cantidad de huéspedes más la cantidad de noches.</H3>
                <ul>
                    <li>La cantidad mínima de huéspedes, es el precio mínimo de quieres alquilar tu propiedad por noche.</li>
                    <br />
                    <li>Ejemplo:
                        <br />
                        Precio por huésped: $50.000
                        <br />
                        Cantidad de Noches: 5
                        <br />
                        Mínimo de Huéspedes: 10
                        <br />
                        Estadía por noche desde: $50.000 ($50.000 x 5) x 10 huéspedes.
                        <br />
                    </li>
                </ul>
                <div class="form-group">
                    <?php
                        $huespedesActivado = false;
                        $estiloHuespedes = 'none';
                        if ($alojamiento->tipo_alquiler == "HU") {
                            $huespedesActivado = true;
                            $estiloHuespedes = 'block';
                        }
                    ?>
                    <div class="form-group custom-control custom-switch col-sm-12" style="padding-left: 15px">
                        <select name="tipo_alquiler" id="tipo_alquiler" class="form-control" style="" onchange='$("#seccionHuespedes").toggle()'>
                            @if ($alojamiento->tipo_alquiler == null || $alojamiento->tipo_alquiler == 'TO')
                                <option selected="selected" value="TO">Total</option>
                                <option value="HU">Huespedes</option>
                            @else
                                <option value="TO">Total</option>
                                <option selected="selected" value="HU">Huespedes</option>
                            @endif
                        </select>
                    </div>
                    <br>
                    <div id="seccionHuespedes" style="display:{{ $estiloHuespedes }}">
                        {!! Form::label('huespedes_min', 'Cantidad mínima de huespedes', ['class' => '']) !!}
                        {{-- <span class="prefijo-pesos">$</span> --}}
                        {{-- {!! Form::tel('precio_deposito', $alojamiento->precio_deposito, ['class' => 'prefijo-pesos-input form-control']) !!} --}}
                        {!! Form::number('huespedes_min', $alojamiento->huespedes_min, [
                            'step' => '1',
                            'min' => '1',
                            'max' => '999',
                            'class' => 'form-control',
                            'required',
                        ]) !!}
                        <br>
                    </div>
                </div>
                <div class="form-group temporada">
                    {!! Form::label('precio_alta', 'Temporada alta', ['class' => 'temporada_alta']) !!}
                    <span class="prefijo-pesos-temporada">$</span>
                    {!! Form::tel('precio_alta', $alojamiento->precio_alta, [
                            'step' => '1',
                            'min' => '0',
                            'max' => '99999999',
                            'class' => 'form-control',
                            'required',
                        ]) !!}
                </div>
                <div class="form-group temporada">
                    {!! Form::label('precio_media', 'Temporada media', ['class' => 'temporada_media']) !!}
                    <span class="prefijo-pesos-temporada">$</span>
                    {{-- {!! Form::tel('precio_media', $alojamiento->precio_media, ['class' => 'form-control', 'required']) !!} --}}
                    {!! Form::tel('precio_media', $alojamiento->precio_media, [
                            'step' => '1',
                            'min' => '0',
                            'max' => '99999999',
                            'class' => 'form-control',
                            'required',
                        ]) !!}
                </div>
                <div class="form-group temporada">
                    {!! Form::label('precio_baja', 'Temporada baja', ['class' => 'temporada_baja']) !!}
                    <span class="prefijo-pesos-temporada">$</span>
                    {{-- {!! Form::tel('precio_baja', $alojamiento->precio_baja, ['class' => 'form-control', 'required']) !!} --}}
                    {!! Form::tel('precio_baja', $alojamiento->precio_baja, [
                            'step' => '1',
                            'min' => '0',
                            'max' => '99999999',
                            'class' => 'form-control',
                            'required',
                        ]) !!}
                </div>
                <br />
                @if ($alojamiento->tipo_alojamiento == 'CB' ||
                    $alojamiento->tipo_alojamiento == 'FN' ||
                    $alojamiento->tipo_alojamiento == 'AP' ||
                    $alojamiento->tipo_alojamiento == 'CS')
                    <h2>Valores Fijos</h2>
                    <br />
                    <div class="form-group">
                        {!! Form::label('precio_limpieza', 'Servicio de limpieza', ['class' => '']) !!}
                        {{-- <span class="prefijo-pesos">$</span> --}}
                        {{-- {!! Form::tel('precio_limpieza', $alojamiento->precio_limpieza, ['class' => 'prefijo-pesos-input form-control']) !!} --}}
                        {!! Form::tel('precio_limpieza', $alojamiento->precio_limpieza, [
                            'step' => '1',
                            'min' => '0',
                            'max' => '9999999',
                            'class' => 'form-control',
                            'required',
                        ]) !!}
                        <small class="form-text text-muted">* Es el dinero que destinas a limpiar tu propiedad, una vez los
                            huéspedes hayan terminado su estadía.</small>
                    </div>
                @endif
                <br />
                <h2>Depósito reembolsable (Opcional)</h2>
                <br />
                <div class="form-group">
                    <?php
                    
                    $depositoActivado = false;
                    
                    $estiloDeposito = 'none';
                    
                    if ($alojamiento->precio_deposito != null) {
                        $depositoActivado = true;
                    
                        $estiloDeposito = 'block';
                    }
                    
                    ?>
                    <div class="form-group custom-control custom-switch">
                        {!! Form::checkbox('deposito', $depositoActivado, $depositoActivado, [
                            'class' => 'custom-control-input',
                            'id' => 'deposito',
                        ]) !!}
                        {!! Form::label('deposito', 'Activar depósito reembolsable', [
                            'class' => 'custom-control-label',
                            'for' => 'deposito',
                            'onclick' => '$("#seccionDeposito").toggle()',
                        ]) !!}
                    </div>
                    <h5>En muchas ocasiones nuestros alojadores solicitan un depósito en efectivo ante cualquier
                        eventualidad o daño al alojamiento por parte de los huéspedes, este depósito te lo darán a su
                        llegada y se debe reembolsar al final de la estadía si en tu alojamiento no se presentaron daños o
                        faltantes. <br /><br />- Es opcional, actívalo y desactívalo como consideres que será más adecuado
                        para tu alojamiento.<br /><br />- Si colocas la opción de Depósito Reembolsable debes tener en
                        cuenta que debe haber una persona que reciba a los huéspedes a su llegada, realice un inventario y
                        reciba el depósito. Al finalizar su estadía deberá revisar que no se presenten daños o faltantes en
                        la propiedad para que este sea reembolsado.</h5>
                    <br />
                    <div id="seccionDeposito" style="display:{{ $estiloDeposito }}">
                        {!! Form::label('precio_deposito', 'Valor de depósito', ['class' => '']) !!}
                        {{-- <span class="prefijo-pesos">$</span> --}}
                        {{-- {!! Form::tel('precio_deposito', $alojamiento->precio_deposito, ['class' => 'prefijo-pesos-input form-control']) !!} --}}
                        {!! Form::tel('precio_deposito', $alojamiento->precio_deposito, [
                            'step' => '1',
                            'min' => '0',
                            'max' => '9999999',
                            'class' => 'form-control',
                        ]) !!}
                    </div>
                </div>
                {{-- <br/>
          <h2>Seguro de protección para alojadores</h2>
          <h4>Es poco probable que un huésped cause daños a tu Alojamiento o a tus pertenencias durante una estadía y no te reembolse los costos. Aloja Colombia cuenta con una cobertura de responsabilidad civil y daños a tu propiedad de hasta $500.000.000 COP</h4>
          <br/>
          <br/> --}}
                <h2>Alquiler mínimo de noches en temporada alta</h2>
                <h4>Opcional</h4>
                <br />
                <div class="form-group form-numero">
                    {!! Form::label('alquiler_minimo', 'Noches mínimo') !!}
                    <button type="button" style="float: left;" onclick="numero('alquiler_minimo', '-', 0, 90);"
                        class="btn numeroBoton">-</button>
                    @if (!is_null($alojamiento->alquiler_minimo))
                        {!! Form::number('alquiler_minimo', $alojamiento->alquiler_minimo, [
                            'step' => '1',
                            'min' => '0',
                            'max' => '90',
                            'class' => 'form-control numeroValor',
                            'required',
                        ]) !!}
                    @else
                        {!! Form::number('alquiler_minimo', 0, [
                            'step' => '1',
                            'min' => '0',
                            'max' => '90',
                            'class' => 'form-control numeroValor',
                            'required',
                        ]) !!}
                    @endif
                    <button type="button" style="float: left;" onclick="numero('alquiler_minimo', '+', 0, 90);"
                        class="btn numeroBoton">+</button>
                    <br />
                </div>
                <br />
                <div class="form-group">
                    {!! Form::label('particularidades_fechas', 'Particularidades') !!}
                    {!! Form::textarea('particularidades_fechas', $alojamiento->particularidades_fechas, [
                        'class' => 'form-control',
                        'maxlength' => '1500',
                    ]) !!}
                    <small class="form-text text-muted">* Detalla a continuación si tu alojamiento tiene algunas
                        aclaraciones para alquilarlo en determinadas fechas (Ej: Alquiler Semanal Exclusivamente. Alquiler
                        mínimo de 2 noches en puente. Alquiler mínimo de 5 noches para fin de año)</small>
                </div>
                </br>
                <br />
                <h2>Descuentos (opcional)</h2>
                <h4>Puedes ofrecer si así lo quisieras algunos descuentos que pueden resultar atractivos para
                    inquilinos.<br />Debes detallar el % de descuento en cada caso y se aplicará al monto total final de
                    noches sin tener en cuenta los valores fijos.</h4>
                <br />
                <div class="form-group">
                    <b>{!! Form::label('descuento_semanal', 'Descuento semanal', ['class' => '']) !!}</b>
                    <span class="sufijo-por">%</span>
                    {!! Form::number('descuento_semanal', $alojamiento->descuento_semanal, [
                        'class' => 'sufijo-por-input form-control',
                        'placeholder' => '',
                        'step' => '1',
                        'min' => '1',
                        'max' => '999',
                    ]) !!}
                    <small class="form-text text-muted">* El descuento aplica para estadías de 7 a 13 noches.</small>
                </div>
                <br />
                <div class="form-group">
                    <b>{!! Form::label('descuento_quincenal', 'Descuento quincenal', ['class' => '']) !!}</b>
                    <span class="sufijo-por">%</span>
                    {!! Form::number('descuento_quincenal', $alojamiento->descuento_quincenal, [
                        'class' => 'sufijo-por-input form-control',
                        'placeholder' => '',
                        'step' => '1',
                        'min' => '1',
                        'max' => '999',
                    ]) !!}
                    <small class="form-text text-muted">* El descuento aplica para estadías de 14 a 27 noches.</small>
                </div>
                <br />
                <div class="form-group">
                    <b>{!! Form::label('descuento_mensual', 'Descuento mensual', ['class' => '']) !!}</b>
                    <span class="sufijo-por">%</span>
                    {!! Form::number('descuento_mensual', $alojamiento->descuento_mensual, [
                        'class' => 'sufijo-por-input form-control',
                        'placeholder' => '',
                        'step' => '1',
                        'min' => '1',
                        'max' => '999',
                    ]) !!}
                    <small class="form-text text-muted">* El descuento aplica para estadías de 28 noches o más.</small>
                </div>
            @endif
            @if ($paso == 9)
                {!! Form::hidden('bloqueos', json_encode($bloqueos, JSON_UNESCAPED_UNICODE)) !!}
                {!! Form::hidden('bloqueosAltas') !!}
                {!! Form::hidden('bloqueosBajas') !!}
                <h2>Calendario de reservas</h2>
                <h4>Un calendario actualizado es fundamental en el momento que te soliciten una reserva y además agiliza los
                    tiempos de respuesta a nuestros huéspedes.</h4>
                <br />
                <h2>Actualiza tu calendario</h2>
                <h4>Es muy fácil, selecciona los días que deseas bloquear y aparecerá como no disponible en el calendario,
                    si quieres liberarlo selecciona la fecha nuevamente y listo, quedará disponible. También puedes bloquear
                    y desbloquear meses completos.</h4>
                <br />
                <div class="form-group">
                    <div class="calendario_referencia">25</div>
                    <div class="calendario_referencia_texto" style="float: left;">Disponible</div>
                    <div class="calendario_referencia" style="; "><span class="tdDiaBloqueado">25</span></div>
                    <div class="calendario_referencia_texto">No disponible</div>
                </div>
                <br />
                <h4>Los colores de fondo hacen referencia al tipo de temporada y se aplicará el valor que estableciste para
                    cada caso.</h4>
                <br />
                <div class="form-group temporada">
                    {!! Form::label('sn', 'Temporada alta', ['class' => 'temporada_alta']) !!}<br />
                    {!! Form::label('sn', 'Temporada media', ['class' => 'temporada_media']) !!}<br />
                    {!! Form::label('sn', 'Temporada baja', ['class' => 'temporada_baja']) !!}
                </div>
                <table class="calendar" id="calendar">
                    <thead>
                        <tr>
                            <td colspan="7" style="border: none; padding: 2px;">
                                <p id="monthAndYear"></p>
                                <div style="float: right;">
                                    <button class="btn btn-light" id="previous" onclick="previous___()"
                                        type="button">Anterior</button>
                                    <button class="btn btn-light" id="next" onclick="next___()"
                                        type="button">Siguiente</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" style="border: none; padding: 2px;">
                                <div style="float: left;">
                                    <button style="padding-left: 0" class="btn btn-link"
                                        onclick="bloqueoCalendario(true)" type="button">Bloqueá este mes</button>
                                </div>
                                <div style="float: right;">
                                    <button style="padding-right: 0" class="btn btn-link"
                                        onclick="bloqueoCalendario(false)" type="button">Desbloqueá este mes</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Dom</th>
                            <th>Lun</th>
                            <th>Mar</th>
                            <th>Mie</th>
                            <th>Jue</th>
                            <th>Vie</th>
                            <th>Sab</th>
                        </tr>
                    </thead>
                    <tbody id="calendar-body">
                    </tbody>
                </table>
                <br /><br />
                <h2>Políticas de cancelación</h2>
                <h4>Elige la política de cancelación en caso que el huésped llegue a cancelar su reserva.<br /><br />Check
                    In: Se toma la hora de confirmación de la reserva enviada por mail (Hora Local), se contemplan días y
                    horas.</h4>
                <br />
                <div class="form-group">
                    <select name="politica_cancelacion" id="politica_cancelacion" class="form-control">
                        @if ($alojamiento->politica_cancelacion == null || $alojamiento->politica_cancelacion == 'F')
                            <option selected="selected" value="F">Flexible</option>
                            <option value="M">Moderada</option>
                            <option value="E">Estricta</option>
                            <option value="S">Muy estricta</option>
                        @else
                            @if ($alojamiento->politica_cancelacion == 'M')
                                <option value="F">Flexible</option>
                                <option selected="selected" value="M">Moderada</option>
                                <option value="E">Estricta</option>
                                <option value="S">Muy estricta</option>
                            @else
                                @if ($alojamiento->politica_cancelacion == 'E')
                                    <option value="F">Flexible</option>
                                    <option value="M">Moderada</option>
                                    <option selected="selected" value="E">Estricta</option>
                                    <option value="S">Muy estricta</option>
                                @else
                                    <option value="F">Flexible</option>
                                    <option value="M">Moderada</option>
                                    <option value="E">Estricta</option>
                                    <option selected="selected" value="S">Muy estricta</option>
                                @endif
                            @endif
                        @endif
                    </select>
                </div>
                <br />
                <h4><b>Flexible</b></h4>
                <h3>Los huéspedes recibirán un reembolso total de la reserva (menos la tarifa de servicio) , si cancelan 48
                    horas antes del Check In.</h3>
                <br />
                <h4><b>Moderada</b></h4>
                <h3>Los huéspedes recibirán un reembolso total de la reserva (menos la tarifa de servicio) , si cancelan 7
                    días antes del Check In.</h3>
                <br />
                <h4><b>Estricta</b></h4>
                <h3>Los huéspedes recibirán un reembolso del 50% del total de la reserva (menos la tarifa de servicio) , si
                    cancelan 15 días antes del Check In.</h3>
                <br />
                <h4><b>Muy estricta</b></h4>
                <h3>Si los huéspedes cancelan la reserva no se les hará ningún reembolso de dinero.</h3>
            @endif
            @if ($paso == 10)
                <h2>Tarifas administrativas</h2>
                <br />
                <h2>Datos de la cuenta</h2>
                <div class="form-group">
                    {!! Form::text('cuenta_nombre', $alojamiento->cuenta_nombre, [
                        'class' => 'form-control',
                        'placeholder' => 'Nombre o razón social',
                        'required' => 'required',
                        'maxlength' => '100',
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::text('cuenta_doc', $alojamiento->cuenta_doc, [
                        'class' => 'form-control',
                        'placeholder' => 'Número de documento o NIT',
                        'required' => 'required',
                        'maxlength' => '20',
                    ]) !!}
                </div>
                <div class="form-group">
                    {!! Form::text('cuenta_banco', $alojamiento->cuenta_banco, [
                        'class' => 'form-control',
                        'placeholder' => 'Banco',
                        'required' => 'required',
                        'maxlength' => '100',
                    ]) !!}
                </div>
                <div class="form-group">
                    <select name="cuenta_tipo" id="cuenta_tipo" class="form-control">
                        @if ($alojamiento->cuenta_tipo == null || $alojamiento->cuenta_tipo == 'CC')
                            <option selected="selected" value="CC">Cuenta corriente</option>
                            <option value="CA">Cuenta de ahorros</option>
                        @else
                            <option value="CC">Cuenta corriente</option>
                            <option selected="selected" value="CA">Cuenta de ahorros</option>
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    {!! Form::text('cuenta_nro', $alojamiento->cuenta_nro, [
                        'class' => 'form-control',
                        'placeholder' => 'Número de la cuenta',
                        'required' => 'required',
                        'maxlength' => '30',
                    ]) !!}
                </div>
                <br />
                <img class="icono" src="{{ url('/img/tarifas.svg') }}"><a class="btn boton_accion boton_eliminar"
                    data-toggle="collapse" href="#collapsePagos" role="button" aria-expanded="false"
                    aria-controls="collapsePagos">Ver procedimiento de pago</a>
                <br /><br />
                <div class="collapse" id="collapsePagos">
                    1. La reserva se hace efectiva una vez el Alojador confirma la reserva y el huésped la paga a través de
                    la plataforma.<br /><br />
                    2. El pago se te hará por medio de una transferencia bancaria a la cuenta que registraste pasadas 24
                    horas de la llegada de los huéspedes. (Este tiempo lo reservamos para que los huéspedes nos indiquen que
                    todo está de acuerdo a lo publicado en tu anuncio)<br /><br />
                    3. El depósito reembolsable lo debes solicitar una vez los huéspedes lleguen a tu propiedad.
                    (Opcional)<br /><br />
                    4. Devolución del depósito reembolsable al final de la estadía. (Revisa que toda tu propiedad esté en
                    las mismas condiciones que se las entregaste). (Opcional)<br /><br />
                </div>
                <br />
            @endif
            @if ($paso == 11)
                <?php
                
                $alojamientoFotoPrincipal = App\AlojamientoFoto::where('alojamiento_id', $alojamiento->id)
                    ->where('num_foto', 1)
                    ->first();
                
                $fotoPrincipal = false;
                
                if (!is_null($alojamientoFotoPrincipal)) {
                    $fotoPrincipal = true;
                }
                
                ?>
                <h2>Estamos listos para publicar tu alojamiento, ¿y tu?</h2><br />
                <h3>Puedes ver como quedará tu propiedad publicada haciendo click en el botón “Vista previa”. Para publicar
                    tu alojamiento, debes regresar a esta ventana.</h3>
                <br />
                <a href="{{ url('/alojamientos/' . $alojamiento->id) }}" target="_blank" class="btn boton_accion">Vista
                    previa</a>
                @if ($alojamiento->estado == 'I')
                    @if ($alojamiento->mapa_locacion == null ||
                        $alojamiento->huespedes == null ||
                        !$fotoPrincipal ||
                        $alojamiento->descripcion == null ||
                        $alojamiento->check_in == null ||
                        $alojamiento->precio_alta == null ||
                        $alojamiento->cuenta_nombre == null)
                        <button disabled class="btn boton_accion">Publicar</button>
                    @else
                        <button type="submit" value="activar" name="navegacion" style=""
                            class="btn boton_accion">Publicar</button>
                    @endif
                @else
                    <button type="submit" value="inactivar" name="navegacion" style=""
                        class="btn boton_accion boton_eliminar">Inactivar</button>
                @endif
                <br /><br />
                <h3>Recuerda que en cualquier momento puedes editar y hacer modificaciones en tu anuncio.</h3>
                <br />
                <h2>Editar información</h2>
                <div id="paso-final-botones">
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=1', 'Tipo de alojamiento y dirección', [
                        'class' => 'btn btn-link',
                    ]) !!}
                    @if ($alojamiento->mapa_locacion == null)
                        <span class="incompleto">(incompleto)</span>
                    @endif
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=2', 'Comodidades', ['class' => 'btn btn-link']) !!}
                    @if ($alojamiento->huespedes == null)
                        <span class="incompleto">(incompleto)</span>
                    @endif
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=3', 'Servicios', ['class' => 'btn btn-link']) !!}
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=4', 'Sitios de interés cercanos', [
                        'class' => 'btn btn-link',
                    ]) !!}
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=5', 'Fotos', ['class' => 'btn btn-link']) !!}
                    @if (!$fotoPrincipal)
                        <span class="incompleto">(incompleto)</span>
                    @endif
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=6', 'Descripción y título', [
                        'class' => 'btn btn-link',
                    ]) !!}
                    @if ($alojamiento->descripcion == null)
                        <span class="incompleto">(incompleto)</span>
                    @endif
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=7', 'Normas - Check in / out', [
                        'class' => 'btn btn-link',
                    ]) !!}
                    @if ($alojamiento->check_in == null)
                        <span class="incompleto">(incompleto)</span>
                    @endif
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=8', 'Valores y descuentos', [
                        'class' => 'btn btn-link',
                    ]) !!}
                    @if ($alojamiento->precio_alta == null)
                        <span class="incompleto">(incompleto)</span>
                    @endif
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=9', 'Calendario de reservas / disponibilidad', [
                        'class' => 'btn btn-link',
                    ]) !!}
                    <br />{!! link_to('alojamientos/' . $alojamiento->id . '/edit?paso=10', 'Datos bancarios', [
                        'class' => 'btn btn-link',
                    ]) !!}
                    @if ($alojamiento->cuenta_nombre == null)
                        <span class="incompleto">(incompleto)</span>
                    @endif
                </div>
                <br />
                <br />
            @endif
            <br />
            <div class="pie_botones form-row">
                <div class="col-3">
                    @if ($paso == 1)
                        <a class="btn boton_anterior disabled">
                            < Anterior</a>
                            @else
                                <button type="submit" value="ant" name="navegacion" class="btn boton_anterior">
                                    < Anterior</button>
                    @endif
                </div>
                <div class="col-3">
                    <button type="submit" value="save" name="navegacion" class="btn boton_guardar">Guardar y
                        salir</button>
                </div>
                <div class="col-3">
                    {!! link_to('alojamientos', 'Salir', ['class' => ' btn boton_volver']) !!}
                </div>
                <div class="col-3">
                    @if ($paso == 11)
                        <a class="btn boton_siguiente disabled" style="float: right;">Siguiente ></a>
                    @else
                        @if ($paso == 10)
                            <button type="submit" value="sig" name="navegacion" style="float: right;"
                                class="btn boton_siguiente">Finalizar ></button>
                        @else
                            <button type="submit" value="sig" name="navegacion" style="float: right;"
                                class="btn boton_siguiente">Siguiente ></button>
                        @endif
                    @endif
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    </div>
@endsection
@push('head')
    @if ($paso == 1)
        <script type="text/javascript"
            src="https://maps.google.com/maps/api/js?key=AIzaSyCGCMtlw34S-hV6EwIaSj0X7zL3Pkx1EwA&libraries=places&callback=initAutocomplete">
        </script>
    @endif
    @if ($paso == 8)
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
    @endif
@endpush
@push('footer')
    <script type="text/javascript">
        @if ($paso == 2)
            onload = "leyendaCamas()";
            document.addEventListener("DOMContentLoaded", () => {
                leyendaCamas()
            });
        @endif
        function numero(nombre, operacion, minimo, maximo) {
            elemento = document.getElementsByName(nombre)[0];
            if (!elemento.value) {
                nuevoValor = 0;
            } else {
                nuevoValor = parseInt(elemento.value);
            }
            if (operacion == '+') {
                nuevoValor = nuevoValor + 1;
            } else {
                nuevoValor = nuevoValor - 1;
            }
            if (nuevoValor > maximo) {
                nuevoValor = maximo;
            }
            if (nuevoValor < minimo) {
                nuevoValor = minimo;
            }
            elemento.value = nuevoValor;
            if (nombre == "cuartos") {
                mostrarOcultar("cuarto", nuevoValor, maximo);
                leyendaCamas();
            }
        }

        function mostrarOcultar(nombre, valor, maximo) {
            for (iCuarto = 1; iCuarto <= valor; iCuarto++) {
                $('#' + nombre + iCuarto).show();
            }
            for (iCuarto = valor + 1; iCuarto <= maximo; iCuarto++) {
                $('#' + nombre + iCuarto).hide();
            }
        }

        function botonCamas(numeroCuarto) {
            $('#editarCamas' + numeroCuarto).toggle();
            $('#leyendaCamas' + numeroCuarto).toggle();
            if ($('#editarCamas' + numeroCuarto).is(':visible')) {
                $('#botonCamas' + numeroCuarto).html('Listo');
            } else {
                $('#botonCamas' + numeroCuarto).html('Editar camas');
                leyendaCamas();
            }
        }

        function leyendaCamas() {
            for (iCuartos = 1; iCuartos <= 31; iCuartos++) {
                leyenda = '';
                camasKing = document.getElementsByName('camas_king[' + iCuartos + ']')[0];
                if (camasKing.value && camasKing.value > 0) {
                    leyenda += 'Camas king: ' + camasKing.value + '<br/>';
                }
                camasQueen = document.getElementsByName('camas_queen[' + iCuartos + ']')[0];
                if (camasQueen.value && camasQueen.value > 0) {
                    leyenda += 'Camas queen: ' + camasQueen.value + '<br/>';
                }
                camasDoble = document.getElementsByName('camas_doble[' + iCuartos + ']')[0];
                if (camasDoble.value && camasDoble.value > 0) {
                    leyenda += 'Camas dobles: ' + camasDoble.value + '<br/>';
                }
                camasSemiDoble = document.getElementsByName('camas_semi_doble[' + iCuartos + ']')[0];
                if (camasSemiDoble.value && camasSemiDoble.value > 0) {
                    leyenda += 'Camas semi dobles: ' + camasSemiDoble.value + '<br/>';
                }
                camasSencilla = document.getElementsByName('camas_sencilla[' + iCuartos + ']')[0];
                if (camasSencilla.value && camasSencilla.value > 0) {
                    leyenda += 'Camas sencillas: ' + camasSencilla.value + '<br/>';
                }
                camasCamarote = document.getElementsByName('camas_camarote[' + iCuartos + ']')[0];
                if (camasCamarote.value && camasCamarote.value > 0) {
                    leyenda += 'Camarotes: ' + camasCamarote.value + '<br/>';
                }
                camasAuxiliar = document.getElementsByName('camas_auxiliar[' + iCuartos + ']')[0];
                if (camasAuxiliar.value && camasAuxiliar.value > 0) {
                    leyenda += 'Camas auxiliares: ' + camasAuxiliar.value + '<br/>';
                }
                camasSofa = document.getElementsByName('camas_sofa[' + iCuartos + ']')[0];
                if (camasSofa.value && camasSofa.value > 0) {
                    leyenda += 'Sofá camas: ' + camasSofa.value + '<br/>';
                }
                camasTipoNombre1 = document.getElementsByName('camas_otro_tipo_nombre_1[' + iCuartos + ']')[0];
                camasTipo1 = document.getElementsByName('camas_otro_tipo_1[' + iCuartos + ']')[0];
                if (camasTipo1.value && camasTipo1.value > 0) {
                    leyenda += camasTipoNombre1.value + ': ' + camasTipo1.value + '<br/>';
                }
                camasTipoNombre2 = document.getElementsByName('camas_otro_tipo_nombre_2[' + iCuartos + ']')[0];
                camasTipo2 = document.getElementsByName('camas_otro_tipo_2[' + iCuartos + ']')[0];
                if (camasTipo2.value && camasTipo2.value > 0) {
                    leyenda += camasTipoNombre2.value + ': ' + camasTipo2.value + '<br/>';
                }
                camasTipoNombre3 = document.getElementsByName('camas_otro_tipo_nombre_3[' + iCuartos + ']')[0];
                camasTipo3 = document.getElementsByName('camas_otro_tipo_3[' + iCuartos + ']')[0];
                if (camasTipo3.value && camasTipo3.value > 0) {
                    leyenda += camasTipoNombre3.value + ': ' + camasTipo3.value + '<br/>';
                }
                camasTipoNombre4 = document.getElementsByName('camas_otro_tipo_nombre_4[' + iCuartos + ']')[0];
                camasTipo4 = document.getElementsByName('camas_otro_tipo_4[' + iCuartos + ']')[0];
                if (camasTipo4.value && camasTipo4.value > 0) {
                    leyenda += camasTipoNombre4.value + ': ' + camasTipo4.value + '<br/>';
                }
                camasTipoNombre5 = document.getElementsByName('camas_otro_tipo_nombre_5[' + iCuartos + ']')[0];
                camasTipo5 = document.getElementsByName('camas_otro_tipo_5[' + iCuartos + ']')[0];
                if (camasTipo5.value && camasTipo5.value > 0) {
                    leyenda += camasTipoNombre5.value + ': ' + camasTipo5.value + '<br/>';
                }
                if (leyenda == '') {
                    leyenda = 'Camas: 0';
                }
                $('#leyendaCamas' + iCuartos).html(leyenda);
            }
        }

        function agregarCama(numeroCuarto) {
            if (!$('#camas_otro_tipo_1_div' + numeroCuarto).is(":visible")) {
                $('#camas_otro_tipo_1_div' + numeroCuarto).show();
            } else {
                if (!$('#camas_otro_tipo_2_div' + numeroCuarto).is(":visible")) {
                    $('#camas_otro_tipo_2_div' + numeroCuarto).show();
                } else {
                    if (!$('#camas_otro_tipo_3_div' + numeroCuarto).is(":visible")) {
                        $('#camas_otro_tipo_3_div' + numeroCuarto).show();
                    } else {
                        if (!$('#camas_otro_tipo_4_div' + numeroCuarto).is(":visible")) {
                            $('#camas_otro_tipo_4_div' + numeroCuarto).show();
                        } else {
                            if (!$('#camas_otro_tipo_5_div' + numeroCuarto).is(":visible")) {
                                $('#camas_otro_tipo_5_div' + numeroCuarto).show();
                                $('#camas_otro_tipo_boton' + numeroCuarto).hide();
                            }
                        }
                    }
                }
            }
        }
    </script>
    <script type="text/javascript">
        function previewImage(id, vista, numero) {
            var oFReader = new FileReader();
            oFReader.readAsDataURL(document.getElementById(id).files[0]);
            oFReader.onload = function(oFREvent) {
                document.getElementById(vista).src = oFREvent.target.result;
            };
            for (var i = 1; i < 31; i++) {
                var elemento = document.getElementById('grupoFoto' + i);
                if (elemento.style.display === 'none') {
                    elemento.style.display = 'inline';
                    break;
                }
            }
        };
    </script>
    @if ($paso == 1)
        <script>
            function initialize() {
                var lat = 4.6486259;
                var lng = -74.2478918
                @if ($alojamiento->mapa_latitud != null)
                    lat = {{ $alojamiento->mapa_latitud }};
                @endif
                @if ($alojamiento->mapa_longitud != null)
                    lng = {{ $alojamiento->mapa_longitud }};
                @endif
                var latlng = new google.maps.LatLng(lat, lng);
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: latlng,
                    zoom: 13
                });
                var marker = new google.maps.Marker({
                    map: map,
                    position: latlng,
                    draggable: true,
                    anchorPoint: new google.maps.Point(0, -29)
                });
                var input = document.getElementById('searchInput');
                //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
                var geocoder = new google.maps.Geocoder();
                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);
                var infowindow = new google.maps.InfoWindow();
                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        //window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }
                    // If the place has a geometry, then present it on a map.
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);
                    bindDataToForm(place.formatted_address, place.geometry.location.lat(), place.geometry.location
                        .lng());
                    bindDataToFormDetails(place);
                    infowindow.setContent(place.formatted_address);
                    infowindow.open(map, marker);
                });
                // this function will work on marker move event into map 
                google.maps.event.addListener(marker, 'dragend', function() {
                    geocoder.geocode({
                        'latLng': marker.getPosition()
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                bindDataToForm(results[0].formatted_address, marker.getPosition().lat(), marker
                                    .getPosition().lng());
                                    bindDataToFormDetails(results[0]);
                                infowindow.setContent(results[0].formatted_address);
                                infowindow.open(map, marker);
                            }
                        }
                    });
                });
            }

            function bindDataToForm(address, lat, lng) {
                document.getElementById('searchInput').value = address;
                document.getElementById('location').value = address;
                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;
            }

            function bindDataToFormDetails(place) {
                let address1 = "";
                let postcode = "";
                let locality = "";
                let state1 = "";
                let country = "";
                let route = "";
                let barrio = "";
                let state2 = "";
                let state3 = "";
                for (const component of place.address_components) {
                    const componentType = component.types[0];
                    switch (componentType) {
                        case "street_number": {
                            address1 = `${component.long_name} ${address1}`;
                            break;
                        }
                        case "route": {
                            address1 = component.short_name + ' ' + address1;
                            break;
                        }
                        case "postal_code": {
                            postcode = `${component.long_name}${postcode}`;
                            break;
                        }
                        case "postal_code_suffix": {
                            postcode = `${postcode}-${component.long_name}`;
                            break;
                        }
                        case "locality": {
                            locality = component.long_name;
                            break;
                        }
                        case "neighborhood": {
                            barrio = component.long_name;
                            break;
                        }
                        case "administrative_area_level_1": {
                            state1 = component.long_name;
                            break;
                        }
                        case "administrative_area_level_2": {
                            state2 = component.long_name;
                            break;
                        }
                        case "administrative_area_level_3": {
                            state3 = component.long_name;
                            break;
                        }
                        case "route": {
                            route = component.long_name;
                            break;
                        }
                        case "country": {
                            country = component.long_name;
                            break;
                        }
                    }
                }
                /*
                detalle = 'Dirección: ' + address1 + '<br/>';  
                detalle += 'Código Postal: ' + postcode + '<br/>';
                detalle += 'Barrio: ' + barrio + '<br/>';   ;
                detalle += 'Localidad: ' + locality + '<br/>';   
                detalle += 'Ruta: ' + locality + '<br/>';   
                detalle += 'Estado nivel 3: ' + state3 + '<br/>';   
                detalle += 'Estado nivel 2: ' + state2 + '<br/>';     
                detalle += 'Estado nivel 1: ' + state1 + '<br/>';  
                detalle += 'País: ' + country;  
                document.getElementById('prueba').innerHTML = detalle;   
                */
                //document.getElementsByName('direccion')[0].value = address1;
                document.getElementsByName('barrio')[0].value = barrio;
                if (locality != '') {
                    document.getElementsByName('ciudad')[0].value = locality;
                } else {
                    document.getElementsByName('ciudad')[0].value = state1;
                }
                document.getElementsByName('municipio')[0].value = state2;
                document.getElementsByName('departamento')[0].value = state1;
            }
            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    @endif
    @if ($paso == 8)
        <script type="text/javascript">
            function prepararFormatoCurrency() {
                const autoNumericOptionsColombia = {
                    digitGroupSeparator: '.',
                    decimalCharacter: ',',
                    currencySymbol: '',
                    minimumValue: '1',
                    maximumValue: '999999999999',
                    decimalPlaces: 0,
                };
                new AutoNumeric('#precio_alta', autoNumericOptionsColombia);
                new AutoNumeric('#precio_media', autoNumericOptionsColombia);
                new AutoNumeric('#precio_baja', autoNumericOptionsColombia);
                new AutoNumeric('#precio_limpieza', autoNumericOptionsColombia);
                new AutoNumeric('#precio_deposito', autoNumericOptionsColombia);
            }
        </script>
    @endif
    @if ($paso == 8 || $paso == 9)
        <script type="text/javascript">
            onload = "mostrarCalendario()";
            document.addEventListener("DOMContentLoaded", () => {
                mostrarCalendario();
                @if ($paso == 8)
                    prepararFormatoCurrency();
                @endif
            });

            function mostrarCalendario() {
                arrMedia = {!! json_encode($temporadaMedia, JSON_UNESCAPED_UNICODE) !!};
                arrAlta = {!! json_encode($temporadaAlta, JSON_UNESCAPED_UNICODE) !!};
                arrBloqueos = null;
                @if ($paso == 9)
                    arrBloqueos = {!! json_encode($bloqueos, JSON_UNESCAPED_UNICODE) !!};
                    arrBloqueosAltas = [];
                    arrBloqueosBajas = [];
                @endif
                today = new Date();
                today.setHours(0, 0, 0, 0);
                currentMonth = today.getMonth();
                mesActual = today.getMonth();
                currentYear = today.getFullYear();
                anioActual = today.getFullYear();
                anioMax = {{ $anioMax }};
                mesMax = {{ $mesMax }};
                months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre",
                    "Noviembre", "Diciembre"
                ];
                monthAndYear = document.getElementById("monthAndYear");
                showCalendar(currentMonth, currentYear);
            }

            function next___() {
                if (mesMax == currentMonth && anioMax == currentYear) {
                    alert("No existe información sobre el siguiente mes.");
                } else {
                    currentYear = (currentMonth === 11) ? currentYear + 1 : currentYear;
                    currentMonth = (currentMonth + 1) % 12;
                    showCalendar(currentMonth, currentYear);
                }
            }

            function previous___() {
                if (mesActual == currentMonth && anioActual == currentYear) {
                    alert("No se pueden mostrar meses previos al actual.");
                } else {
                    currentYear = (currentMonth === 0) ? currentYear - 1 : currentYear;
                    currentMonth = (currentMonth === 0) ? 11 : currentMonth - 1;
                    showCalendar(currentMonth, currentYear);
                }
            }

            function agregarQuitarFecha(fechaP) {
                elemento = $('#' + fechaP);
                const index = arrBloqueos.indexOf(fechaP);
                const indexBajas = arrBloqueosBajas.indexOf(fechaP);
                const indexAltas = arrBloqueosAltas.indexOf(fechaP);
                // Eliminación de bloqueos
                if (elemento.hasClass('tdDiaBloqueado')) {
                    elemento.removeClass('tdDiaBloqueado');
                    elemento.addClass('tdDiaNoBloqueado');
                    // Si está en la BD y no está en Bajas, lo agrego a Bajas
                    // En cualquier caso, lo elimino de altas 
                    if (index > -1 && indexBajas == -1) {
                        arrBloqueosBajas.push(fechaP);
                    }
                    if (indexBajas > -1) {
                        arrBloqueosAltas.splice(no, 1);
                    }
                }
                // Altas de bloqueo
                else {
                    elemento.addClass('tdDiaBloqueado');
                    elemento.removeClass('tdDiaNoBloqueado');
                    // Si no está en la BD y no está en Altas, lo agrego a Altas
                    // En cualquier caso, lo elimino de bajas
                    if (index == -1 && indexAltas == -1) {
                        arrBloqueosAltas.push(fechaP);
                    }
                    if (indexBajas > -1) {
                        arrBloqueosBajas.splice(indexBajas, 1);
                    }
                }
                $('input[name=bloqueosAltas]').val(arrBloqueosAltas);
                $('input[name=bloqueosBajas]').val(arrBloqueosBajas);
                console.log('BD: ' + $('input[name=bloqueos]').val());
                console.log('Nuevos bloqueos: ' + $('input[name=bloqueosAltas]').val());
                console.log('Bloqueos a eliminar: ' + $('input[name=bloqueosBajas]').val());
            }

            function bloqueoCalendario(bloquear) {
                $("#calendar td a").each(function() {
                    if (bloquear && $(this).hasClass('tdDiaNoBloqueado')) {
                        agregarQuitarFecha($(this).attr('id'));
                    }
                    if (!bloquear && $(this).hasClass('tdDiaBloqueado')) {
                        agregarQuitarFecha($(this).attr('id'));
                    }
                });
            }

            function showCalendar(month, year) {
                let firstDay = (new Date(year, month)).getDay();
                tbl = document.getElementById("calendar-body");
                tbl.innerHTML = "";
                monthAndYear.innerHTML = months[month] + " " + year;
                let date = 1;
                for (let i = 0; i < 6; i++) {
                    let row = document.createElement("tr");
                    for (let j = 0; j < 7; j++) {
                        if (i === 0 && j < firstDay) {
                            cell = document.createElement("td");
                            cellText = document.createTextNode("");
                            cell.appendChild(cellText);
                            row.appendChild(cell);
                        } else if (date > daysInMonth(month, year)) {
                            break;
                        } else {
                            cell = document.createElement("td");
                            let fechaFormateada = year + '-' + ('0' + (month + 1)).slice(-2) + '-' + ('0' + date).slice(-2);
                            let fecha = new Date(year, month, date);
                            @if ($paso == 9)
                                if (fecha >= today) {
                                    a = document.createElement('a');
                                    a.href = 'javascript:agregarQuitarFecha("' + fechaFormateada + '")';
                                    a.innerHTML = date;
                                    a.id = fechaFormateada;
                                    cell.appendChild(a);
                                    if (arrBloqueos.indexOf(fechaFormateada) != -1) {
                                        a.classList.add("tdDiaBloqueado");
                                    } else {
                                        a.classList.add("tdDiaNoBloqueado");
                                    }
                                } else {
                                    cellText = document.createTextNode(date);
                                    cell.appendChild(cellText);
                                }
                            @else
                                cellText = document.createTextNode(date);
                                cell.appendChild(cellText);
                            @endif
                            cell.className = "tdTemporadaBaja";
                            if (fecha < today) {
                                cell.className = "tdTemporadaPasado";
                            }
                            if (arrMedia.indexOf(fechaFormateada) != -1) {
                                cell.className = "tdTemporadaMedia";
                            }
                            if (arrAlta.indexOf(fechaFormateada) != -1) {
                                cell.className = "tdTemporadaAlta";
                            }
                            //console.log( year + '-' + ('0'+month).slice(-2) + '-' + ('0'+date).slice(-2)   );
                            //console.log(arrMedia);
                            //console.log(fechaFormateada);
                            //console.log(arrMedia.indexOf(fechaFormateada) );
                            row.appendChild(cell);
                            date++;
                        }
                    }
                    tbl.appendChild(row);
                }
            }

            function daysInMonth(iMonth, iYear) {
                return 32 - new Date(iYear, iMonth, 32).getDate();
            }
        </script>
    @endif
@endpush
