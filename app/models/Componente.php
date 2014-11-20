<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Componente extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectoComponentes";
	
	public function scopeContenidoCompleto($query){
		return $query->with('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable');
	}

	public function actividades(){
		return $this->hasMany('Actividad','idComponente');
	}

	public function formula(){
		return $this->belongsTo('Formula','idFormula');
	}

	public function dimension(){
		return $this->belongsTo('Dimension','idDimensionIndicador');
	}

	public function frecuencia(){
		return $this->belongsTo('Frecuencia','idFrecuenciaIndicador');
	}

	public function tipoIndicador(){
		return $this->belongsTo('TipoIndicador','idTipoIndicador');
	}

	public function unidadMedida(){
		return $this->belongsTo('UnidadMedida','idUnidadMedida');
	}

	public function entregable(){
		return $this->belongsTo('Entregable','idEntregable');
	}
}