<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alojamiento extends Model
{
    public function reservaciones()
    {
        return $this->hasMany('App\AlojamientoPedido');
    }

    public function fotoAlojamiento()
    {
        return $this->hasMany('App\AlojamientoFoto');
    }

    public function Propietario()
    {
        return $this->belongsTo('App\User');
    }

    public function tipoFormateado()
    {
        $tipo = '';

        switch ($this->tipo_alojamiento) {
            case 'AP':
                $tipo = 'Apartamento';
                break;

            case 'CS':
                $tipo = 'Casa';
                break;

            case 'CB':
                $tipo = 'Cabaña';
                break;

            case 'FN':
                $tipo = 'Finca';
                break;

            case 'GL':
                $tipo = 'Glamping';
                break;

            case 'FH':
                $tipo = 'Finca Hotel';
                break;

            case 'HT':
                $tipo = 'Hotel';
                break;

            default:
                $tipo = 'error';
                break;
        }

        return $tipo;
    }

    public function precioFormateado($valor)
    {
        return number_format($valor, 0, ',', '.');
    }

    public function precioFormateadoMoneda($valor)
    {
        return number_format($valor, 0, ',', '.') . ' COP';
    }

    public function leyendaHuespedesCuartos()
    {
        $leyenda = '';

        // Huéspedes

        if ($this->huespedes != null) {
            $leyenda = '1 huésped';

            if ($this->huespedes > 1) {
                $leyenda = $this->huespedes . ' huéspedes';
            }

            // Cuartos

            if ($this->cuartos != null) {
                $cuartos = ' - 1 habitación';

                if ($this->cuartos > 1) {
                    $cuartos = ' - ' . $this->cuartos . ' habitaciones';
                }

                $leyenda .= $cuartos;
            }

            // Baños

            if (
                $this->banios_completos != null ||
                $this->banios_sin_ducha != null
            ) {
                $banios = ' - 1 baño';

                if ($this->banios_completos + $this->banios_sin_ducha > 1) {
                    $banios =
                        ' - ' .
                        ($this->banios_completos + $this->banios_sin_ducha) .
                        ' baños';
                }

                $leyenda .= $banios;
            }
        }

        return $leyenda;
    }

    public function cuartosFormateados()
    {
        $cuartos = '';

        $alojamientoCuartos = AlojamientoCuarto::where(
            'alojamiento_id',
            $this->id
        )
            ->orderBy('num_cuarto')
            ->get();

        foreach ($alojamientoCuartos as $key => $alojamientoCuarto) {
            $cuartoIndividual = '<div class="show-cuarto col-md-4 col-6">';

            $cuartoIndividual .=
                '<div class="show-cuartos-iconos">_ICONOS_</div>';

            $cuartoIndividual .= '<div class="show-cuartos-titulo">';

            if ($alojamientoCuarto->num_cuarto != 31) {
                $cuartoIndividual .=
                    'Dormitorio ' . $alojamientoCuarto->num_cuarto;
            } else {
                $cuartoIndividual .= 'Espacios compartidos';
            }

            $cuartoIndividual .= '</div>';

            $grande = 0;

            $chica = 0;

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_king,
                'king',
                $grande,
                true
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_queen,
                'queen',
                $grande,
                true
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_doble,
                'doble',
                $grande,
                true
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_semi_doble,
                'semidoble',
                $grande,
                true
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_sencilla,
                'sencilla',
                $chica,
                true
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_camarote,
                'camarote',
                $chica,
                true
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_auxiliar,
                'auxiliar',
                $chica,
                true
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_sofa,
                'sofá',
                $chica,
                true
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_otro_tipo_1,
                $alojamientoCuarto->camas_otro_tipo_nombre_1,
                $chica,
                false
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_otro_tipo_2,
                $alojamientoCuarto->camas_otro_tipo_nombre_2,
                $chica,
                false
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_otro_tipo_3,
                $alojamientoCuarto->camas_otro_tipo_nombre_3,
                $chica,
                false
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_otro_tipo_4,
                $alojamientoCuarto->camas_otro_tipo_nombre_4,
                $chica,
                false
            );

            $cuartoIndividual .= $this->cuartosFormateadosCama(
                $alojamientoCuarto->camas_otro_tipo_5,
                $alojamientoCuarto->camas_otro_tipo_nombre_5,
                $chica,
                false
            );

            $cuartoIndividual .= '</div>';

            $iconos = '';

            for ($i = 0; $i < $grande; $i++) {
                $iconos .= '<img src="' . asset('img/quenn.svg') . '" />';
            }

            for ($i = 0; $i < $chica; $i++) {
                $iconos .= '<img src="' . asset('img/sencilla.svg') . '" />';
            }

            $cuartoIndividual = str_replace(
                '_ICONOS_',
                $iconos,
                $cuartoIndividual
            );

            if ($grande + $chica > 0) {
                $cuartos .= $cuartoIndividual;
            }
        }

        return $cuartos;
    }

    public function cuartosFormateadosCama(
        $cantidad,
        $tipo,
        &$incremento,
        $esCama )
    {
        if ($cantidad != null && $cantidad > 0) {
            $incremento = $incremento + $cantidad;

            $prefijo = '';

            if ($esCama) {
                $prefijo = 'Cama ';
            }

            return '<div class="show-cuartos-cama">' .
                $prefijo .
                $tipo .
                ': ' .
                $cantidad .
                '</div>';
        } else {
            return '';
        }
    }

    public function baniosFormateados()
    {
        $banios = '';

        $banios1 = '';

        $banios2 = '';

        $banios .= '<div class="show-cuarto col-md-4">';

        $banios .=
            '<div class="show-cuartos-iconos-banio"><img src="' .
            asset('img/banio.svg') .
            '" /></div>';

        if ($this->banios_completos != null && $this->banios_completos > 1) {
            $banios1 =
                '<div class="show-cuartos-cama">Baños completos: ' .
                $this->banios_completos .
                '</div>';
        }

        if ($this->banios_sin_ducha != null && $this->banios_sin_ducha > 1) {
            $banios2 =
                '<div class="show-cuartos-cama">Baños simples:' .
                $this->banios_sin_ducha .
                '</div>';
        }

        if ($banios1 == '' && $banios2 == '') {
            $banios .= '<div class="show-cuartos-cama">No posee</div>';
        } else {
            $banios .= $banios1 . $banios2;
        }

        $banios .= '</div>';

        return $banios;
    }

    public function serviciosFormateados()
    {
        $servicios = '<div class="card-columns">';

        $divOpen =
            '<div class="show-servicio card"><table><tr><td class="show-servicio-td">';

        $divClose = '</td></tr></table></div>';

        if ($this->servicio_wifi) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('wifi');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_wifi,
                'WIFI'
            );

            $servicios .= $divClose;
        }

        if (
            $this->servicio_aa ||
            $this->servicio_vent ||
            $this->servicio_agua
        ) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('aire');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_aa,
                'Aire acondicionado'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_vent,
                'Ventilador'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_agua,
                'Agua caliente'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_sonido) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('audio');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_sonido,
                'Reproductor de sonido'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_tv || $this->servicio_cable) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('tv');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_tv,
                'Televisor'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_cable,
                'TV por cable'
            );

            $servicios .= $divClose;
        }

        if (
            $this->servicio_lav ||
            $this->servicio_sec ||
            $this->servicio_sec_pelo ||
            $this->servicio_plancha
        ) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('lavadora');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_lav,
                'Lavadora'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_sec,
                'Secadora'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_toallas || $this->servicio_sabanas) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('totallas');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_toallas,
                'Toallas'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_sabanas,
                'Sábanas'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_sec_pelo,
                'Secadora de pelo'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_plancha,
                'Plancha'
            );

            $servicios .= $divClose;
        }

        if (
            $this->servicio_cocina ||
            $this->servicio_nevera ||
            $this->servicio_utensillos ||
            $this->servicio_horno_elec ||
            $this->servicio_micro
        ) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('heladera');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_cocina,
                'Cocina'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_nevera,
                'Nevera'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_utensillos,
                'Utensillos de cocina'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_horno_elec,
                'Horno eléctrico'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_micro,
                'Microondas'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_piscina || $this->servicio_jacuzzi) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('pileta');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_piscina,
                'Piscina'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_jacuzzi,
                'Jacuzzi'
            );

            $servicios .= $divClose;
        }

        if (
            $this->servicio_asoleadoras ||
            $this->servicio_sombrillas ||
            $this->servicio_kiosko ||
            $this->servicio_hamacas
        ) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('sol');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_asoleadoras,
                'Asoleadoras'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_sombrillas,
                'Sombrillas'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_utensillos,
                'Kiosko'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_hamacas,
                'Hamacas'
            );

            $servicios .= $divClose;
        }

        if (
            $this->servicio_bbq ||
            $this->servicio_horno_len ||
            $this->servicio_estufa_len
        ) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('bbq');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_bbq,
                'BBQ'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_horno_len,
                'Horno de leña / barro'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_estufa_len,
                'Estufa de leña'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_verdes) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('bosque');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_verdes,
                'Zonas verdes'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_gimnasio) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('gim');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_gimnasio,
                'Gimnasio'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_chimenea) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('chimenea');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_chimenea,
                'Chimenea Interior'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_balcon) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('balcon');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_balcon,
                'Balcón'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_ascensor) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('elevador');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_ascensor,
                'Ascensor'
            );

            $servicios .= $divClose;
        }

        if ($this->servicio_parqueadero) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('auto');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_parqueadero,
                'Parqueadero'
            );

            $servicios .= $divClose;
        }

        if (
            $this->servicio_cancha_futbol ||
            $this->servicio_billar ||
            $this->servicio_ping_pong ||
            $this->servicio_tejo ||
            $this->servicio_rana ||
            $this->servicio_juegos_mesa
        ) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('cancha');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_cancha_futbol,
                'Cancha de fútbol'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_billar,
                'Billar / Pool'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_ping_pong,
                'Ping Pong'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_tejo,
                'Mini tejo'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_rana,
                'Rana'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_juegos_mesa,
                'Juegos de mesa'
            );

            $servicios .= $divClose;
        }

        if (
            $this->servicio_extintor ||
            $this->servicio_humo ||
            $this->servicio_alarma ||
            $this->servicio_botiquin ||
            $this->servicio_monoxido ||
            $this->servicio_caja_seg
        ) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('seguridad');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_extintor,
                'Extintor de fuego'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_humo,
                'Detector de humo'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_alarma,
                'Alarma'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_botiquin,
                'Botiquín primeros auxilios'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_monoxido,
                'Detector de monóxido de carbono'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_caja_seg,
                'Caja de seguridad'
            );

            $servicios .= $divClose;
        }

        if (
            $this->servicio_desayuno ||
            $this->servicio_almuerzo ||
            $this->servicio_cena
        ) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('desayuno');

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_desayuno,
                'Desayuno'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_almuerzo,
                'Almuerzo'
            );

            $servicios .= $this->serviciosFormateadosLeyenda(
                $this->servicio_cena,
                'Cena'
            );

            $servicios .= $divClose;
        }

        if (
            $this->servicio_adicional_nombre_1 != null ||
            $this->servicio_adicional_nombre_2 != null ||
            $this->servicio_adicional_nombre_3 != null ||
            $this->servicio_adicional_nombre_4 != null ||
            $this->servicio_adicional_nombre_5 != null
        ) {
            $servicios .= $divOpen;

            $servicios .= $this->serviciosFormateadosImagen('mas');

            $servicios .= $this->serviciosFormateadosLeyendaOtros(
                $this->servicio_adicional_nombre_1,
                $this->servicio_adicional_nombre_1
            );

            $servicios .= $this->serviciosFormateadosLeyendaOtros(
                $this->servicio_adicional_nombre_2,
                $this->servicio_adicional_nombre_2
            );

            $servicios .= $this->serviciosFormateadosLeyendaOtros(
                $this->servicio_adicional_nombre_3,
                $this->servicio_adicional_nombre_3
            );

            $servicios .= $this->serviciosFormateadosLeyendaOtros(
                $this->servicio_adicional_nombre_4,
                $this->servicio_adicional_nombre_4
            );

            $servicios .= $this->serviciosFormateadosLeyendaOtros(
                $this->servicio_adicional_nombre_5,
                $this->servicio_adicional_nombre_5
            );

            $servicios .= $divClose;
        }

        $servicios .= '</div>';

        return $servicios;
    }

    public function serviciosFormateadosImagen($imagen)
    {
        return '<div class="show-cuartos-iconos-servicio"><img src="' .
            asset('img/' . $imagen . '.svg') .
            '" /></div></td><td>';
    }

    public function serviciosFormateadosLeyenda($opcion, $titulo)
    {
        if ($opcion) {
            return '<div class="show-cuartos-cama">' . $titulo . '</div>';
        }
    }

    public function serviciosFormateadosLeyendaOtros($opcion, $titulo)
    {
        if ($opcion != null) {
            return '<div class="show-cuartos-cama">' . $titulo . '</div>';
        }
    }

    public function sitiosFormateados()
    {
        $sitios = '';

        $sitios .= '<div class="show-sitio col-md-12">';

        $sitios .= $this->sitioFormateadoConValor(
            $this->sitio_playa,
            'Playa',
            $this->sitio_playa_distancia
        );

        $sitios .= $this->sitioFormateadoConValor(
            $this->sitio_rio,
            'Río',
            $this->sitio_rio_distancia
        );

        $sitios .= $this->sitioFormateadoConValor(
            $this->sitio_parque,
            'Parque',
            $this->sitio_parque_distancia
        );

        $sitios .= $this->sitioFormateadoConValor(
            $this->sitio_sendero_caminar,
            'Sendero para caminar',
            $this->sitio_sendero_caminar_distancia
        );

        $sitios .= $this->sitioFormateadoConValor(
            $this->sitio_sendero_ecologico,
            'Sendero ecológico',
            $this->sitio_sendero_ecologico_distancia
        );

        $sitios .= $this->sitioFormateadoConValor(
            $this->sitio_ruta_bici_,
            'Ruta para bicicleta',
            $this->sitio_ruta_bici_distancia
        );

        $sitios .= $this->sitioFormateadoConValor6(
            $this->sitio_act_tur,
            'Actividad turística',
            $this->sitio_act_tur_detalle_1,
            $this->sitio_act_tur_detalle_2,
            $this->sitio_act_tur_detalle_3,
            $this->sitio_act_tur_detalle_4,
            $this->sitio_act_tur_detalle_5
        );

        $sitios .= $this->sitioFormateadoConValor2(
            $this->sitio_parque_tem,
            'Parque temático',
            $this->sitio_parque_tem_nombre,
            $this->sitio_parque_tem_distancia
        );

        $sitios .= $this->sitioFormateadoConValor2(
            $this->sitio_parque_div,
            'Parque de diversiones',
            $this->sitio_parque_div_nombre,
            $this->sitio_parque_div_distancia
        );

        $sitios .= $this->sitioFormateadoConValor2(
            $this->sitio_parque_acua,
            'Parque acuático',
            $this->sitio_parque_acua_nombre,
            $this->sitio_parque_acua_distancia
        );

        $sitios .= $this->sitioFormateadoConValor(
            $this->sitio_pesca,
            'Lugar de pesca',
            $this->sitio_pesca_distancia
        );

        $sitios .= $this->sitioFormateadoConValor6(
            $this->sitio_act_dep,
            'Actividad deportiva',
            $this->sitio_act_dep_detalle_1,
            $this->sitio_act_dep_detalle_2,
            $this->sitio_act_dep_detalle_3,
            $this->sitio_act_dep_detalle_4,
            $this->sitio_act_dep_detalle_5
        );

        $sitios .= $this->sitioFormateadoConValor2(
            $this->sitio_sup,
            'Supermercado',
            $this->sitio_sup_nombre,
            $this->sitio_sup_distancia
        );

        $sitios .= $this->sitioFormateadoConValor2(
            $this->sitio_drog,
            'Droguería',
            $this->sitio_drog_nombre,
            $this->sitio_drog_distancia
        );

        $sitios .= $this->sitioFormateadoConValor6(
            $this->sitio_centro_com,
            'Centro Comercial',
            $this->sitio_centro_com_nombre_1,
            $this->sitio_centro_com_nombre_2,
            $this->sitio_centro_com_nombre_3,
            $this->sitio_centro_com_nombre_4,
            $this->sitio_centro_com_nombre_5
        );

        $sitios .= $this->sitioFormateadoConValor6(
            $this->sitio_rest,
            'Restaurante',
            $this->sitio_rest_nombre_1,
            $this->sitio_rest_nombre_2,
            $this->sitio_rest_nombre_3,
            $this->sitio_rest_nombre_4,
            $this->sitio_rest_nombre_5
        );

        $sitios .= $this->sitioFormateadoConValor(
            $this->sitio_gimnasio,
            'Gimnasio',
            $this->sitio_gimnasio_distancia
        );

        $sitios .= $this->sitioFormateadoConValor(
            $this->sitio_iglesia,
            'Iglesia',
            $this->sitio_iglesia_distancia
        );

        $sitios .= $this->sitioFormateadoConValor2(
            $this->sitio_hospital,
            'Hospital / Clínica',
            $this->sitio_hospital_nombre,
            $this->sitio_hospital_distancia
        );

        $sitios .= $this->sitioFormateado(
            $this->sitio_transporte,
            'Transporte público'
        );

        $sitios .= '</div>';

        return $sitios;
    }

    public function sitioFormateado($opcion, $titulo)
    {
        if ($opcion) {
            $resultado =
                '<div class="show-sitio-individual"><table><tr><td style="vertical-align: top"><div class="show-sitio-icono"><img src="' .
                asset('img/check.svg') .
                '" /></div></td><td><div class="show-sitio-titulo">' .
                $titulo;

            $resultado .= '</div></td></tr></table></div>';

            return $resultado;
        }
    }

    public function sitioFormateadoConValor($opcion, $titulo, $valor)
    {
        if ($opcion) {
            $resultado =
                '<div class="show-sitio-individual"><table><tr><td style="vertical-align: top"><div class="show-sitio-icono"><img src="' .
                asset('img/check.svg') .
                '" /></div></td><td><div class="show-sitio-titulo">' .
                $titulo;

            if ($valor != null) {
                $resultado .=
                    '<span class="show-sitio-valor"> - ' . $valor . '</span>';
            }

            $resultado .= '</div></td></tr></table></div>';

            return $resultado;
        }
    }

    public function sitioFormateadoConValor6(
        $opcion,
        $titulo,
        $valor1,
        $valor2,
        $valor3,
        $valor4,
        $valor5
    ) {
        if ($opcion) {
            $resultado =
                '<div class="show-sitio-individual"><table><tr><td style="vertical-align: top"><div class="show-sitio-icono"><img src="' .
                asset('img/check.svg') .
                '" /></div></td><td><div class="show-sitio-titulo">' .
                $titulo;

            if ($valor1 != null) {
                $resultado .=
                    '<span class="show-sitio-valor"> - ' . $valor1 . '</span>';
            }

            if ($valor2 != null) {
                $resultado .=
                    '<span class="show-sitio-valor"> - ' . $valor2 . '</span>';
            }

            if ($valor3 != null) {
                $resultado .=
                    '<span class="show-sitio-valor"> - ' . $valor3 . '</span>';
            }

            if ($valor4 != null) {
                $resultado .=
                    '<span class="show-sitio-valor"> - ' . $valor4 . '</span>';
            }

            if ($valor5 != null) {
                $resultado .=
                    '<span class="show-sitio-valor"> - ' . $valor5 . '</span>';
            }

            $resultado .= '</div></td></tr></table></div>';

            return $resultado;
        }
    }

    public function sitioFormateadoConValor2($opcion, $titulo, $valor1, $valor2)
    {
        if ($opcion) {
            $resultado =
                '<div class="show-sitio-individual"><table><tr><td style="vertical-align: top"><div class="show-sitio-icono"><img src="' .
                asset('img/check.svg') .
                '" /></div></td><td><div class="show-sitio-titulo">' .
                $titulo;

            if ($valor1 != null) {
                $resultado .=
                    '<span class="show-sitio-valor"> - ' . $valor1 . '</span>';
            }

            if ($valor2 != null) {
                $resultado .=
                    '<span class="show-sitio-valor"> - ' . $valor2 . '</span>';
            }

            $resultado .= '</div></td></tr></table></div>';

            return $resultado;
        }
    }

    public function normasFormateadas()
    {
        $normas = '';

        $normas .= '<div class="show-sitio col-md-12">';

        $normas .= $this->normaFormateada(
            $this->regla_mascotas,
            'Acepto mascotas'
        );

        $normas .= $this->normaFormateada(
            $this->regla_fumadores,
            'Apto para fumadores'
        );

        $normas .= $this->normaFormateada(
            $this->regla_fiestas,
            'Se permiten fiestas o eventos'
        );

        $normas .= $this->normaAdicionalFormateada($this->regla_adicional_1);

        $normas .= $this->normaAdicionalFormateada($this->regla_adicional_2);

        $normas .= $this->normaAdicionalFormateada($this->regla_adicional_3);

        $normas .= $this->normaAdicionalFormateada($this->regla_adicional_4);

        $normas .= $this->normaAdicionalFormateada($this->regla_adicional_5);

        $normas .= '</div>';

        return $normas;
    }

    public function normaAdicionalFormateada($titulo)
    {
        if ($titulo != null) {
            return '<div class="show-sitio-individual"><table><tr><td style="vertical-align: top"><div class="show-sitio-icono"><img src="' .
                asset('img/check.svg') .
                '" /></div></td><td><div class="show-sitio-titulo">' .
                $titulo .
                '</div></td></tr></table></div>';
        }
    }

    public function normaFormateada($opcion, $titulo)
    {
        $imagen = 'cruz';

        if ($opcion) {
            $imagen = 'check';
        }

        return '<div class="show-sitio-individual"><table><tr><td style="vertical-align: top"><div class="show-sitio-icono"><img src="' .
            asset('img/' . $imagen . '.svg') .
            '" /></div></td><td><div class="show-sitio-titulo">' .
            $titulo .
            '</div></td></tr></table></div>';
    }
}
