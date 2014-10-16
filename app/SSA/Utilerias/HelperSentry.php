<?php
namespace SSA\Utilerias;

class HelperSentry{
	public static function GetJsonPermisos($PermisosArray){
		$permisos = array('Children'=>array());
		foreach ($PermisosArray as $key => $value) {
			$key_data = explode('.', $key);
			$next_array = &$permisos;
			foreach ($key_data as $item) {
				if($item == 'C' || $item == 'R' || $item == 'U' || $item == 'D' ){
					$next_array['Permissions'][$item] = $value;
				}else{
					if(!isset($next_array['Children'][$item])){
						$next_array['Children'][$item] = array(
							'Permissions'=>array('C'=>0,'R'=>0,'U'=>0,'D'=>0),'Children'=>array()
						);
					}
					$next_array = &$next_array['Children'][$item];
				}
			}
		}
		return $permisos['Children'];
	}
}