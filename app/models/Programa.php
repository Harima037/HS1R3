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

	public function arbolProblema(){
		return $this->hasMany('ProgramaArbolProblema','idPrograma');
	}

	public function arbolObjetivo(){
		return $this->hasMany('ProgramaArbolObjetivo','idPrograma');
	}

	public function indicadores(){
		return $this->hasMany('ProgramaIndicador','idPrograma');
	}

	public function registroAvance(){
		return $this->hasMany('RegistroAvancePrograma','idPrograma');
	}

	public function evaluacionTrimestre(){
		return $this->hasMany('EvaluacionProgramaTrimestre','idPrograma');
	}
}