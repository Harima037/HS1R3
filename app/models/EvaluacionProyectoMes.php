<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionProyectoMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionProyectoMes";
	
	public function proyectos(){
		return $this->hasMany('Proyecto','id','idProyecto');
	}
}