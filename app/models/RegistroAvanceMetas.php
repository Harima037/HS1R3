<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RegistroAvanceMetas extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "registroAvancesMetas";

	/*
	public function scopeAgrupadoMes($query){
		$query->select('id','idProyecto','idActividad','mes',DB::raw('sum(meta) AS meta'))
				->groupBy('idActividad','mes');
	}
	public function scopeAgrupadoJurisdiccion($query){
		$query->select('id','idProyecto','idActividad','claveJurisdiccion',DB::raw('sum(meta) AS meta'))
				->groupBy('claveJurisdiccion');
	}
	*/
}