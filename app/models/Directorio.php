<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Directorio extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "vistaDirectorio";

	public function scopeSoloActivos($query){
		$query->whereNull('fechaFin');
	}

	public function scopeTitularesActivos($query,$claves = NULL){
		$query = $query->where('nivel','=',1)->whereNull('fechaFin')
				->join('catalogoUnidadesResponsables AS unidad','unidad.idArea','=','vistaDirectorio.idArea')
				->select('vistaDirectorio.*','unidad.clave AS claveUnidad');
		if($claves){
			$query = $query->whereIn('unidad.clave',$claves);
		}
		return $query;
	}

	public function scopeResponsablesActivos($query,$clave_unidad = NULL){
		$query = $query->whereNull('fechaFin')
				->join('catalogoUnidadesResponsables AS unidad',function($join){
					$join->on('unidad.idArea','=','vistaDirectorio.idArea')
						->orOn('unidad.idArea','=','vistaDirectorio.idAreaDepende');
				})
				->select('vistaDirectorio.*');
		if($clave_unidad){
			$query = $query->where('unidad.clave','=',$clave_unidad);
		}
		return $query;
	}
}