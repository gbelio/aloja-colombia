@extends('layouts.app')
@section('content')
    <div class="__contenedor">
        <input type="hidden" id="mercado-pago-public-key" value="{{ config('services.mercadopago.key') }}">
        <div class="container-fluid formulario">
            <div class="form-row" style="margin: 0">
                <div class="col-xl-7" style="background-color: #f6f6f6;">
                    <div>
                        <!-- Result -->
                        <section class="shopping-cart dark" style="display: block;">
                            <div class="container container__result" style="display: block;">
                                <div class="block-heading">
                                    <h2>Estado de la operación</h2>
                                    <p>Verifique el detalle de la operación</p>
                                </div>
                                <div class="content">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="items product info product-details">
                                                <div class="row justify-content-md-center">
                                                    <div class="col-md-4 product-detail">
                                                        <div class="product-info">
                                                            <div id=""
                                                                style="display: block !important; text-align-last: center;">
                                                                <br />
                                                                @isset($payment)
                                                                    @if ($payment->id)
                                                                        <p><b>ID: </b><span
                                                                                id="">{{ $payment->id }}</span></p>
                                                                        <p><b>Estado: </b><span id="">Pendiente</span>
                                                                        </p>
                                                                        <p><b>Detalle: </b><span id="">Pendiente de
                                                                                pago</span></p>
                                                                        <p><b>¡Revisa tu correo!</b></p>
                                                                        <br />
                                                                    @endif
                                                                @endisset
                                                                @isset($payment->transaction_details->external_resource_url)
                                                                    <a href="{{ $payment->transaction_details->external_resource_url }}"
                                                                        style="margin-bottom:25px;" target="_blank"
                                                                        rel="noopener noreferrer" class="btn btn-info">Link de
                                                                        pago</a>
                                                                @endisset
                                                                @if (!$payment->id)
                                                                    <p><b>Estado: </b><span id="">No se pudo
                                                                            generar el pago</span></p>
                                                                    <p><b>Detalle: </b><span id="">Intente
                                                                            nuevamente</span></p>
                                                                    <br />
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="__boton-volver">
                                    <a id="back-url-cash" class="btn btn-primary" href="{{ url()->previous() }}"
                                        role="button">
                                        @if ($payment->id)
                                            VOLVER
                                        @else
                                            Volver a Intentar
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="seccionFormulario col-xl-5">
                    <br />
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (Session::has('notice'))
                        <div class="alert alert-success">
                            {!! Session::get('notice') !!}
                        </div>
                    @endif
                    @if (Session::has('error'))
                        <div class="alert alert-danger">
                            {!! Session::get('error') !!}
                        </div>
                    @endif
                    <input value="{{ $alojamientoPedido->id }}" id="booking-id" type="hidden">
                    @if ($alojamientoPedido->estado == 'SO' && $puedeEditar)
                        <h2>{{ $alojamientoPedido->Huesped->nombreCompleto() }} ha realizado una solicitud de reserva por tu
                            alojamiento.</h2><br />
                        <h5>Todas las solicitudes de reserva serán enviadas a tu correo electrónico, las puedes CONFIRMAR o
                            RECHAZAR desde la plataforma.
                            <br />
                            <br />
                            Tienes 24 horas para contestar si no será RECHAZADA automáticamente, entre más rápido respondas
                            más tiempo tendrán tus huéspedes para organizar su viaje.
                        </h5>
                        <br />
                        <br />
                        <button type="submit" onclick="return confirm('Se confirmará la reserva. ¿Desea continuar?')"
                            value="CO" name="navegacion" style="" class="btn boton_accion">Confirmar</button>
                        <button type="submit" onclick="return confirm('Se rechazará la reserva. ¿Desea continuar?')"
                            value="CA" name="navegacion" style=""
                            class="btn boton_accion boton_eliminar">Rechazar</button>
                        <br /><br />
                        <br /><br />
                    @endif
                    <?php
                    $estiloFoto = 'margin-top: -35px;';
                    $alojamientoFoto = \App\AlojamientoFoto::where('alojamiento_id', $alojamientoPedido->Alojamiento->id)
                        ->where('num_foto', 1)
                        ->first();
                    ?>
                    <h1 class="indice_h1">Código del Alojamiento: {{ $alojamientoPedido->Alojamiento->codigo_alojamiento }}
                    </h1>
                    <h2 class="indice_h2">{{ $alojamientoPedido->Alojamiento->titulo }}</h2>
                    <h5>Solicitud: {{ $alojamientoPedido->fecha_pedido }} hs.</h5>
                    @if ($alojamientoPedido->estado == 'CO' ||
                        $alojamientoPedido->estado == 'PP' ||
                        $alojamientoPedido->estado == 'PC')
                        <h5>Confirmación: {{ $alojamientoPedido->fecha_confirmacion }} hs.</h5>
                        <input type="hidden" id="confirmation-date" value="{{ $alojamientoPedido->fecha_confirmacion }}">
                        <input type="hidden" id="pay-status" value="{{ $alojamientoPedido->estado }}">
                    @endif
                    @if ($alojamientoPedido->estado == 'RE')
                        <h5>Rechazo de reserva: {{ $alojamientoPedido->fecha_rechazo }} hs.</h5>
                    @endif
                    @if (!is_null($alojamientoFoto))
                        <?php
                        $estiloFoto = 'margin-top: -80px;';
                        ?>
                        <div>
                            <img src="{{ URL::to('/uploads/' . $alojamientoFoto->archivo) }}" style="width: 100%" />
                        </div>
                    @endif
                    <br />
                    <div style="{{ $estiloFoto }} position: relative; z-index: 1000; padding: 15px; ">
                    </div>
                    <br />
                    <br />
                    <div>
                        <table class="indice_tabla">
                            <tr>
                                <td>
                                    <h1 class="indice_h1">Baja</h1><span
                                        class="indice_valor">${{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->Alojamiento->precio_baja) }}</span>
                                </td>
                                <td>
                                    <h1 class="indice_h1">Media</h1><span
                                        class="indice_valor">${{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->Alojamiento->precio_media) }}</span>
                                </td>
                                <td>
                                    <h1 class="indice_h1">Alta</h1><span
                                        class="indice_valor">${{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->Alojamiento->precio_alta) }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br />
                    <br />
                    <h2>Detalle de la solicitud</h2>
                    <div id="form-disponibilidad" class=" show-form">
                        <div class="show-presupuesto-linea">Fecha de llegada<span
                                class="show-presupuesto-linea-subtotal">{{ $alojamientoPedido->fecha_desde }}</span></div>
                        <input type="hidden" id="arrival-date" value="{{ $alojamientoPedido->fecha_desde }}">
                        <div class="show-presupuesto-linea">Fecha de salida<span
                                class="show-presupuesto-linea-subtotal">{{ $alojamientoPedido->fecha_hasta }}</span></div>
                        <div class="show-presupuesto-linea">Cantidad de huéspedes<span
                                class="show-presupuesto-linea-subtotal">{{ $alojamientoPedido->huespedes }}</span></div>
                        <div class="show-presupuesto-linea">Cantidad de noches<span
                                class="show-presupuesto-linea-subtotal">{{ $alojamientoPedido->cantidad_noches }}</span>
                        </div>
                        <br />
                        @if ($puedeEditar)
                            @if ($alojamientoPedido->cantidad_noches_baja > 0)
                                <div class="show-presupuesto-linea">$
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_noche_promedio_baja) }}
                                    x {{ $alojamientoPedido->cantidad_noches_baja }} noches (temporada baja)
                                    @if ($alojamientoPedido->Alojamiento->tipo_alquiler == 'HU')
                                        x {{$cantidadHuespedes}} huespedes
                                    @endif
                                    <span class="show-presupuesto-linea-subtotal">$
                                        {{ ($alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valorSubtotalBaja())) }}
                                    </span>
                                </div>
                            @endif
                            @if ($alojamientoPedido->cantidad_noches_media > 0)
                                <div class="show-presupuesto-linea">$
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_noche_promedio_media) }}
                                    x {{ $alojamientoPedido->cantidad_noches_media }} noches (temporada media)
                                    @if ($alojamientoPedido->Alojamiento->tipo_alquiler == 'HU')
                                        x {{$cantidadHuespedes}} huespedes
                                    @endif
                                    <span class="show-presupuesto-linea-subtotal">$
                                        {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valorSubtotalMedia()) }}
                                    </span>
                                </div>
                            @endif
                            @if ($alojamientoPedido->cantidad_noches_alta > 0)
                                <div class="show-presupuesto-linea">$
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_noche_promedio_alta) }}
                                    x {{ $alojamientoPedido->cantidad_noches_alta }} noches (temporada alta)
                                    @if ($alojamientoPedido->Alojamiento->tipo_alquiler == 'HU')
                                        x {{$cantidadHuespedes}} huespedes
                                    @endif
                                    <span class="show-presupuesto-linea-subtotal">$
                                        {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valorSubtotalAlta()) }}
                                    </span>
                                </div>
                            @endif
                        @else
                            <div class="show-presupuesto-linea">$
                                {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_noche_promedio) }}
                                x {{ $alojamientoPedido->cantidad_noches }} noches
                                <span class="show-presupuesto-linea-subtotal">$
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_subtotal) }}
                                </span>
                            </div>
                        @endif
                        @if ($alojamientoPedido->valor_descuento > 0)
                            <div class="show-presupuesto-linea">{{ $descuentoDescripcion }}<span
                                    class="show-presupuesto-linea-subtotal show-presupuesto-linea-descuento">- $
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_descuento) }}</span>
                            </div>
                        @endif
                        <br />
                        <div class="show-presupuesto-linea">Tarifa de limpieza<span
                                class="show-presupuesto-linea-subtotal">$
                                {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_limpieza) }}</span>
                        </div>
                        @if ($puedeEditar)
                            <div class="show-presupuesto-linea">Comisión por servicio<span
                                    class="show-presupuesto-linea-subtotal">- $
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_comision_servicio) }}</span>
                            </div>
                            <br />
                            <div class="show-presupuesto-linea">Total que recibirás<span
                                    class="show-presupuesto-linea-subtotal">$
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_propietario) }}</span>
                            </div>
                        @else
                            <div class="show-presupuesto-linea">Tarifa por servicio<span
                                    class="show-presupuesto-linea-subtotal">$
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_servicio) }}</span>
                            </div>
                            <br />
                            <div class="show-presupuesto-linea">Total<span class="show-presupuesto-linea-subtotal">$
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_total) }}</span>
                            </div>
                            <input type="hidden" id="sum-total"
                                value="{{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_total) }}">
                            <input type="hidden" id="sum-total-mitad"
                                value="{{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_total_mitad) }}">
                        @endif
                        @if ($alojamientoPedido->valor_deposito != 0)
                            <br />
                            <div class="show-presupuesto-linea show-presupuesto-linea-deposito">Deposito Reembolsable<span
                                    class="show-presupuesto-linea-subtotal">$
                                    {{ $alojamientoPedido->Alojamiento->precioFormateado($alojamientoPedido->valor_deposito) }}</span>
                            </div>
                            @if ($puedeEditar)
                                <div class="show-presupuesto-senia">Depósito Reembolsable: <span
                                        class="show-presupuesto-senia-texto">Al elegir esta opción debes tener en cuenta
                                        que una persona deberá estar presente tanto a la llegada como a la salida de los
                                        huéspedes, realizará el inventario del Alojamiento, y al finalizar la estadía,
                                        revisará que no se presenten daños o faltantes en la propiedad para que este
                                        depósito sea reembolsado.</span></div>
                            @else
                                <div class="show-presupuesto-senia">Depósito Reembolsable: <span
                                        class="show-presupuesto-senia-texto">Este Alojamiento te solicitará un Depósito
                                        Reembolsable el cual deberas entregar en efectivo al llegar a tu Alojamiento y te
                                        será devuelto en su totalidad al finalizar tu estadía si no se presentan daños o
                                        faltantes dentro de la propiedad.</span></div>
                            @endif
                        @endif
                    </div>
                    <div class="show-post-form">
                        <div class="show-presupuesto-check"><b>Check In</b>: desde las
                            {{ date('H:i', strtotime($alojamientoPedido->Alojamiento->check_in)) }}hs.</div>
                        <div class="show-presupuesto-check"><b>Check Out</b>: desde las
                            {{ date('H:i', strtotime($alojamientoPedido->Alojamiento->check_out)) }}hs.</div>
                    </div>
                    @if ($puedeEditar)
                        <div class="show-post-form">
                            <div class="show-politicas-titulo">Procedimiento de Pago</div>
                            <div class="show-politicas-detalle">
                                1. La reserva se hace efectiva una vez el Alojador confirma la reserva y el huésped la paga
                                a través de la plataforma.<br /><br />
                                2. El pago se te hará por medio de una transferencia bancaria a la cuenta que registraste
                                pasadas 24 horas de la llegada de los huéspedes. (Este tiempo lo reservamos para que los
                                huéspedes nos indiquen que todo está de acuerdo a lo publicado en tu anuncio).<br /><br />
                                3. Si colocas la opción de Depósito Reembolsable debes tener en cuenta que debe haber una
                                persona que reciba a los huéspedes a su llegada, realice un inventario y reciba el depósito.
                                Al finalizar su estadía deberá revisar que no se presenten daños o faltantes en la propiedad
                                para que este sea reembolsado.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
