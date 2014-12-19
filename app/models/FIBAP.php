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

    public function datosProyecto(){
        return $this->hasOne('FibapDatosProyecto','idFibap');
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
}