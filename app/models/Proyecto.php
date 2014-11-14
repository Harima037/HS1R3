<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Proyecto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectos";

	public function getClavePresupuestariaAttribute(){
		return $this->unidadResponsable . $this->finalidad . $this->funcion . $this->subfuncion . $this->subsubfuncion . $this->programaSectorial . $this->programaPresupuestario . $this->programaEspecial . $this->actividadInstitucional . $this->proyectoEstrategico . $this->numeroProyectoEstrategico;
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
		return $query->with('beneficiarios','clasificacionProyecto','tipoProyecto','cobertura','tipoAccion','datosUnidadResponsable',
			'datosFinalidad','datosFuncion','datosSubFuncion','datosSubSubFuncion','datosProgramaSectorial','datosProgramaPresupuestario',
			'datosProgramaEspecial','datosActividadInstitucional','datosProyectoEstrategico','objetivoPed','tipoBeneficiario',
			'estatusProyecto');
	}

	public function beneficiarios(){
		return $this->hasMany('Beneficiario','idProyecto');
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

	public function tipoBeneficiario(){
		return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
	}

	public function estatusProyecto(){
		return $this->belongsTo('EstatusProyecto','idEstatusProyecto');
	}
}