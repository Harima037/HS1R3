<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FIBAP extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibap";

    public function scopeContenidoCompleto($query){
        return $query->with('documentos','propuestasFinanciamiento','antecedentesFinancieros','distribucionPresupuestoAgrupado','acciones');
    }

    public function scopeCedulasValidacion($query){
        return $query->select('fibap.*','proyecto.nombreTecnico','programa_presup.descripcion AS programaPresupuestario','proyecto.idCobertura','tipoDeProyecto.descripcion AS tipoProyecto',
            DB::raw('concat(proyecto.unidadResponsable,proyecto.finalidad,proyecto.funcion,proyecto.subfuncion,proyecto.subsubfuncion,proyecto.programaSectorial,proyecto.programaPresupuestario,proyecto.programaEspecial,proyecto.actividadInstitucional,proyecto.proyectoEstrategico,LPAD(proyecto.numeroProyectoEstrategico,3,"0")) as clavePresup')
                        ,'coberturas.descripcion AS cobertura')
            ->leftjoin('proyectos AS proyecto','proyecto.id','=','fibap.idProyecto')
            ->leftjoin('catalogoProgramasPresupuestales AS programa_presup','proyecto.programaPresupuestario','=','programa_presup.clave')
            ->leftjoin('catalogoTiposProyectos AS tipoDeProyecto','tipoDeProyecto.id','=','proyecto.idTipoProyecto')
            ->leftjoin('catalogoCoberturas AS coberturas','coberturas.id','=','proyecto.idCobertura')
            ->with('accionesCompletas','beneficiarios','propuestasFinanciamientoCompleto','distribucionPresupuestoMesCompleto');
    }

	public function proyecto(){
        return $this->belongsTo('Proyecto','idProyecto');
    }

    public function beneficiarios(){
        return $this->hasMany('Beneficiario','idProyecto','idProyecto')->with('tipoBeneficiario');
    }

    public function accionesCompletas(){
        return $this->hasMany('Accion','idFibap')->contenidoCompleto();
    }

    public function acciones(){
        return $this->hasMany('Accion','idFibap');
    }

    public function accionesCompletasDescripcion(){
        return $this->hasMany('Accion','idFibap')->completoConDescripcion()
                ->with('propuestasFinanciamiento','distribucionPresupustoPartidaDescripcion'); //'desglosePresupuestoCompleto'
    }

    public function proyectoCompleto(){
        return $this->belongsTo('Proyecto','idProyecto')->contenidoCompleto();
    }

    public function datosProyecto(){
        return $this->hasOne('FibapDatosProyecto','idFibap');
    }

    public function datosProyectoCompleto(){
        return $this->hasOne('FibapDatosProyecto','idFibap')->contenidoCompleto();
    }

    public function documentos(){
    	return $this->belongsToMany('DocumentoSoporte','relFibapDocumentoSoporte','idFibap','idDocumentoSoporte');
    }

    public function propuestasFinanciamiento(){
        return $this->hasMany('PropuestaFinanciamiento','idFibap')->agrupadoPorFibap();
    }

    public function propuestasFinanciamientoCompleto(){
        return $this->hasMany('PropuestaFinanciamiento','idFibap')->agrupadoPorFibapCompleto();
    }

    public function antecedentesFinancieros(){
        return $this->hasMany('AntecedenteFinanciero','idFibap');
    }

    public function distribucionPresupuesto(){
        return $this->hasMany('DistribucionPresupuesto','idFibap');
    }

    public function distribucionPresupuestoAgrupado(){
        return $this->hasMany('DistribucionPresupuesto','idFibap')->agrupar();
    }

    public function distribucionPresupuestoMesCompleto(){
        return $this->hasMany('DistribucionPresupuesto','idFibap')->agruparMesCompleto();
    }
}