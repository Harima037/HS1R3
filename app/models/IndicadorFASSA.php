<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class IndicadorFASSA extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "indicadorFASSA";

	public function metas(){
		return $this->hasMany('IndicadorFASSAMeta','idIndicadorFASSA');
	}

	public function metasDetalle(){
		return $this->hasMany('IndicadorFASSAMeta','idIndicadorFASSA')->conDetalle();
	}
}