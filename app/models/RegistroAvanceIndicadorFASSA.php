<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RegistroAvanceIndicadorFASSA extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "registroAvancesIndicadorFASSA";

	public function scopeConDetalle($query){
		return $query->select('registroAvancesIndicadorFASSA.*','estatus.descripcion AS estatus')
					->leftjoin('catalogoEstatusProyectos AS estatus','estatus.id','=','registroAvancesIndicadorFASSA.idEstatus');
	}
}