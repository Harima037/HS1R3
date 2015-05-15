<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class SysConfiguracionVariable extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "sysConfiguracionVariables";

	public static function obtenerVariables($variables = NULL){
		if($variables){
			$rows = self::whereIn('variable',$variables);
		}else{
			$rows = self::all();
		}

		$rows = $rows->get();

		return $rows;
	}

}