<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionEstrategiaTrimestre extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionEstrategiaTrimestre";
	
	public function estrategia(){
		return $this->hasMany('Estrategia','id','idEstrategia');
	}
}