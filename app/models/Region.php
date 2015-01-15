<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Region extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "vistaRegiones";

	public function municipios(){
		return $this->hasMany('Municipio','idRegion');
	}

	public function scopeObtenerJurisdicciones($query,$region){
		$query->select('jurisdiccion.*')
				->leftjoin('vistaMunicipios AS municipio','municipio.idRegion','=','vistaRegiones.id')
				->leftjoin('vistaJurisdicciones AS jurisdiccion','jurisdiccion.id','=','municipio.idJurisdiccion')
				->where('vistaRegiones.region','=',$region)
				->groupby('jurisdiccion.clave');
	}
}