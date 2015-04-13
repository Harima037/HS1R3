<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Programa extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "programa";

	public function scopeContenidoDetalle($query){
		$query->select('programa.*','programaPresupuestal.descripcion as programaPresupuestario','unidadResponsable.descripcion AS unidadResponsable','ODM.clave AS claveODM','ODM.descripcion AS ODM','Modalidad.clave AS claveModalidad', 'Modalidad.descripcion AS Modalidad', 'Sectorial.clave AS claveSectorial', 'Sectorial.descripcion AS sectorial', 'objetivosPED.descripcion AS objetivoPED', 'objetivosPND.descripcion AS objetivoPND')
		      ->join('catalogoProgramasPresupuestales AS programaPresupuestal','programaPresupuestal.clave','=','programa.claveProgramaPresupuestario')
			  ->join('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','programa.claveUnidadResponsable')
			  ->join('catalogoODM as ODM','ODM.id','=','programa.idOdm')
			  ->join('catalogoModalidad as Modalidad','Modalidad.id','=','programa.idModalidad')
			  ->join('catalogoProgramasSectoriales as Sectorial','Sectorial.clave','=','programa.claveProgramaSectorial')
			  ->join('catalogoObjetivosPED as objetivosPED','objetivosPED.id','=','programa.idObjetivoPED')
			  ->join('catalogoObjetivosPND as objetivosPND','objetivosPND.id','=','programa.idObjetivoPND');
	}

	public function programaPresupuestario(){
		return $this->belongsTo('ProgramaPresupuestario','claveProgramaPresupuestario','clave');
	}

	public function arbolProblema(){
		return $this->hasMany('ProgramaArbolProblema','idPrograma');
	}

	public function arbolObjetivo(){
		return $this->hasMany('ProgramaArbolObjetivo','idPrograma');
	}

	public function indicadores(){
		return $this->hasMany('ProgramaIndicador','idPrograma');
	}

	public function registroAvance(){
		return $this->hasMany('RegistroAvancePrograma','idPrograma');
	}

	public function evaluacionTrimestre(){
		return $this->hasMany('EvaluacionProgramaTrimestre','idPrograma');
	}
	
	public function comentario(){
		return $this->hasMany('ProgramaComentario','idPrograma');
	}
}