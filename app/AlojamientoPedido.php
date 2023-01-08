<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Storage;

class AlojamientoPedido extends Model
{
    protected $table = 'alojamientos_pedidos';

    public function Alojamiento()
    {
        return $this->belongsTo('App\Alojamiento');
    }

    public function Huesped()
    {
        return $this->belongsTo('App\User', 'huesped_id');
    }

    public function totalComisiones()
    {
        return $this->valor_servicio + $this->valor_comision_servicio;
    }

    public function valorSubtotalBaja()
    {
        if($this->Alojamiento->tipo_alquiler == 'HU'){
            return $this->cantidad_noches_baja * $this->valor_noche_promedio_baja * $this->huespedes;
        }
        return $this->cantidad_noches_baja * $this->valor_noche_promedio_baja;
    }

    public function valorSubtotalMEdia()
    {
        if($this->Alojamiento->tipo_alquiler == 'HU'){
            return $this->cantidad_noches_media * $this->valor_noche_promedio_media * $this->huespedes;
        }
        return $this->cantidad_noches_media * $this->valor_noche_promedio_media;
    }

    public function valorSubtotalAlta()
    {
        if($this->Alojamiento->tipo_alquiler == 'HU'){
            return $this->cantidad_noches_alta * $this->valor_noche_promedio_alta * $this->huespedes;
        }
        return $this->cantidad_noches_alta * $this->valor_noche_promedio_alta;
    }
}
