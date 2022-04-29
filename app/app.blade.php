<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="{{ asset('css/styles.css?v=18') }}" rel="stylesheet" type="text/css" >

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" >
    @stack('head')
</head>
<body>
    @if ( Request::is('/') )
    <div id="app" class="home-page">
    @else
    <div id="app">
    @endif
        @if ( Request::is('/') || Request::segment(2) =='busqueda')
        <nav class="navbar navbar-light bg-white shadow-sm">
        @else
        <nav class="navbar navbar-light bg-white shadow-sm nav-formulario">
        @endif
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img class="logo" src="{{ url('/img/logo.svg') }}">
                </a>

                @if ( Request::is('/') || Request::segment(2) =='busqueda')

                <button class="btn boton_abrir_busqueda_menu" id="abrir-busqueda" onclick="$('#panel-busqueda').toggle('slow')" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>

                @endif

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('alojamientos.index') }}">Alojamientos</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
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

            @if ( Request::is('/') || Request::segment(2) =='busqueda')

                <div id="panel-busqueda">

                    <br/><br/>

                    {!! Form::open(array('url' => 'alojamientos/busqueda', 'method' => 'GET', 'id' => 'menu-busqueda', 'onsubmit' => 'return validarDatos();')) !!}

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

                    <div class="row" style="">
                        
                            <div class="col-xl-4">
                                {!! Form::label('l', 'A dónde vamos?' ) !!}
                                <select name="l" class="form-control js-select2" required=""> 
                                    <option value=""></option>
                                    @foreach ($lugares as $item)
                                        <option value="{{ $item->valor }}"> {{ $item->valor }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2">
                                {!! Form::label('fd', 'Desde cuándo?' ) !!}
                                {!! Form::date('fd', app('request')->input('fd'), ['class' => 'form-control', 'min' => $fecha_actual ]) !!}
                            </div>
                            <div class="col-xl-2">
                                {!! Form::label('fh', 'Hasta cuándo?' ) !!}
                                {!! Form::date('fh', app('request')->input('fh'), ['class' => 'form-control',  'min' => $fecha_actual]) !!}
                            </div>
                            <div class="col-xl-2">
                                {!! Form::label('h', 'Cuántos somos?' ) !!}
                                {!! Form::number('h', app('request')->input('h'), ['step' => '1', 'min' => '0', 'max' => '50', 'class' => 'form-control']) !!}
                            </div>
                            <div class="col-md-2">
                                <br/>
                                <button class="btn boton_submit_busqueda_menu" type="submit" id="submit-busqueda"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>

                    </div>

                    {!! Form::close() !!}                        
                        
                </div>

                
            @endif

        </nav>

        <main class="">
            @yield('content')
        </main>
    </div>
</body>
@stack('footer')

<script src="{{ asset('plugins/select2/js/select2.min.js') }}" defer></script>
<script src="{{ asset('plugins/select2/js/i18n/es.js') }}" defer></script>

<script type="text/javascript">

    function validarDatos() {
      fd = document.getElementsByName("fd")[0];
      fh = document.getElementsByName("fh")[0];
      if( (fh.value == "" && fd.value != "") || (fh.value != "" && fd.value == "") ) {
        alert("Debe completar fecha desde y hasta o dejar las dos en blanco."); 
        fd.focus();
        return false;        
      }
      if( fh.value < fd.value  ) {
        alert("Fecha desde debe ser menor a fecha hasta."); 
        fd.focus();
        return false;        
      }
      return true;
    }

    $(document).ready(function () {
      $('.js-select2').select2({
        language: "es"
      });
      $("#l").change();
    });

</script>

</html>
