<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ActividadMetaMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "actividadMetasMes";

	public function scopeAgrupadoMes($query){
		$query->select('id','idProyecto','idActividad','mes',DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))
				->groupBy('idActividad','mes');
	}

	public function scopeAgrupadoJurisdiccion($query){
		$query->select('id','idProyecto','idActividad','claveJurisdiccion',DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'))
				->groupBy('claveJurisdiccion');
	}

}