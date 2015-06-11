<?php
class CargaDatosEP01 extends BaseModel
{
	protected $table = "cargaDatosEP01";
	public $timestamps = false;

	public function scopeListarDatosReporte($query){
		return $query->select('cargaDatosEP01.*',DB::raw('concat(UR,FI,FU,SF,SSF,PS,PP,PE,AI,PT,MPIO,OG,STG,FF,SFF,DG,CP,DM) AS clavePresupuestaria'));
	}
}