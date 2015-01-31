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
    	return $this->belongsTo('Localidad','claveLocalidad','clave')->where('idMunicipio',$this->municipio->id);
    }

    public function scopeAgrupar($query){
    	$query->select('id','idFibap','idAccion','idObjetoGasto',DB::raw('sum(cantidad) AS cantidad'))->groupBy('idObjetoGasto');
    }

    public function scopeAgruparPorLocalidad($query){
    	$query->select('fibapDistribucionPresupuesto.id','idFibap','idAccion','localidad.nombre AS localidad','municipio.nombre AS municipio','claveJurisdiccion',
                DB::raw('sum(cantidad) AS cantidad'))
                ->groupBy('claveJurisdiccion','claveMunicipio','claveLocalidad')
                ->leftjoin('vistaMunicipios AS municipio','municipio.clave','=','claveMunicipio')
                ->leftjoin('vistaLocalidades AS localidad',function($join){
                    return $join->on('localidad.clave','=','claveLocalidad')
                         ->on('localidad.idMunicipio','=','municipio.id');
                });
    }
}