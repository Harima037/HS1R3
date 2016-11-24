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

	public function fondoFinanciamiento(){
		return $this->belongsTo('FuenteFinanciamiento','idFondoFinanciamiento');
	}

	public function subFuentesFinanciamiento(){
		return $this->belongsToMany('SubFuenteFinanciamiento','relProyectoFinanciamientoSubFuente','idProyectoFinanciamiento','idSubFuenteFinanciamiento');
	}
}