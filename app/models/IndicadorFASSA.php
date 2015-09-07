<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class IndicadorFASSA extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "indicadorFASSA";
	
	public function scopeContenidoSuggester($query){
		$query->select('indicadorFASSA.id','indicadorFASSA.claveNivel','indicadorFASSA.indicador','indicadorFASSA.idEstatus',
				'estatus.descripcion AS estatus','indicadorFASSA.idUsuarioValidacionSeg','indicadorFASSA.idUsuarioRendCuenta'
			)
			->leftjoin('catalogoEstatusProyectos AS estatus','estatus.id','=','indicadorFASSA.idEstatus');
	}
	
	public function metas(){
		return $this->hasMany('IndicadorFASSAMeta','idIndicadorFASSA');
	}

	public function metasDetalle(){
		return $this->hasMany('IndicadorFASSAMeta','idIndicadorFASSA')->conDetalle();
	}
}