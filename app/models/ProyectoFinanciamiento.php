<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProyectoFinanciamiento extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectoFinanciamiento";

	public function fuenteFinanciamiento(){
		return $this->belongsTo('FuenteFinanciamiento','idFuenteFinanciamiento');
	}

	public function destinoGasto(){
		return $this->belongsTo('DestinoGasto','idDestinoGasto');
	}

	public function subFuentesFinanciamiento(){
		return $this->belongsToMany('SubFuenteFinanciamiento','relProyectoFinanciamientoSubFuente','idProyectoFinanciamiento','idSubFuenteFinanciamiento');
	}
}