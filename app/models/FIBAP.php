<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FIBAP extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibap";

	public function proyecto(){
        return $this->belongsTo('Proyecto','idProyecto');
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
        return $this->hasMany('PropuestaFinanciamiento','idFibap');
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
}