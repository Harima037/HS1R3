<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Municipio extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "vistaMunicipios";

	public function localidades(){
		return $this->hasMany('Localidad','idMunicipio');
	}

	public function jurisdiccion(){
		return $this->belongsTo('Jurisdiccion','idJurisdiccion');
	}

	public function scopeObtenerJurisdicciones($query,$municipio){
		$query->select('jurisdiccion.*')
				->leftjoin('vistaJurisdicciones AS jurisdiccion','jurisdiccion.id','=','vistaMunicipios.idJurisdiccion')
				->where('vistaMunicipios.clave','=',$municipio);
	}
}