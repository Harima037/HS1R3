<?php
class CargaDatosEP01 extends BaseModel
{
	protected $table = "cargaDatosEP01";
	public $timestamps = false;

	public function scopeListarDatosReporte($query){
		return $query->select('cargaDatosEP01.*',DB::raw('concat(UR,FI,FU,SF,SSF,PS,PP,PE,AI,PT,MPIO,OG,STG,FF,SFF,DG,CP,DM) AS clavePresupuestaria'));
	}
	
	public function scopeReporteRegionalizado($query, $mes, $anio){
		$query->select('cargaDatosEP01.PP', 'catalogoProgramasPresupuestales.descripcion AS nombrePrograma', 
				DB::RAW('SUM(cargaDatosEP01.presupuestoDevengadoModificado) AS PresDevengado'),
				DB::RAW('SUM(cargaDatosEP01.presupuestoAprobado) AS PresAprobado'),
				DB::RAW('SUM(cargaDatosEP01.presupuestoModificado) AS PresModificado'))
				->leftJoin('catalogoProgramasPresupuestales','catalogoProgramasPresupuestales.clave','=','cargaDatosEP01.PP')
				->where('cargaDatosEP01.mes','=',$mes)
				->where('cargaDatosEP01.CP','=',$anio)
				->groupBy('cargaDatosEP01.PP');
	}			
}