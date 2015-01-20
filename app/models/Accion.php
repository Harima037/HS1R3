<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Accion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibapAcciones";

    public function partidas(){
        return $this->belongsToMany('ObjetoGasto','relAccionesPartidas','idAccion','idObjetoGasto');
    }

	public function componente(){
        return $this->belongsTo('Componente','idComponente');
    }

    public function desgloceComponente(){
        return $this->hasOne('ComponenteDesgloce','idAccion');
    }

    public function datosComponente(){
    	return $this->hasOne('FibapDatosComponente','idAccion');
    }

	public function propuestasFinanciamiento(){
		return $this->hasMany('PropuestaFinanciamiento','idAccion');
	}

	public function distribucionPresupuesto(){
        return $this->hasMany('DistribucionPresupuesto','idAccion');
    }

    public function distribucionPresupuestoAgrupado(){
        return $this->hasMany('DistribucionPresupuesto','idAccion')->agruparPorLocalidad();
    }
}
