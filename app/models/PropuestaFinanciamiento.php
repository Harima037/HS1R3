<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class PropuestaFinanciamiento extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibapPropuestaFinanciamiento";

	public function origen(){
		return $this->belongsTo('OrigenFinanciamiento','idOrigenFinanciamiento');
	}

	public function scopeAgrupadoPorFibap($query){
		return $query->select('id','idFibap','idAccion','idOrigenFinanciamiento',DB::raw('sum(cantidad) AS cantidad'))
					->groupBy('idOrigenFinanciamiento');
	}

	public function scopeAgrupadoPorFibapCompleto($query){
		return $query->select('fibapPropuestaFinanciamiento.id','idFibap','idAccion','idOrigenFinanciamiento','origenesFinanciamiento.descripcion AS origenFinanciamiento',DB::raw('sum(cantidad) AS cantidad'))
					->leftjoin('catalogoOrigenesFinanciamiento AS origenesFinanciamiento','origenesFinanciamiento.id','=','idOrigenFinanciamiento')
					->groupBy('idOrigenFinanciamiento');
	}
}