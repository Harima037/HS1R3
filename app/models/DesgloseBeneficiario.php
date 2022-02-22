<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DesgloseBeneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "desgloseBeneficiarios";

	public function tipoBeneficiario(){
		return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
	}

	public function scopeConDescripcion($query){
		return $query->select('desgloseBeneficiarios.*','tipoBeneficiario.descripcion AS tipoBeneficiario','tipoBeneficiario.grupo as grupo','tipoCaptura.descripcion AS tipoCaptura')
				->leftJoin('catalogoTiposBeneficiarios AS tipoBeneficiario','tipoBeneficiario.id','=','desgloseBeneficiarios.idTipoBeneficiario')
				->leftJoin('proyectoComponentes AS componentes','componentes.id','=','desgloseBeneficiarios.idComponente')
				->leftJoin('proyectoBeneficiarios AS beneficiarios',function($join){
																		$join->on('beneficiarios.idProyecto','=','componentes.idProyecto')
																			->on('beneficiarios.idTipoBeneficiario','=','desgloseBeneficiarios.idTipoBeneficiario')
																			->whereNull('beneficiarios.borradoAl');
																	})
				->leftJoin('catalogoTiposCaptura AS tipoCaptura','tipoCaptura.id','=','beneficiarios.idTipoCaptura');
	}
}