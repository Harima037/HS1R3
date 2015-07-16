<?php
class CargaDatosEPRegion extends BaseModel
{
	protected $table = "cargaDatosEPRegion";
	public $timestamps = false;

	public function scopeListarDatosReporte($query){
		return $query->select('cargaDatosEPREgion.*',DB::raw('concat(UR,FI,FU,SF,SSF,PS,PP,PE,AI,PT,MPIO,OG,STG,FF,SFF,DG,CP,DM) AS clavePresupuestaria'));
	}
	
	
	public function scopeReporteEstatal($query, $mes){
		$query->select('cargaDatosEPRegion.PP', DB::RAW('SUM(cargaDatosEPRegion.importe) AS ImporteEstatal'))
				->where('cargaDatosEPRegion.mes','=',$mes)
				->where('cargaDatosEPRegion.MPIO','=','000')				
				->groupBy('cargaDatosEPRegion.PP');
	}
	
	public function scopeReporteRegional($query, $mes, $region){
		$query = 'SELECT cargaDatosEPRegion.PP,SUM(cargaDatosEPRegion.importe) AS importe FROM cargaDatosEPRegion WHERE mes = "'.$mes.'" AND (MPIO IN (SELECT clave FROM vistaMunicipios WHERE idRegion="'.$region.'") OR MPIO IN (SELECT claveMunicipio FROM catalogoRegionalizado WHERE idRegion="'.$region.'")) GROUP BY PP';
		return DB::select(DB::raw($query));
	}
	
	
	
	
	
 
	
	
	
}