<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Storage;

class AlojamientoFoto extends Model
{

  protected $table = "alojamientos_fotos";

  public function Alojamiento()
  {
      return $this->belongsTo('App\Alojamiento');
  }

  public function srcImagen($ancho) {
  	
  	if ( config('app.env', 'local') == 'local' ) {
  		$src = url('/uploads/' . $this->archivo);
  	}
	else {
	  	$src = url('/red/image.php?width=' . $ancho . '&image=/uploads/' . $this->archivo);
	  	//dd($src);
	  	//https://alojacolombia.com/red/image.php?width=400&height=500&image=/uploads/propiedades/14/foto9/IMG_8473.JPG&nocache
	}
  	return $src;
  }

}