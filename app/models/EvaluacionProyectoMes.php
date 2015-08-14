<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionProyectoMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionProyectoMes";

	/**
    * Set the update and creation timestamps on the model.
    */

    public static function boot(){
        parent::boot();
        
        EvaluacionProyectoMes::updated(function($seguimiento_mes){
        	if($seguimiento_mes->idEstatus != 1){
        		$bitacora = new BitacoraValidacionSeguimiento;
				$bitacora->idUsuario 	= Sentry::getUser()->id;
				$bitacora->idProyecto 	= $seguimiento_mes->idProyecto;
				$bitacora->idEstatus 	= $seguimiento_mes->idEstatus;
				$bitacora->mes 			= $seguimiento_mes->mes;
				$bitacora->ejercicio 	= $seguimiento_mes->anio;
				$bitacora->save();
        	}
        });
    }
	
	public function proyectos(){
		return $this->hasMany('Proyecto','id','idProyecto');
	}
}