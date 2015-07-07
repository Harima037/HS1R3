<?php
class CargaDatosEPRegion extends BaseModel
{
	protected $table = "cargaDatosEPRegion";
	public $timestamps = false;

	public function scopeListarDatosReporte($query){
		return $query->select('cargaDatosEPREgion.*',DB::raw('concat(UR,FI,FU,SF,SSF,PS,PP,PE,AI,PT,MPIO,OG,STG,FF,SFF,DG,CP,DM) AS clavePresupuestaria'));
	}
}