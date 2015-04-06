<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Programa extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "programa";

	public function programaPresupuestario(){
		return $this->belongsTo('ProgramaPresupuestario','claveProgramaPresupuestario','clave');
	}
}