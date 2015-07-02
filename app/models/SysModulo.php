<?php
class SysModulo extends Eloquent
{
	protected $table = "sysModulos";
	public $timestamps = false;
	
	public function sistema(){
		return $this->belongsTo("SysGrupoModulo",'idSysGrupoModulo');		
	}
	public static function findByKey($_key){
		$modulos = self::all();
		foreach($modulos as $modulo){
			if($modulo->key==$_key)
				return $modulo;
		}
		return false;
	}
}	
?>