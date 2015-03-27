<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Proyecto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectos";
	protected $appends = array('ClavePresupuestaria');
	
	
	public static function boot(){
        parent::boot();

        static::updating(function($item){
        	if($item->idEstatusProyecto == 4 && $item->numeroProyectoEstrategico == 0){
        		$count = Proyecto::where('unidadResponsable',$item->unidadResponsable)
        					 ->where('finalidad',$item->finalidad)
        					 ->where('funcion',$item->funcion)
        					 ->where('subFuncion',$item->subFuncion)
        					 ->where('subSubFuncion',$item->subSubFuncion)
        					 ->where('programaSectorial',$item->programaSectorial)
        					 ->where('programaPresupuestario',$item->programaPresupuestario)
        					 ->where('programaEspecial',$item->programaEspecial)
        					 ->where('actividadInstitucional',$item->actividadInstitucional)
        					 ->where('proyectoEstrategico',$item->proyectoEstrategico)
        					 ->max('numeroProyectoEstrategico');
            	$item->numeroProyectoEstrategico = ($count + 1);
        	}
        });
    }
    

    public function fibap(){
        return $this->hasOne('FIBAP','idProyecto');
    }

	public function getClavePresupuestariaAttribute(){
		return $this->unidadResponsable . $this->finalidad . $this->funcion . $this->subfuncion . $this->subsubfuncion . $this->programaSectorial . $this->programaPresupuestario . $this->programaEspecial . $this->actividadInstitucional . $this->proyectoEstrategico . str_pad($this->numeroProyectoEstrategico, 3,'0',STR_PAD_LEFT);
	}

	public function getClaveFuncionAttribute(){
		return $this->finalidad . '.' . $this->funcion;
	}

	public function getClaveSubFuncionAttribute(){
		return $this->finalidad . '.' . $this->funcion . '.' . $this->subFuncion;
	}

	public function getClaveSubSubFuncionAttribute(){
		return $this->finalidad . '.' . $this->funcion . '.' . $this->subFuncion . '.' . $this->subSubFuncion;
	}

	public function scopeContenidoCompleto($query){
		return $query->with('componentes','beneficiarios','municipio','region','clasificacionProyecto','tipoProyecto','cobertura','tipoAccion',
			'datosUnidadResponsable','datosFinalidad','datosFuncion','datosSubFuncion','datosSubSubFuncion','datosProgramaSectorial',
			'datosProgramaPresupuestario','datosProgramaEspecial','datosActividadInstitucional','datosProyectoEstrategico',
			'objetivoPed','estatusProyecto','jefeInmediato','liderProyecto','jefePlaneacion','coordinadorGrupoEstrategico');
	}

	public function registroAvance(){
    	return $this->hasMany('RegistroAvanceMetas','idProyecto');
    }

    public function registroAvanceBeneficiarios(){
    	return $this->hasMany('RegistroAvanceBeneficiario','idProyecto');
    }

    public function analisisFuncional(){
    	return $this->hasMany('EvaluacionAnalisisFuncional','idProyecto');
    }

	public function jefeInmediato(){
		return $this->belongsTo('Titular','idJefeInmediato')->withTrashed();
	}

	public function liderProyecto(){
		return $this->belongsTo('Titular','idLiderProyecto')->withTrashed();
	}

	public function jefePlaneacion(){
		return $this->belongsTo('Titular','idJefePlaneacion')->withTrashed();
	}

	public function coordinadorGrupoEstrategico(){
		return $this->belongsTo('Titular','idCoordinadorGrupoEstrategico')->withTrashed();
	}

	public function componentes(){
		return $this->hasMany('Componente','idProyecto')->with('usuario');
	}

	public function actividades(){
		return $this->hasMany('Actividad','idProyecto')->with('usuario');
	}
	
	public function beneficiarios(){
		return $this->hasMany('Beneficiario','idProyecto')->with('tipoBeneficiario')->orderBy('id');
	}

	public function municipio(){
		return $this->belongsTo('Municipio','claveMunicipio','clave');
	}

	public function region(){
		return $this->belongsTo('Region','claveRegion','region');
	}

	public function clasificacionProyecto(){
		return $this->belongsTo('ClasificacionProyecto','idClasificacionProyecto');
	}

	public function tipoProyecto(){
		return $this->belongsTo('TipoProyecto','idTipoProyecto');
	}

	public function cobertura(){
		return $this->belongsTo('Cobertura','idCobertura');
	}

	public function tipoAccion(){
		return $this->belongsTo('TipoAccion','idTipoAccion');
	}

	public function datosUnidadResponsable(){
		return $this->belongsTo('UnidadResponsable','unidadResponsable','clave');
	}

	public function datosFinalidad(){
		return $this->belongsTo('FuncionGasto','finalidad','clave');
	}

	public function datosFuncion(){
		return $this->belongsTo('FuncionGasto','claveFuncion','clave');
	}

	public function datosSubFuncion(){
		return $this->belongsTo('FuncionGasto','claveSubFuncion','clave');
	}

	public function datosSubSubFuncion(){
		return $this->belongsTo('FuncionGasto','claveSubSubFuncion','clave');
	}

	public function datosProgramaSectorial(){
		return $this->belongsTo('ProgramaSectorial','programaSectorial','clave');
	}

	public function datosProgramaPresupuestario(){
		return $this->belongsTo('ProgramaPresupuestario','programaPresupuestario','clave');
	}

	public function datosProgramaEspecial(){
		return $this->belongsTo('ProgramaEspecial','programaEspecial','clave');
	}

	public function datosActividadInstitucional(){
		return $this->belongsTo('ActividadInstitucional','actividadInstitucional','clave');
	}

	public function datosProyectoEstrategico(){
		return $this->belongsTo('ProyectoEstrategico','proyectoEstrategico','clave');
	}

	public function objetivoPed(){
		return $this->belongsTo('ObjetivoPED','idObjetivoPED');
	}

	public function objetivoPedCompleto(){
		return $this->belongsTo('ObjetivoPED','idObjetivoPED')->with('padre');
	}	

	/*public function tipoBeneficiario(){
		return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
	}*/

	public function estatusProyecto(){
		return $this->belongsTo('EstatusProyecto','idEstatusProyecto');
	}
	
	public function comentarios(){
		return $this->hasMany('ProyectoComentario','idProyecto');
	}
	
	public function evaluacionMeses(){
		return $this->hasMany('EvaluacionProyectoMes','idProyecto');
	}
}