<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Accion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibapAcciones";

    public function scopeContenidoCompleto($query){
        if($this->idComponente){
            return $query->with('desglosePresupuesto.metasMes','desglosePresupuesto.beneficiarios.tipoBeneficiario')
                    ->select('fibapAcciones.*','componente.*','unidadesMedida.descripcion AS unidadMedida','entregables.descripcion AS entregable'
                        ,'entregablesAcciones.descripcion AS entregableAccion','entregablesTipos.descripcion AS entregableTipo')
                    ->leftjoin('proyectoComponentes AS componente','componente.id','=','fibapAcciones.idComponente')
                    ->leftjoin('catalogoUnidadesMedida AS unidadesMedida','unidadesMedida.id','=','componente.idUnidadMedida')
                    ->leftjoin('catalogoEntregables AS entregables','entregables.id','=','componente.idEntregable')
                    ->leftjoin('catalogoEntregablesAcciones As entregablesAcciones','entregablesAcciones.id','=','componente.idEntregableAccion')
                    ->leftjoin('catalogoEntregablesTipos AS entregablesTipos','entregablesTipos.id','=','componente.idEntregableTipo');
        }else{
            return $query->with('desglosePresupuesto.metasMes','desglosePresupuesto.beneficiarios.tipoBeneficiario')
                    ->select('fibapAcciones.*','actividad.*','unidadesMedida.descripcion AS unidadMedida')
                    ->leftjoin('componenteActividades AS actividad','actividad.id','=','fibapAcciones.idActividad')
                    ->leftjoin('catalogoUnidadesMedida AS unidadesMedida','unidadesMedida.id','=','actividad.idUnidadMedida');
        }
    }

    public function scopeCompletoConDescripcion($query){
        if($this->idComponente){
            return $query->select('fibapAcciones.*',
                        'componente.objetivo','componente.indicador','componente.idUnidadMedida','componente.idEntregable','componente.idEntregableTipo',
                        'componente.idEntregableAccion','componente.numeroTrim1','componente.numeroTrim2','componente.numeroTrim3','componente.numeroTrim4',
                        'componente.valorNumerador',
                        'unidadesMedida.descripcion AS unidadMedida','entregables.descripcion AS entregable'
                        ,'entregablesAcciones.descripcion AS entregableAccion','entregablesTipos.descripcion AS entregableTipo')
                    ->leftjoin('proyectoComponentes AS componente','componente.id','=','fibapAcciones.idComponente')
                    ->leftjoin('catalogoUnidadesMedida AS unidadesMedida','unidadesMedida.id','=','componente.idUnidadMedida')
                    ->leftjoin('catalogoEntregables AS entregables','entregables.id','=','componente.idEntregable')
                    ->leftjoin('catalogoEntregablesAcciones As entregablesAcciones','entregablesAcciones.id','=','componente.idEntregableAccion')
                    ->leftjoin('catalogoEntregablesTipos AS entregablesTipos','entregablesTipos.id','=','componente.idEntregableTipo');
        }else{
            return $query->select('fibapAcciones.*','actividad.objetivo','actividad.indicador','actividad.idUnidadMedida',
                        'actividad.numeroTrim1','actividad.numeroTrim2','actividad.numeroTrim3','actividad.numeroTrim4',
                        'actividad.valorNumerador','unidadesMedida.descripcion AS unidadMedida')
                    ->leftjoin('componenteActividades AS actividad','actividad.id','=','fibapAcciones.idActividad')
                    ->leftjoin('catalogoUnidadesMedida AS unidadesMedida','unidadesMedida.id','=','componente.idUnidadMedida');
        }
    }

    public function fibap(){
        return $this->belongsTo('FIBAP','idFibap');
    }

    public function partidas(){
        return $this->belongsToMany('ObjetoGasto','relAccionesPartidas','idAccion','idObjetoGasto');
    }
    
    /*
    public function datosComponente(){
    	return $this->hasOne('FibapDatosComponente','idAccion');
    }
    public function datosComponenteListado(){
        return $this->hasOne('FibapDatosComponente','idAccion')->mostrarDatos();
    }
    */
    
    public function componente(){
        return $this->belongsTo('Componente','idComponente');
    }
    public function actividad(){
        return $this->belongsTo('Actividad','idActividad');
    }
    
    //Estas dos parar listar varias acciones
    public function datosComponenteDetalle(){
        return $this->belongsTo('Componente','idComponente')->mostrarDatos();
    }
    public function datosActividadDetalle(){
        return $this->belongsTo('Actividad','idActividad')->mostrarDatos();
    }
    
	public function propuestasFinanciamiento(){
		return $this->hasMany('PropuestaFinanciamiento','idAccion');
	}

	public function distribucionPresupuesto(){
        return $this->hasMany('DistribucionPresupuesto','idAccion');
    }

    public function distribucionPresupuestoPartidaDescripcion(){
        return $this->hasMany('DistribucionPresupuesto','idAccion')->agruparMesCompleto();
    }

    public function distribucionPresupuestoAgrupado(){
        return $this->hasMany('DistribucionPresupuesto','idAccion')->agruparPorLocalidad();
    }

    public function desgloseComponente(){
        return $this->hasMany('ComponenteDesglose','idComponente','idComponente');
    }
    public function desgloseActividad(){
        return $this->hasMany('ActividadDesglose','idActividad','idActividad');
    }

    public function desglosePresupuestoComponente(){
        return $this->hasMany('ComponenteDesglose','idComponente','idComponente')->listarDatos();
    }
    public function desglosePresupuestoActividad(){
        return $this->hasMany('ActividadDesglose','idActividad','idActividad')->listarDatos();
    }

    public function desglosePresupuestoCompletoComponente(){
        return $this->hasMany('ComponenteDesglose','idComponente','idComponente')->listarDatos()->with('metasMes','beneficiariosDescripcion');
    }
    
    public function desglosePresupuestoCompletoActividad(){
        return $this->hasMany('ActividadDesglose','idActividad','idActividad')->listarDatos()->with('metasMes','beneficiariosDescripcion');
    }
}
