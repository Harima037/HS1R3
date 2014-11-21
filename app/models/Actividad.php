<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Actividad extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteActividades";

	public function scopeContenidoCompleto($query){
		return $query->with('formula','dimension','frecuencia','tipoIndicador','unidadMedida');
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
}