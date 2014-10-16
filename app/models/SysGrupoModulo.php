<?php
class SysGrupoModulo extends Eloquent
{
	protected $table = "sysGruposModulos";
	
	public function modulos(){
		return $this->hasMany("SysModulo",'idSysGrupoModulo');		
	}

	public static function getPermisoBase($uri){
		$rows = DB::table('sysModulos AS modulo')
				->leftjoin('sysGruposModulos AS sistema','modulo.idSysGrupoModulo','=','sistema.id')
				->select(DB::raw('concat_ws(".",sistema.key,modulo.key) AS llave'))
				->where('modulo.uri','=',$uri)
				->get();
		if(count($rows) > 0){
			return $rows[0]->llave;
		}else{
			return false;
		}
	}

	//Devuelve un Array generado de las tablas de sistemas y modulos, con los permisos del CRUD, --Catalogo de Permisos
	public static function getPermisos($id = NULL){
		$rows = DB::table('sysGruposModulos AS sistema')
				->leftjoin('sysModulos AS modulo','modulo.idSysGrupoModulo','=','sistema.id')
				->leftjoin('sysPermisos AS permiso','permiso.id','=','modulo.idSysPermiso')
				->select(DB::raw('concat_ws(".",sistema.key,modulo.key) AS llave'),'permiso.permisos');
		if($id !== NULL){
			$rows = $rows->where('sistema.id','=',$id);
		}
		$rows = $rows->get();
		
		$permisos = array();
		foreach ($rows as $row) {
			$plantilla_permisos = explode('|', $row->permisos);
			foreach ($plantilla_permisos as $value) {
				$permisos[] = $row->llave . '.' . $value;
			}
		}
		return $permisos;
	}

	public static function findByKey($_key){
		return self::where('key','=',$_key)->firstOrFail();
		/*
		$sistemas = self::all();
		foreach($sistemas as $sistema){
			if($sistema->key==$_key)
				return $sistema;
		}
		return false;*/
	}
	
}	
?>