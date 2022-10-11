<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class AlojamientoCalendario extends Model
{
  protected $table = "alojamientos_calendario";

  public function Alojamiento()
  {
      return $this->belongsTo('App\Alojamiento');
  }
}