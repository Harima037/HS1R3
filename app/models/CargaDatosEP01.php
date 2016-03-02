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
				->where('cargaDatosEP01.ejercicio','=',$anio)
				->groupBy('cargaDatosEP01.PP');
	}

	public function scopeReporteProyectosEP01($query,$mes,$anio){
		$query->select(DB::raw('concat(UR,FI,FU,SF,SSF,PS,PP,PE,AI,PT) AS clavePresupuestaria'), 
				DB::RAW('SUM(cargaDatosEP01.presupuestoAprobado) AS presupuestoAprobado'),
				DB::RAW('SUM(cargaDatosEP01.presupuestoModificado) AS presupuestoModificado'),
				DB::RAW('SUM(cargaDatosEP01.presupuestoDevengadoModificado) AS presupuestoDevengadoModificado'),
				DB::RAW('SUM(cargaDatosEP01.presupuestoEjercidoModificado) AS presupuestoEjercidoModificado'),
				'fuente.descripcion AS fuenteFinanciamiento','subfuente.descripcion AS subFuenteFinanciamiento',
				'fuente.clave AS claveFuenteFinanciamiento','subfuente.clave AS claveSubFuenteFinanciamiento',
				'tipoRecurso.clave AS claveTipoRecurso','tipoRecurso.descripcion AS tipoRecurso')
				->groupBy(DB::raw('concat(UR,FI,FU,SF,SSF,PS,PP,PE,AI,PT,FF,SFF)'))
				->leftJoin('catalogoFuenteFinanciamiento AS fuente',function($join){
					$join->on('fuente.clave','=','FF')->whereNull('fuente.borradoAl');
				})
				->leftJoin('catalogoSubFuenteFinanciamiento AS subfuente',function($join){
					$join->on('subfuente.clave','=','SFF')->whereNull('subfuente.borradoAl');
				})
				->leftJoin('catalogoFuenteFinanciamiento AS tipoRecurso',function($join){
					$join->on('tipoRecurso.clave','=','fuente.fuente')->whereNull('tipoRecurso.borradoAl');;
				})
				->where('cargaDatosEP01.mes','=',$mes)
				->where('cargaDatosEP01.ejercicio','=',$anio);
	}
}