<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DistribucionPresupuesto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibapDistribucionPresupuesto";

	public function objetoGasto(){
        return $this->belongsTo('ObjetoGasto','idObjetoGasto');
    }

    public function jurisdiccion(){
        return $this->belongsTo('Jurisdiccion','claveJurisdiccion','clave');
    }

    public function municipio(){
    	return $this->belongsTo('Municipio','claveMunicipio','clave');
    }

    public function localidad(){
    	return $this->belongsTo('Localidad','claveLocalidad','clave');
    }

    public function scopeAgrupar($query){
    	$query->select('id','idFibap','idAccion','idObjetoGasto',DB::raw('sum(cantidad) AS cantidad'))->groupBy('idObjetoGasto');
    }

    public function scopeAgruparPorLocalidad($query){
    	$query->select('id','idFibap','idAccion','claveLocalidad','claveMunicipio','claveJurisdiccion',DB::raw('sum(cantidad) AS cantidad'))->groupBy('claveLocalidad');
    }
}