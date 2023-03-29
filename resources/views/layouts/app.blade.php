<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:url" content="https://alojacolombia.com/" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Aloja Colombia" />
    <meta property="og:description" content="Busca - Encuentra - Vive" />
    <meta property="og:image" content="https://www.alojacolombia.com/img/logo-colombia2.png" />  
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Aloja Colombia') }}</title>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('js/owl.carousel.js') }}" defer></script>
    <script src="{{ asset('plugins/Chocolat/js/chocolat.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:ital,wght@0,200;0,400;1,200;1,400" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app-ric.css?1_1') }}" rel="stylesheet">
    <link href="{{ asset('css/slideshow.css?1_1') }}" rel="stylesheet">
    <link href="{{ asset('css/styles-ric.css?1_1') }}" rel="stylesheet">
    <link href="{{ asset('css/statistics.css?1_1') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/styles.css') }}" rel="stylesheet" type="text/css" >
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/Chocolat/css/chocolat.css') }}" rel="stylesheet">
    <link href="{{ asset('css/owl.theme.default.css') }}" rel="stylesheet">
    {{-- <script src="https://kit.fontawesome.com/60d20bc537.js" crossorigin="anonymous"></script> --}}
    {{ Html::favicon('/favicon.png') }}
    @stack('head')
    {{-- //MERCADOPAGO --}}
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script type="text/javascript" src="{{ asset('js/index.js') }}" defer></script>
    
</head>
@if (Request::segment(1) == 'login' || Request::segment(1) == 'register' || Request::segment(1) == 'password')
    <body class="aloja-login-body">
    @else
        <body>
@endif
@if (Request::is('/'))
    <div id="app" class="home-page">
    @else
        <div id="app">
@endif
@if (Request::is('/') ||
    Request::segment(2) == 'busqueda' ||
    Request::segment(1) == 'alojar' ||
    $view_name == 'alojamientos.show')
    <nav class="navbar navbar-light bg-white shadow-sm">
    @else
        <nav class="navbar navbar-light bg-white shadow-sm nav-formulario">
@endif
<div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">
        <img class="logo" src="{{ url('/img/logo-colombia.svg') }}">
    </a>
    @guest
        <button class="navbar-toggler nav-imagen-generico" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
    @else
        <?php
        $user = \App\User::find(\Auth::user()->id);
        $fotoUsuario = $user->sourceFoto();
        ?>
        <button class="navbar-toggler nav-imagen-usuario" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="{{ __('Toggle navigation') }}" style="background: none">
            <span class="navbar-toggler-icon"></span>
            @if ($fotoUsuario == '')
                <img class="" src="{{ asset('img/carita.svg') }}" />
            @else
                <img class="" src="{{ $fotoUsuario }}" />
            @endif
        </button>
    @endguest
    @if (Request::is('/') ||
        Request::segment(2) == 'busqueda' ||
        Request::segment(1) == 'alojar' ||
        $view_name == 'alojamientos.show' ||
        app('request')->input('op') == 2)
        @if (app('request')->input('op') == 2)
        @else
            <button class="btn boton_abrir_busqueda_menu" id="abrir-busqueda"
                onclick="window.scrollTo(0, 0);$('#panel-busqueda').toggle('slow'); $('#abrir-busqueda').toggle('slow'); $('#cerrar-busqueda').toggle('slow');"
                type="button"><i class="fa fa-search" aria-hidden="true"></i></button>
        @endif
        @guest
            <a class="boton-quiero-alojar btn" href="{{ url('/alojamientos') }}"><span>Quiero alojar</span></a>
        @else
            @if (!Request::is('/'))
                <a class="btn buscarAlojamiento" href="{{ url('/') }}">Busca alojamientos</a>
            @endif
            <a class="boton-quiero-alojar btn" href="{{ url('/alojamientos') }}"><span>Modo alojador</span></a>
        @endguest
    @else
        @if (!(Request::segment(1) == 'login' || Request::segment(1) == 'register' || Request::segment(1) == 'password'))
            <a class="boton-quiero-alojar btn boton-quiero-alojar-buscar-alojamiento" href="{{ url('/') }}">Busca
                alojamientos</a>
        @endif
    @endif
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left Side Of Navbar -->
        <ul class="navbar-nav mr-auto">
        </ul>
        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ml-auto">
            <!-- Authentication Links -->
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Ingresar</a>
                </li>
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Registrate</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('alojamientos.index') }}">Quiero alojar</a>
                </li>
            @else
                @if (!(Request::is('/') ||
                    Request::segment(1) == 'alojar' ||
                    Request::segment(2) == 'busqueda' ||
                    Request::segment(1) == 'alojar' ||
                    $view_name == 'alojamientos.show' ||
                    (Request::segment(1) == 'alojamientosPedidos' && app('request')->input('op') == 2)
                ))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('alojamientos.index') }}">Alojamientos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('alojamientosPedidos.index') }}?op=1">Reservas</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('alojamientosPedidos.index') }}?op=2">Mis viajes</a>
                    </li>
                @endif
                @if (Auth::user()->esAdministrador())
                    <li class="nav-item">
                        <a class="nav-link" href="/statistics">Información</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/users">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/properties">Propiedades</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="/statistics/{{ Auth::user()->id }}">Información</a>
                    </li>
                @endif
                <div class="dropdown-divider"></div>
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->nombreCompleto() }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('users.edit', \Auth::user()->id) }}">Editar perfil</a>
                        <a class="dropdown-item" href="{{ route('changePassword') }}">Cambiar contraseña</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</div>
@if (Request::is('/') ||
    Request::segment(2) == 'busqueda' ||
    Request::segment(1) == 'alojar' ||
    $view_name == 'alojamientos.show')
    <div id="panel-busqueda">
        <br /><br />
        {!! Form::open([
            'url' => 'alojamientos/busqueda',
            'method' => 'GET',
            'id' => 'menu-busqueda',
            'onsubmit' => 'return validarDatos();',
        ]) !!}
        <?php
        $fecha_actual = \Carbon\Carbon::now()->format('Y-m-d');
        $queryLugares = '
                                SELECT  DISTINCT(valor) AS valor FROM (
                                  SELECT DISTINCT(barrio) AS valor FROM alojamientos
                                  UNION
                                  SELECT DISTINCT(ciudad) AS valor FROM alojamientos
                                  UNION
                                  SELECT DISTINCT(municipio) AS valor FROM alojamientos
                                  UNION
                                  SELECT DISTINCT(departamento) AS valor FROM alojamientos
                                ) AS lugares ORDER BY valor
                                  ';
        
        $lugares = DB::select($queryLugares);
        ?>
        <div class="row" style="color: black; text-shadow: 0px 0px 0px black;">
            <div class="col-xl-4">
                {!! Form::label('l', '¿A dónde vamos?') !!}
                <select name="l" class="form-control js-select2" required="">
                    <option value=""></option>
                    @foreach ($lugares as $item)
                        @if ($item->valor == app('request')->input('l'))
                            <option selected="selected" value="{{ $item->valor }}"> {{ $item->valor }} </option>
                        @else
                            <option value="{{ $item->valor }}"> {{ $item->valor }} </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-xl-2">
                {!! Form::label('fd', '¿Desde cuándo?') !!}
                {!! Form::date('fd', app('request')->input('fd'), [
                    'class' => 'form-control',
                    'min' => $fecha_actual,
                    'onchange' => 'setearFechaHastaDefault()',
                ]) !!}
            </div>
            <div class="col-xl-2">
                {!! Form::label('fh', '¿Hasta cuándo?') !!}
                {!! Form::date('fh', app('request')->input('fh'), ['class' => 'form-control', 'min' => $fecha_actual]) !!}
            </div>
            <div class="col-xl-2">
                {!! Form::label('h', '¿Cuántos somos?') !!}
                {!! Form::number('h', app('request')->input('h'), [
                    'step' => '1',
                    'min' => '0',
                    'max' => '50',
                    'class' => 'form-control',
                ]) !!}
            </div>
            <div class="col-xl-2" style="align-self: center;">
                <button class="btn boton_submit_busqueda_menu" type="submit" id="submit-busqueda"><i
                        class="fa fa-search" aria-hidden="true"></i></button>
                <button class="btn boton_cerrar_busqueda_menu" type="button" style="display: none;"
                    id="cerrar-busqueda"
                    onclick="$('#panel-busqueda').toggle('slow'); $('#abrir-busqueda').toggle('slow'); $('#cerrar-busqueda').toggle('slow');"><i
                        class="fa fa-close" aria-hidden="true"></i></button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endif
</nav>
<main class="">
    @yield('content')
</main>
@if (Request::is('/') ||
    Request::segment(2) == 'busqueda' ||
    Request::segment(1) == 'alojar' ||
    $view_name == 'alojamientos.show')
    <div class="row pie pie-pc">
        <div class="col-md-3 text-center">
        </div>
        <div class="col-md-2">
            <div class="pie-logo"><img src="{{ url('/img/logo-colombia.svg') }}"></div>
            <div><a class="pie-boton" href="{{ route('alojamientos.index') }}">Quiero Alojar</a></div>
            <div><a class="pie-boton" href="{{ url('/') }}">Busco Alojamiento</a></div>
        </div>
        <div class="col-md-2">
            <div style="max-width: fit-content; margin-right: auto; display: table; margin-left: auto;">
                <div class="pie-titulo text-center">¿Necesitas ayuda?</div>
                <div><a class="pie-link text-center" href="mailto:hola@alojacolombia.com">hola@alojacolombia.com</a>
                </div>
                <br />
                <div class="text-center" style="padding-left: 10px;">
                    <div>
                        <a class="pie-red" href="https://www.facebook.com/alojaColombiaCom/" target="_blank"><img
                                src="{{ url('/img/facebook.svg') }}"></a>
                        <a class="pie-red" href="https://www.instagram.com/aloja.colombia/" target="_blank"><img
                                src="{{ url('/img/instagram.svg') }}"></a>
                        <a class="pie-red"
                            href="https://api.whatsapp.com/send?phone=+573227704646&text=Bienvenidos%20a%20Aloja%20Colombia,%20env%C3%ADanos%20tu%20consulta%20y%20en%20breve%20nos%20contactaremos%20contigo."
                            target="_blank"><img src="{{ url('/img/whatsapp.svg') }}"></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div style="float: right;">
                <div>{!! link_to('/doc/terminos.pdf', 'Términos y Condiciones', ['target' => '_blank', 'class' => 'pie-link2']) !!}</div>
                <div>{!! link_to('/doc/privacidad.pdf', 'Política de Privacidad', ['target' => '_blank', 'class' => 'pie-link2']) !!}</div>
            </div>
        </div>
        <div class="col-md-3 text-center">
        </div>
        <div class="col-md-5 text-center">
        </div>
        <div class="col-md-2 text-center">
            <div class="pie-linea text-center"></div>
        </div>
        <div class="col-md-12 text-center">
            <div class="pie-pie">Copyright © {{ date('Y') }} - Aloja Colombia - Todos los derechos reservados
            </div>
        </div>
    </div>
    <div class="row pie pie-celu">
        <div class="col-6">
            <div class="pie-logo"><img src="{{ url('/img/logo-colombia.svg') }}"></div>
            <div><a class="pie-boton" href="{{ route('alojamientos.index') }}">Quiero Alojar</a></div>
            <div><a class="pie-boton" href="{{ url('/') }}">Busco Alojamiento</a></div>
            <br />
            <div><a style="float: left;" class="pie-red" href="https://www.facebook.com/alojaColombiaCom/"
                    target="_blank"><img src="{{ url('/img/facebook.svg') }}"></a></div>
            <div><a style="float: left;" class="pie-red" href="https://www.instagram.com/aloja.colombia/"
                    target="_blank"><img src="{{ url('/img/instagram.svg') }}"></a></div>
            <div><a class="pie-red"
                    href="https://api.whatsapp.com/send?phone=+573227704646&text=Bienvenidos%20a%20Aloja%20Colombia,%20env%C3%ADanos%20tu%20consulta%20y%20en%20breve%20nos%20contactaremos%20contigo."
                    target="_blank"><img src="{{ url('/img/whatsapp.svg') }}"></a></div>
        </div>
        <div class="col-6">
            <div class="pie-titulo">¿Necesitas ayuda?</div>
            <div><a class="pie-link" href="mailto:hola@alojacolombia.com">hola@alojacolombia.com</a></div>
            <br />
            <div>{!! link_to('/doc/terminos.pdf', 'Términos y Condiciones', ['target' => '_blank', 'class' => 'pie-link2']) !!}</div>
            <div>{!! link_to('/doc/privacidad.pdf', 'Política de Privacidad', ['target' => '_blank', 'class' => 'pie-link2']) !!}</div>
        </div>
        <div class="col-md-12 text-center">
            <div class="pie-pie">Copyright © {{ date('Y') }} - Aloja Colombia - Todos los derechos reservados
            </div>
        </div>
    </div>
@endif
</div>
</body>
@stack('footer')
<script src="{{ asset('plugins/select2/js/select2.min.js') }}" defer></script>
<script src="{{ asset('plugins/select2/js/i18n/es.js') }}" defer></script>
<script type="text/javascript">
    // Cierre de menú
    $(document).ready(function() {
        $(document).click(function() {
            // if($(".navbar-collapse").hasClass("in")){
            $('#navbarSupportedContent').collapse('hide');
            // }
        });
    });
    function validarDatos() {
        fd = document.getElementsByName("fd")[0];
        fh = document.getElementsByName("fh")[0];
        if ((fh.value == "" && fd.value != "") || (fh.value != "" && fd.value == "")) {
            alert("Debe completar fecha desde y hasta o dejar las dos en blanco.");
            fd.focus();
            return false;
        }
        if (fh.value < fd.value) {
            alert("Fecha desde debe ser menor a fecha hasta.");
            fd.focus();
            return false;
        }
        return true;
    }
    function setearFechaHastaDefault() {
        fd = document.getElementsByName("fd")[0];
        fh = document.getElementsByName("fh")[0];
        if (fh.value == "" && fd.value != "") {
            fh.value = fd.value;
        }
    }
    $(document).ready(function() {
        $('.js-select2').select2({
            language: "es",
            minimumInputLength: 1,
        });
        $("#l").change();
        $("#panel-busqueda .select2-selection").css("height", $("#panel-busqueda input[name=h]").css("height"));
    });
    $("#navbarSupportedContent").css("top", $(".navbar").css("height"));
    @if (Request::is('/') ||
        Request::segment(2) == 'busqueda' ||
        Request::segment(1) == 'alojar' ||
        $view_name == 'alojamientos.show')
        // Hide Header on on scroll down
        var didScroll;
        var lastScrollTop = 0;
        var delta = 5;
        var navbarHeight = 80;
        $(window).scroll(function(event) {
            didScroll = true;
        });
        setInterval(function() {
            if (didScroll) {
                hasScrolled();
                didScroll = false;
            }
        }, 250);
        function hasScrolled() {
            var st = $(this).scrollTop();
            // Make sure they scroll more than delta
            if (Math.abs(lastScrollTop - st) <= delta)
                return;
            // If they scrolled down and are past the navbar, add class .nav-up.
            // This is necessary so you never see what is "behind" the navbar.
            if (st > lastScrollTop && st > navbarHeight) {
                // Scroll Down
                $('#panel-busqueda').fadeOut();
                $('#cerrar-busqueda').fadeOut();
                $('#abrir-busqueda').fadeIn();
            }
            if (st == 0 && $(window).width() > 767) {
                $('.home-page #panel-busqueda').fadeIn();
                $('.home-page #abrir-busqueda').fadeOut();
            }
            lastScrollTop = st;
        }
    @endif
</script>
</html>
