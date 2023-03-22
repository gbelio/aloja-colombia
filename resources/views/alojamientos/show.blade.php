@extends('layouts.app')
@section('content')
    <div class="container-fluid show">
        <div class="row">
            <div id="slider" class="col-md-12">
                <div class="owl-carousel owl-theme">
                    @foreach ($alojamientoFotos as $alojamientoFoto)
                        <?php
                        $sourceImagen = URL::to('/uploads/' . $alojamientoFoto->archivo);
                        ?>
                        <div>
                            <a class="chocolat-image" href="{{ $sourceImagen }}" title="Foto {{ $alojamientoFoto->num_foto }}">
                                <img onerror="this.style.display='none'" class="imagen-slider" src="{{ $alojamientoFoto->srcImagen(400) }}" />
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <br /><br />
        <div class="form-row">
            <div class="col-xl-1">
            </div>
            <div class="col-xl-5">
                <div class="form-row show-detalle">
                    <div class="col-md-9">
                        <div class="show-ubicacion">{{ $alojamiento->tipoFormateado() }} - {{ $alojamiento->ciudad }}</div>
                        <div class="show-titulo">{{ $alojamiento->titulo }}</div>
                        <div class="show-huespedes">{{ $alojamiento->leyendaHuespedesCuartos() }}</div>
                        <div class="show-descripcion">{{ $alojamiento->descripcion }}</div>
                        @if ($alojamiento->particularidades_fechas != null)
                            <br />
                            <div class="show-ubicacion">Particularidades</div>
                            <div class="show-descripcion">{{ $alojamiento->particularidades_fechas }}</div>
                        @endif
                        <br />
                    </div>
                    <div class="col-md-3 text-center">
                        <?php
                        $propietario = \App\User::find($alojamiento->propietario_id);
                        $fotoPropietario = $propietario->sourceFoto();
                        ?>
                        <div class="show-ubicacion">Alojador</div>
                        <div class="show-nombre-imagen">
                            @if ($fotoPropietario == '')
                                <img class="" src="{{ asset('img/carita-naranja.svg') }}" />
                            @else
                                <img class="" src="{{ $fotoPropietario }}" />
                            @endif
                        </div>
                        <div class="show-nombre">{{ $alojamiento->Propietario->nombreCompleto() }}</div>
                        <div class="show-alojador-fecha">Se registró en<br />{{ $propietario->fechaRegistroFormateada() }}
                        </div>
                        <div class="show-alojador-descripcion">{{ $propietario->descripcion }}</div>
                    </div>
                </div>
                <div class="form-row show-detalle">
                    <div class="col-md-12">
                        <div class="show-titulo">Habitaciones</div>
                        <div class="show-cuartos row">{!! $alojamiento->cuartosFormateados() !!}</div>
                    </div>
                </div>
                <div class="form-row show-detalle">
                    <div class="col-md-12">
                        <div class="show-titulo">Baños</div>
                        <div class="show-cuartos row">{!! $alojamiento->baniosFormateados() !!}</div>
                    </div>
                </div>
                <div class="form-row show-detalle">
                    <div class="col-md-12">
                        <div class="show-titulo">Servicios</div>
                        <div class="show-servicios">{!! $alojamiento->serviciosFormateados() !!}</div>
                    </div>
                </div>
                <div class="form-row show-detalle">
                    <div class="col-md-12">
                        <div class="show-titulo">Sitios de interés cercano</div>
                        <div class="show-cuartos row">{!! $alojamiento->sitiosFormateados() !!}</div>
                    </div>
                </div>
                <div class="form-row show-detalle">
                    <div class="col-md-12">
                        <div class="show-titulo">Normas de la casa</div>
                        <div class="show-cuartos row">{!! $alojamiento->normasFormateadas() !!}</div>
                    </div>
                </div>
                <div class="form-row show-detalle-blanco">
                    <div class="col-md-12">
                        <div class="show-titulo">Ubicación | Barrio</div>
                        <div class="map" id="map" style="width: 100%; height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-1">
            </div>
            <div class="col-xl-4" id="f">
                <div class="show-fijo">
                    <div id="form-disponibilidad" class=" show-form">
                        {!! Form::open([
                            'url' => 'alojamientos/' . $alojamiento->id . '#f',
                            'method' => 'GET',
                            'class' => '',
                            'onsubmit' => 'return validarDatosDisponibilidad();',
                        ]) !!}
                        <?php $fecha_actual = \Carbon\Carbon::now()->format('Y-m-d'); ?>
                        @if ($errorCod != 'RESERVADO')
                            @if($alojamiento->tipo_alquiler == 'HU')
                            <div class="show-precio-titulo">Precio por huesped por noche</div>
                            <div class="show-precio-titulo" style="margin-top: 0;">mínimo {{ $alojamiento->huespedes_min }} huespedes</div>
                            @else
                            <div class="show-precio-titulo">{{ $precioTitulo }}</div>
                            @endif
                            <div class="show-precio">$ {{ $alojamiento->precioFormateado($precioValor) }}</div>
                            <div class="row">
                                {!! Form::hidden('l', app('request')->input('l')) !!}
                                <div class="col-md-6">
                                    {!! Form::label('fd', 'Llegada', ['class' => 'show-form-label']) !!}
                                    {!! Form::text('fd', app('request')->input('fd'), [
                                        'style' => 'background: white',
                                        'class' => 'form-control',
                                        'required',
                                        'readonly',
                                        'onclick' => '$(".calendarInquilinoContenedor").toggle()',
                                    ]) !!}
                                    <div class="calendarInquilinoContenedor" id="calenD" style="display: none;">
                                        <table class="calendar calendarInquilino" id="calendarD">
                                            <thead>
                                                <tr>
                                                    <td colspan="7" style="border: none; padding: 2px;">
                                                        <p id="monthAndYearD"></p>
                                                        <div style="float: right;">
                                                            <button class="btn btn-light" id="previous"
                                                                onclick="previousD('calendar-bodyD')"
                                                                type="button">Anterior</button>
                                                            <button class="btn btn-light" id="next"
                                                                onclick="nextD('calendar-bodyD')"
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
                                            <tbody id="calendar-bodyD">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {!! Form::label('fh', 'Salida', ['class' => 'show-form-label']) !!}
                                    {!! Form::text('fh', app('request')->input('fh'), [
                                        'style' => 'background: white',
                                        'class' => 'form-control',
                                        'required',
                                        'readonly',
                                        'onclick' => '$(".calendarInquilinoContenedor").toggle()',
                                    ]) !!}
                                    <div class="calendarInquilinoContenedor" id="calenH" style="display: none;">
                                        <table class="calendar calendarInquilino" id="calendarH">
                                            <thead>
                                                <tr>
                                                    <td colspan="7" style="border: none; padding: 2px;">
                                                        <p id="monthAndYearH"></p>
                                                        <div style="float: right;">
                                                            <button class="btn btn-light" id="previous"
                                                                onclick="previousH('calendar-bodyH')"
                                                                type="button">Anterior</button>
                                                            <button class="btn btn-light" id="next"
                                                                onclick="nextH('calendar-bodyH')"
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
                                            <tbody id="calendar-bodyH">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {!! Form::label('h', 'Huéspedes', ['class' => 'show-form-label']) !!}
                                    {!! Form::number('h', app('request')->input('h'), [
                                        'step' => '1',
                                        'min' => $alojamiento->huespedes_min,
                                        'max' => $alojamiento->huespedes,
                                        'class' => 'form-control',
                                        'value' => $alojamiento->huespedes_min,
                                        'required',
                                        'onchange' => '$("#botonReservar").attr("disabled", true);',
                                    ]) !!}
                                </div>
                                <div class="col-md-6">
                                    <br />
                                    <button class="btn show-boton consultar-disponibilidad-boton"
                                        type="submit">Cotizar</button>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        @else
                            <div class="show-reserva-titulo">Solicitud de reserva enviada</div>
                            <div class="show-reserva-id">Código de reserva: {{ $codigo_reserva }}</div>
                        @endif
                        <div class="">
                            @if ($disponible)
                                @if ($errorCod == 'RESERVADO')
                                    <div class="show-reserva-mensaje">
                                        Tu reserva está pendiente de aprobación por parte de el alojador. En el transcurso
                                        de las siguientes 24 horas te dará respuesta, de lo contrario tu reserva quedará sin
                                        efecto. Se te notificará a tu correo electrónico.
                                    </div>
                                @else
                                    <br />
                                    <br />
                                    <div class="show-presupuesto-linea">
                                        $ {{ $alojamiento->precioFormateado($precioValor) }}
                                        x {{ $diasTotales }}
                                        noches
                                        @if($alojamiento->tipo_alquiler == 'HU')
                                        x {{$cantidadHuespedes}} huespedes
                                        @endif
                                        <span class="show-presupuesto-linea-subtotal">
                                            $ {{ $alojamiento->precioFormateado($precioTotal) }}
                                        </span>
                                    </div>
                                    @if ($descuento != 0)
                                        <div class="show-presupuesto-linea">{{ $descuentoDescripcion }}<span
                                                class="show-presupuesto-linea-subtotal show-presupuesto-linea-descuento">-
                                                $ {{ $alojamiento->precioFormateado($descuento) }}</span></div>
                                    @endif
                                    <div class="show-presupuesto-linea">Tarifa de limpieza<span
                                            class="show-presupuesto-linea-subtotal">$
                                            {{ $alojamiento->precioFormateado($precioLimpieza) }}</span></div>
                                    <div class="show-presupuesto-linea">Tarifa por servicio<span
                                            class="show-presupuesto-linea-subtotal">$
                                            {{ $alojamiento->precioFormateado($tarifaServicio) }}</span></div>
                                    <div class="show-presupuesto-linea show-presupuesto-linea-total">Total<span
                                            class="show-presupuesto-linea-subtotal">$
                                            {{ $alojamiento->precioFormateado($totalGeneral) }}</span></div>
                                    @if ($deposito != 0)
                                        <br />
                                        <div class="show-presupuesto-linea show-presupuesto-linea-deposito">Deposito
                                            Reembolsable<span class="show-presupuesto-linea-subtotal">$
                                                {{ $alojamiento->precioFormateado($deposito) }}</span></div>
                                        <div class="show-presupuesto-senia">Este Alojamiento te solicitará un Depósito
                                            Reembolsable el cual deberas entregar en efectivo al llegar a tu Alojamiento y
                                            te será devuelto en su totalidad al finalizar tu estadía si no se presentan
                                            daños o faltantes dentro de la propiedad.</div>
                                    @endif
                                    {!! Form::open([
                                        'onsubmit' => "return confirm('Se efectuará la reserva. ¿Desea continuar?')",
                                        'url' => 'alojamientos/' . $alojamiento->id . '/reservar#f',
                                        'method' => 'POST',
                                    ]) !!}
                                    {!! Form::hidden('fd', app('request')->input('fd')) !!}
                                    {!! Form::hidden('fh', app('request')->input('fh')) !!}
                                    {!! Form::hidden('h', app('request')->input('h')) !!}
                                    {!! Form::button('Reservar', [
                                        'type' => 'submit',
                                        'id' => 'botonReservar',
                                        'class' => ' btn show-boton',
                                        'style' => 'float: left; margin-right: 5px;',
                                    ]) !!}
                                    {!! Form::close() !!}
                                    <br />
                                @endif
                            @else
                                <br />
                                @if (app('request')->input('opcion') == null && app('request')->input('h') != null)
                                    @if ($errorCod == 'MINIMO')
                                        <div class="alert alert-danger">
                                            Esta propiedad requiere reservar {{ $alojamiento->alquiler_minimo }} días como
                                            mínimo en temporada alta.
                                        </div>
                                    @else
                                        <div class="alert alert-danger">
                                            Lamentablemente no tenemos disponibilidad en estas fechas para esa cantidad de
                                            huéspedes.
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-dark">
                                        Elija un rango de fechas y cantidad de huéspedes para consultar disponibilidad.
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="show-post-form">
                        <div class="show-presupuesto-check"><b>Check In</b>: desde las
                            {{ date('H:i', strtotime($alojamiento->check_in)) }}hs.</div>
                        <div class="show-presupuesto-check"><b>Check Out</b>: desde las
                            {{ date('H:i', strtotime($alojamiento->check_out)) }}hs.</div>
                    </div>
                    <div class="show-post-form">
                        <div class="show-politicas-titulo">Políticas de cancelación</div>
                        <div class="show-politicas-detalle">
                            @if ($alojamiento->politica_cancelacion == null || $alojamiento->politica_cancelacion == 'F')
                                Los huéspedes recibirán un reembolso total de la reserva (menos la tarifa de servicio) , si
                                cancelan 48 horas antes del Check In.
                            @else
                                @if ($alojamiento->politica_cancelacion == 'M')
                                    Los huéspedes recibirán un reembolso total de la reserva (menos la tarifa de servicio) ,
                                    si cancelan 7 días antes del Check In.
                                @else
                                    @if ($alojamiento->politica_cancelacion == 'E')
                                        Los huéspedes recibirán un reembolso del 50% del total de la reserva (menos la
                                        tarifa de servicio) , si cancelan 15 días antes del Check In.
                                    @else
                                        Si los huéspedes cancelan la reserva no se les hará ningún reembolso de dinero.
                                    @endif
                                @endif
                            @endif
                        </div>
                        <div class="show-politicas-notas">*Check in: Se contemplan horas y días completas antes de la hora
                            local de llegada del anuncio (indicada en el correo electrónico de confirmación.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('head')
    <script type="text/javascript"
        src="https://maps.google.com/maps/api/js?key=AIzaSyCGCMtlw34S-hV6EwIaSj0X7zL3Pkx1EwA&libraries=places&callback=initAutocomplete">
    </script>
    <script type="text/javascript">
        function validarDatosDisponibilidad() {
            fd = $("#form-disponibilidad input[name=fd]");
            fh = $("#form-disponibilidad input[name=fh]");
            if (fh.val() <= fd.val()) {
                alert("Fecha desde debe ser menor a fecha hasta.");
                fd.focus();
                return false;
            }
            return true;
        }

        function setearFechaHastaDefaultDisponibilidad() {
            fd = $("#form-disponibilidad input[name=fd]");
            fh = $("#form-disponibilidad input[name=fh]");
            if (fh.val() == "" && fd.val() != "") {
                fh.val(fd.val());
            }
        }
        $(document).ready(function() {
            $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 2,
                nav: true,
                autoplay: true,
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                        autoHeight: true
                    },
                    1000: {
                        center: true,
                        autoWidth: true,
                        items: 3
                    },
                    1500: {
                        center: true,
                        autoWidth: true,
                        items: 5
                    }
                }
            })
            Chocolat(document.querySelectorAll('.owl-carousel .chocolat-image'))
        });
        google.maps.event.addDomListener(window, 'load', initialize);

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
            marker.setVisible(false);
            var circle = new google.maps.Circle({
                map: map,
                radius: 800,
                fillColor: '#EF251B',
                strokeColor: "#EF251B",
                strokeOpacity: 1,
                strokeWeight: 1
            });
            circle.bindTo('center', marker, 'position');
        }
        @if ($errorCod != 'RESERVADO')
            onload = "mostrarCalendario()";
            document.addEventListener("DOMContentLoaded", () => {
                mostrarCalendarios();
            });

            function mostrarCalendarios() {
                arrBloqueos = null;
                arrBloqueos = {!! json_encode($bloqueos, JSON_UNESCAPED_UNICODE) !!};
                today = new Date();
                today.setHours(0, 0, 0, 0);
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const d = urlParams.get('fd');
                const h = urlParams.get('fh');
                if (d != null) {
                    var dFechaS = Date.parse(d + 'T00:00:00');
                    dFecha = new Date(dFechaS);
                    dFecha.setHours(0, 0, 0, 0);
                } else {
                    dFecha = today;
                }
                if (h != null) {
                    var hFechaS = Date.parse(h + 'T00:00:00');
                    hFecha = new Date(hFechaS);
                    hFecha.setHours(0, 0, 0, 0);
                } else {
                    hFecha = today;
                }
                mesActualD = dFecha.getMonth();
                anioActualD = dFecha.getFullYear();
                mesActualH = hFecha.getMonth();
                anioActualH = hFecha.getFullYear();
                mesActual = today.getMonth();
                anioActual = today.getFullYear();
                anioMax = {{ $anioMax }};
                mesMax = {{ $mesMax }};
                months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre",
                    "Noviembre", "Diciembre"
                ];
                monthAndYearD = document.getElementById("monthAndYearD");
                monthAndYearH = document.getElementById("monthAndYearH");
                showCalendar(dFecha, mesActualD, anioActualD, "calendar-bodyD");
                showCalendar(hFecha, mesActualH, anioActualH, "calendar-bodyH");
            }

            function nextD(calendario) {
                if (mesMax == mesActualD && anioMax == anioActualD) {
                    alert("No existe información sobre el siguiente mes.");
                } else {
                    anioActualD = (mesActualD === 11) ? anioActualD + 1 : anioActualD;
                    mesActualD = (mesActualD + 1) % 12;
                    showCalendar(dFecha, mesActualD, anioActualD, calendario);
                }
            }

            function previousD(calendario) {
                if (mesActual == mesActualD && anioActual == anioActualD) {
                    alert("No se pueden mostrar meses previos al actual.");
                } else {
                    anioActualD = (mesActualD === 0) ? anioActualD - 1 : anioActualD;
                    mesActualD = (mesActualD === 0) ? 11 : mesActualD - 1;
                    showCalendar(dFecha, mesActualD, anioActualD, calendario);
                }
            }

            function nextH(calendario) {
                if (mesMax == mesActualH && anioMax == anioActualH) {
                    alert("No existe información sobre el siguiente mes.");
                } else {
                    anioActualH = (mesActualH === 11) ? anioActualH + 1 : anioActualH;
                    mesActualH = (mesActualH + 1) % 12;
                    showCalendar(hFecha, mesActualH, anioActualH, calendario);
                }
            }

            function previousH(calendario) {
                if (mesActual == mesActualH && anioActual == anioActualH) {
                    alert("No se pueden mostrar meses previos al actual.");
                } else {
                    anioActualH = (mesActualH === 0) ? anioActualH - 1 : anioActualH;
                    mesActualH = (mesActualH === 0) ? 11 : mesActualH - 1;
                    showCalendar(hFecha, mesActualH, anioActualH, calendario);
                }
            }

            function seleccionarFecha(calendario, fechaF) {
                var fechaS = Date.parse(fechaF + 'T00:00:00');
                fecha = new Date(fechaS);
                if (calendario == "calendar-bodyD") {
                    elemento = $('input[name=fd]');
                    elementoCalendario = $('#calendarD #' + fechaF);
                    dFecha = fecha;
                } else {
                    elemento = $('input[name=fh]');
                    elementoCalendario = $('#calendarH #' + fechaF);
                    hFecha = fecha;
                }
                elemento.val(fechaF);
                $('#' + calendario + ' .tdTemporadaDiaElegido').attr('class', 'tdTemporadaBlanca');
                elementoCalendario.parent().attr('class', 'tdTemporadaDiaElegido');
                $("#botonReservar").attr("disabled", true);
                if (calendario == "calendar-bodyD" && ((anioActualD == anioActualH && mesActualH < mesActualD) || (
                        anioActualD > anioActualH))) {
                    mesActualH = dFecha.getMonth()
                    anioActualH = dFecha.getFullYear()
                    showCalendar(hFecha, mesActualH, anioActualH, "calendar-bodyH");
                }
            }

            function showCalendar(seleccionada, month, year, calendario) {
                let firstDay = (new Date(year, month)).getDay();
                tbl = document.getElementById(calendario);
                tbl.innerHTML = "";
                if (calendario == "calendar-bodyD") {
                    monthAndYearD.innerHTML = months[month] + " " + year;
                } else {
                    monthAndYearH.innerHTML = months[month] + " " + year;
                }
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
                            if (fecha >= today) {
                                a = document.createElement('a');
                                a.innerHTML = date;
                                a.id = fechaFormateada;
                                if (arrBloqueos.indexOf(fechaFormateada) != -1) {
                                    a.href = 'javascript:event.preventDefault();';
                                    a.classList.add("tdDiaBloqueado");
                                } else {
                                    a.href = 'javascript:seleccionarFecha("' + calendario + '","' + fechaFormateada + '")';
                                    a.classList.add("tdDiaNoBloqueado");
                                }
                                cell.appendChild(a);
                            } else {
                                cellText = document.createTextNode(date);
                                cell.appendChild(cellText);
                            }
                            cell.className = "tdTemporadaBlanca";
                            if (fecha < today) {
                                cell.className = "tdTemporadaPasado";
                            }
                            if (fecha.getTime() == seleccionada.getTime()) {
                                cell.className = "tdTemporadaDiaElegido";
                            }
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
        @endif
    </script>
@endpush
