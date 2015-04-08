<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProgramaIndicador extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "programaIndicador";

	public function registroAvance(){
		return $this->hasMany('RegistroAvancePrograma','idIndicador');
	}
}