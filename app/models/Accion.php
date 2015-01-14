<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Accion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibapAcciones";

	public function componente(){
        return $this->belongsTo('Componente','idComponente');
    }

    public function datosComponente(){
    	return $this->hasOne('FibapDatosComponente','idAccion');
    }

		public function propuestasFinanciamiento(){
			return $this->hasMany('PropuestaFinanciamiento','idAccion');
		}
}
