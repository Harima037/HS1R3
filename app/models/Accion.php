<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Accion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibapAcciones";

    public function fibap(){
        return $this->belongsTo('FIBAP','idFibap');
    }

    public function partidas(){
        return $this->belongsToMany('ObjetoGasto','relAccionesPartidas','idAccion','idObjetoGasto');
    }

	public function componente(){
        return $this->belongsTo('Componente','idComponente');
    }

    public function datosComponente(){
    	return $this->hasOne('FibapDatosComponente','idAccion');
    }

    public function datosComponenteListado(){
        return $this->hasOne('FibapDatosComponente','idAccion')->mostrarDatos();
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
