<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ActividadDesgloseBeneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "actividadDesgloseBeneficiarios";

	public function tipoBeneficiario(){
		return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
	}

	public function scopeConDescripcion($query){
		return $query->select('actividadDesgloseBeneficiarios.*','tipoBeneficiario.descripcion AS tipoBeneficiario','tipoBeneficiario.grupo as grupo','tipoCaptura.descripcion AS tipoCaptura')
				->leftJoin('catalogoTiposBeneficiarios AS tipoBeneficiario','tipoBeneficiario.id','=','actividadDesgloseBeneficiarios.idTipoBeneficiario')
				->leftJoin('componenteActividades AS actividades','actividades.id','=','actividadDesgloseBeneficiarios.idActividad')
				->leftJoin('proyectoBeneficiarios AS beneficiarios',function($join){
																		$join->on('beneficiarios.idProyecto','=','actividades.idProyecto')
																			->on('beneficiarios.idTipoBeneficiario','=','actividadDesgloseBeneficiarios.idTipoBeneficiario')
																			->whereNull('beneficiarios.borradoAl');
																	})
				->leftJoin('catalogoTiposCaptura AS tipoCaptura','tipoCaptura.id','=','beneficiarios.idTipoCaptura');
	}
}