<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ComponenteMetaMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteMetasMes";

	public function scopeAgrupadoMes($query){
		$query->select('id','idProyecto','idComponente','mes',DB::raw('sum(meta) AS meta'))
				->groupBy('idComponente','mes');
	}

	public function scopeAgrupadoJurisdiccion($query){
		$query->select('id','idProyecto','idComponente','claveJurisdiccion',DB::raw('sum(meta) AS meta'))
				->groupBy('idComponente','claveJurisdiccion');
	}
}