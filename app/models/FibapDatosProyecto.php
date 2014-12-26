<?php
class FibapDatosProyecto extends BaseModel
{
	protected $table = "fibapDatosProyecto";

	public function fibap(){
        return $this->belongsTo('FIBAP','idFibap');
    }

    public function scopeContenidoCompleto($query){
        return $query->with('municipio','region','clasificacionProyecto','tipoProyecto','cobertura','datosProgramaPresupuestario',
                'objetivoPed','tipoBeneficiario');
    }

    public function tipoProyecto(){
        return $this->belongsTo('TipoProyecto','idTipoProyecto');
    }

    public function clasificacionProyecto(){
        return $this->belongsTo('ClasificacionProyecto','idClasificacionProyecto');
    }

    public function datosProgramaPresupuestario(){
        return $this->belongsTo('ProgramaPresupuestario','programaPresupuestario','clave');
    }

    public function cobertura(){
        return $this->belongsTo('Cobertura','idCobertura');
    }

    public function municipio(){
        return $this->belongsTo('Municipio','claveMunicipio','clave');
    }

    public function region(){
        return $this->belongsTo('Region','claveRegion','region');
    }

    public function objetivoPed(){
        return $this->belongsTo('ObjetivoPED','idObjetivoPED');
    }

    public function tipoBeneficiario(){
        return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
    }
}