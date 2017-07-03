<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Directorio extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "vistaDirectorio";
	protected $fillable =  [ 'nombre', 'cargo', 'email'];

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
		$query = $query->whereNull('fechaFin')->where('nivel','!=',1)
				->join('catalogoUnidadesResponsables AS unidad',function($join){
					$join->on('vistaDirectorio.mascara','like',DB::raw('concat("%",unidad.idArea,"%")'))
						->orOn('unidad.idArea','=','vistaDirectorio.idArea');
				})
				->select('vistaDirectorio.*');
		if($clave_unidad){
			$query = $query->where('unidad.clave','=',$clave_unidad);
		}
		return $query;
	}
}