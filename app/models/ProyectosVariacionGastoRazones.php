<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProyectosVariacionGastoRazones extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectosVariacionGastoRazones";
	
	public function scopeUltimaRazon($query, $idproyecto)
	{
		$query -> select('proyectosVariacionGastoRazones.razonesAprobado','proyectosVariacionGastoRazones.razonesDevengado')
				->where('proyectosVariacionGastoRazones.idProyecto','=',$idproyecto)
				->orderBy('proyectosVariacionGastoRazones.mes', 'desc')
				->take(1);
	}
	
	public function scopeHallaRazonPorMes($query, $idproyecto, $mes)
	{
		$query -> select('proyectosVariacionGastoRazones.id')
				->where('proyectosVariacionGastoRazones.idProyecto','=',$idproyecto)
				->where('proyectosVariacionGastoRazones.mes','=',$mes)
				->take(1);
	}
	
}