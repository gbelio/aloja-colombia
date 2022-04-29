<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlojamientoCuarto extends Model
{

  protected $table = "alojamientos_cuartos";

  public function Alojamiento()
  {
      return $this->belongsTo('App\Alojamiento');
  }

}