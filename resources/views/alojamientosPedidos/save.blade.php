@extends('layouts.app')
@section('content')
    <?php
    $paso = app('request')->input('paso');
    ?>
    <div class="__contenedor">
        <div class="container-fluid formulario formulario_index">
            <div class="form-row" style="margin: 0; justify-content: flex-end;">
        {{-- <div class="container-fluid formulario">
            <div class="form-row" style="margin: 0"> --}}
                @if ($alojamientoPedido->alojamiento->propietario->id != Auth::user()->id && !Auth::user()->admin)
                    @if ($alojamientoPedido->estado == 'CO' || $alojamientoPedido->estado == 'PP')
                        <div class="col-xl-7" style="background-color: #f6f6f6; padding: 0;">
                            <div>
                                <!-- Hidden input to store your integration public key -->
                                <input type="hidden" id="mercado-pago-public-key"
                                    value="{{ config('services.mercadopago.key') }}">
                                <!-- Card Payment -->
                                <section class="payment-form dark">
                                    <div class="container__payment">
                                        <div class="block-heading">
                                            <h2>Pago con Tarjeta</h2>
                                        </div>
                                        <div class="form-payment">
                                            <div>
                                                <button class="btn btn-primary"
                                                    style="width: 69%; background-color: #fd3b34 !important; border: 0 !important;">
                                                    Tarjeta
                                                </button>
                                                <button class="btn btn-outline-secondary" id="go-cash-payment"
                                                    style="width: 29%;">
                                                    Efectivo
                                                </button>
                                            </div>
                                            <div class="payment-details">
                                                <form id="form-checkout">
                                                    @csrf
                                                    <h3 class="title">Datos Cliente</h3>
                                                    <div class="row">
                                                        <div class="form-group col" style="margin-bottom: 0">
                                                            <label for="">Email</label>
                                                            <input id="form-checkout__cardholderEmail"
                                                                value="test_user_48501408@testuser.com"
                                                                name="cardholderEmail" type="email"
                                                                class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-5">
                                                            <label for="">Tipo identificación</label>
                                                            <select id="form-checkout__identificationType"
                                                                name="identificationType" class="form-control"></select>
                                                        </div>
                                                        <div class="form-group col-sm-7">
                                                            <label for="">Número identificación</label>
                                                            <input id="form-checkout__identificationNumber" value="11111111"
                                                                name="docNumber" type="text" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <h3 class="title">Datos Tarjeta</h3>
                                                    <div class="row">
                                                        <div class="form-group col-sm-8">
                                                            <label for="">Nombre completo</label>
                                                            <input required id="form-checkout__cardholderName"
                                                                name="cardholderName" value="APRO" type="text"
                                                                class="form-control" />
                                                        </div>
                                                        <div class="form-group col-sm-4">
                                                            <label for="">Fecha Vencimiento</label>
                                                            <div class="input-group expiration-date">
                                                                <input required id="form-checkout__cardExpirationMonth"
                                                                    value="11" name="cardExpirationMonth" type="text"
                                                                    class="form-control" />
                                                                <span class="date-separator">/</span>
                                                                <input required id="form-checkout__cardExpirationYear"
                                                                    value="25" name="cardExpirationYear" type="text"
                                                                    class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-sm-8">
                                                            <label for="">Número de tarjeta</label>
                                                            <input required id="form-checkout__cardNumber" name="cardNumber"
                                                                value="5254 1336 7440 3564" type="text"
                                                                class="form-control" />
                                                        </div>
                                                        <div class="form-group col-sm-4">
                                                            <label for="">Código de seg.</label>
                                                            <input required id="form-checkout__securityCode" value="123"
                                                                name="securityCode" type="text" class="form-control" />
                                                        </div>
                                                        <div id="issuerInput" class="form-group col-sm-12 hidden">
                                                            <select id="form-checkout__issuer" name="issuer"
                                                                class="form-control"></select>
                                                        </div>
                                                        <div id="split-pagos" class="form-group col-sm-12"
                                                            style="display: none;">
                                                            <br>
                                                            <h3 class="title">División del pago</h3>
                                                            <label for="split-1" class="__label-split">1 pago</label><input
                                                                id="split-1" type="radio" value="1" name="split"
                                                                checked>
                                                            <label for="split-2" class="__label-split">2
                                                                pagos</label><input id="split-2" type="radio"
                                                                value="2" name="split">
                                                            <h3 style="font-weight: bold;" class="" id="1-pago">
                                                            </h3>
                                                            <h3 style="font-weight: bold;" class="" id="2-pago"
                                                                style="display: none"></h3>
                                                            <hr>
                                                            <label for="" hidden>Cuotas</label>
                                                            <select id="form-checkout__installments" name="installments"
                                                                type="text" class="form-control" hidden></select>
                                                        </div>
                                                        <div class="form-group col-sm-12">
                                                            <input type="hidden" id="amount" />
                                                            <input type="hidden" id="description" />
                                                            <br>
                                                            <button id="form-checkout__submit" type="submit"
                                                                class="btn btn-danger btn-block"
                                                                style="border-color: #fd3b34;">Pagar</button>
                                                            <br>
                                                            <p id="loading-message">Cargando, espere por favor...</p>
                                                            <br>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <!-- Cash Payment -->
                                <section class="payment-form-cash dark">
                                    <div class="container__payment-cash">
                                        <div class="block-heading">
                                            <h2>Pago en Efectivo</h2>
                                        </div>
                                        <div class="form-payment">
                                            <div>
                                                <button class="btn btn-outline-secondary" id="go-card-payment"
                                                    style="width: 29%;">
                                                    Tarjeta
                                                </button>
                                                <button class="btn btn-primary"
                                                    style="width: 69%; background-color: #fd3b34 !important;  border: 0 !important;">
                                                    Efectivo
                                                </button>
                                            </div>
                                            <div class="payment-details">
                                                <form action="/cash_process_payment" method="post" id="paymentForm">
                                                    @csrf
                                                    <input type="hidden" name="alojamientoPedidoId"
                                                        value="{{ $alojamientoPedido->id }}">
                                                    <h3 class="title">Medios de pago</h3>
                                                    <div>
                                                        <select class="form-control" id="paymentMethod"
                                                            name="paymentMethod">
                                                            <option value="efecty">Efecty</option>
                                                        </select>
                                                    </div>
                                                    <br>
                                                    <h3 class="title">Datos del comprador</h3>
                                                    <div class="row">
                                                        <div class="form-group col-sm-6">
                                                            <label for="">Nombre</label>
                                                            <input id="payerFirstName" placeholder="Nombre"
                                                                name="payerFirstName" type="text" value="NombreFalso"
                                                                class="form-control">
                                                        </div>
                                                        <div class="form-group col-sm-6">
                                                            <label for="">Apellido</label>
                                                            <input id="payerLastName" placeholder="Apellido"
                                                                name="payerLastName" type="text" value="ApellidoFalso"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col">
                                                            <label for="">Email</label>
                                                            <input id="emailCash" value="test_user_48501408@testuser.com"
                                                                pattern="[a-zA-Z0-9!#$%&'*_+-]([\.]?[a-zA-Z0-9!#$%&'*_+-])+@[a-zA-Z0-9]([^@&%$\/()=?¿!.,:;]|\d)+[a-zA-Z0-9][\.][a-zA-Z]{2,4}([\.][a-zA-Z]{2})?"
                                                                required placeholder="E-mail" name="payerEmail"
                                                                type="email" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-5">
                                                            <label for="">Tipo de identificación</label>
                                                            <select class="form-control" id="docType" name="docType"
                                                                data-checkout="docType" type="text">
                                                                <option value="CC">C.C.</option>
                                                                <option value="CE">C.E.</option>
                                                                <option value="NIT">NIT</option>
                                                                <option value="Otro">Otro</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-sm-7">
                                                            <label for="">Número identificación</label>
                                                            <input id="docNumber" name="docNumber" required
                                                                placeholder="Número de identificación"
                                                                data-checkout="docNumber" value="11111111" type="text"
                                                                class="form-control" />
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div id="split-pagos-cash" class="form-group col-sm-12"
                                                        style="display: none;">
                                                        <br>
                                                        <h3 class="title">División del pago</h3>
                                                        <label for="split-cash-1" class="__label-split">1
                                                            pago</label><input id="split-cash-1" type="radio"
                                                            value="1" name="split-cash" checked>
                                                        <label for="split-cash-2" class="__label-split">2
                                                            pagos</label><input id="split-cash-2" type="radio"
                                                            value="2" name="split-cash">
                                                        <h3 style="font-weight: bold;" class="" id="1-pago-cash">
                                                        </h3>
                                                        <h3 style="font-weight: bold;" class="" id="2-pago-cash"
                                                            style="display: none"></h3>
                                                        <hr>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-sm-12">
                                                            <input type="hidden" id="amount-cash"
                                                                name="transactionAmount" type="number" />
                                                            <input type="hidden" name="description"
                                                                id="productDescription" />
                                                            <br>
                                                            <button id="buy" type="submit"
                                                                class="btn btn-danger btn-block"
                                                                style="border-color: #fd3b34;">Pagar</button>
                                                            <br>
                                                            <p id="loading-message">Cargando, espere por favor...</p>
                                                            <br>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <!-- Result -->
                                <section class="shopping-cart dark">
                                    <div class="container container__result">
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
                                                                    <div id="fail-response">
                                                                        <br />
                                                                        <img src="img/fail.png" width="350px">
                                                                        <p class="text-center font-weight-bold">Algo salió
                                                                            mal</p>
                                                                        <p id="error-message" class="text-center"></p>
                                                                        <br />
                                                                    </div>
                                                                    <div id="success-response">
                                                                        <br />
                                                                        <p><b>Número de transacción: </b><span
                                                                                id="payment-id"></span></p>
                                                                        <p><span id="payment-status"></span></p>
                                                                        <p><span id="payment-detail"></span></p>
                                                                        <br />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="__boton-recargar">
                                            <button class="btn btn-primary" id="reload">Volver a intentar</button>
                                        </div>
                                        <div class="__boton-volver">
                                            <a id="back-url" class="btn btn-primary" href="/alojamientosPedidos?op=2"
                                                role="button">
                                                VOLVER
                                            </a>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    @else
                        <div class="imagenPanelPC col-xl-7 cb-slideshow cb-slideshow-section">
                            <li><span>Image 01</span></li>
                            <li><span>Image 02</span></li>
                            <li><span>Image 03</span></li>
                            <li><span>Image 04</span></li>
                            <li><span>Image 05</span></li>
                        </div>
                    @endif
                @else
                    <div class="imagenPanelPC col-xl-7 cb-slideshow cb-slideshow-section">
                        <li><span>Image 01</span></li>
                        <li><span>Image 02</span></li>
                        <li><span>Image 03</span></li>
                        <li><span>Image 04</span></li>
                        <li><span>Image 05</span></li>
                    </div>
                @endif
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
                    {!! Form::open([
                        'url' => 'alojamientosPedidos/' . $alojamientoPedido->id,
                        'method' => $method,
                        'enctype' => 'multipart/form-data',
                    ]) !!}
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
                        <input type="hidden" id="confirmation-date"
                            value="{{ $alojamientoPedido->fecha_confirmacion }}">
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
                        <input type="hidden" id="arrival-date" value="{{ $alojamientoPedido->fecha_from }}">
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
                                @if ($alojamientoPedido->Alojamiento->tipo_alquiler == 'HU')
                                    x {{$cantidadHuespedes}} huespedes
                                @endif
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
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" style="" data-backdrop="static" data-keyboard="false"
        tabindex="-1">
        <div class="modal-dialog modal-sm"
            style="display: table;position: relative;margin: 0 auto;top: calc(50% - 24px);background-color: transparent;border: none;background-color: transparent;border: none;">
            <div class="modal-content" style="width: 48px;background-color: transparent;border: none;">
                <span class="fa fa-spinner fa-spin fa-3x"></span>
            </div>
        </div>
    </div>
@endsection
