<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionProgramaTrimestre extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionProgramaTrimestre";
	
	public function programas(){
		return $this->hasMany('Programa','id','idPrograma');
	}
}