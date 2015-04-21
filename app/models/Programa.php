<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Programa extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "programa";

	public function scopeContenidoDetalle($query){
		$query->select('programa.*',
			DB::raw('concat_ws(" ",programaPresupuestal.clave,programaPresupuestal.descripcion) as programaPresupuestarioDescripcion'),
			DB::raw('concat_ws(" ",programa.claveUnidadResponsable,unidadResponsable.descripcion) AS unidadResponsable'),
			DB::raw('concat_ws(" ",ODM.clave,ODM.descripcion) AS ODM'),
			'modalidad.clave AS claveModalidad', 'modalidad.descripcion AS modalidad', 
			DB::raw('concat_ws(" ",sectorial.clave,sectorial.descripcion) AS programaSectorial'),
			DB::raw('concat_ws(" ",objetivosPED.clave,objetivosPED.descripcion) AS objetivoPED'),

			DB::raw('concat_ws(" ",eje.clave,eje.descripcion) AS eje'),
			DB::raw('concat_ws(" ",tema.clave,tema.descripcion) AS tema'),
			DB::raw('concat_ws(" ",politicaPublica.clave,politicaPublica.descripcion) AS politicaPublica'),

			DB::raw('concat_ws(" ",objetivosPND.clave,objetivosPND.descripcion) AS objetivoPND'),
			'titular.nombre AS liderPrograma','titular.email AS liderCorreo','titular.telefono AS liderTelefono')
				->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestal','programaPresupuestal.clave','=','programa.claveProgramaPresupuestario')
				->leftjoin('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','programa.claveUnidadResponsable')
				->leftjoin('catalogoODM as ODM','ODM.id','=','programa.idOdm')
				->leftjoin('catalogoModalidad as modalidad','modalidad.id','=','programa.idModalidad')
				->leftjoin('catalogoProgramasSectoriales as sectorial','sectorial.clave','=','programa.claveProgramaSectorial')
				->leftjoin('catalogoObjetivosPED as objetivosPED','objetivosPED.id','=','programa.idObjetivoPED')
				->leftjoin('catalogoObjetivosPED AS eje','eje.clave','=',DB::raw('SUBSTRING(objetivosPED.clave,1,2)'))
				->leftjoin('catalogoObjetivosPED AS tema','tema.clave','=',DB::raw('SUBSTRING(objetivosPED.clave,1,4)'))
				->leftjoin('catalogoObjetivosPED AS politicaPublica','politicaPublica.clave','=',DB::raw('SUBSTRING(objetivosPED.clave,1,6)'))
				->leftjoin('catalogoObjetivosPND as objetivosPND','objetivosPND.id','=','programa.idObjetivoPND')
				->leftjoin('titulares As titular','titular.id','=','programa.idLiderPrograma');
	}

	public function programaPresupuestario(){
		return $this->belongsTo('ProgramaPresupuestario','claveProgramaPresupuestario','clave');
	}

	public function arbolProblemas(){
		return $this->hasMany('ProgramaArbolProblema','idPrograma');
	}

	public function arbolObjetivos(){
		return $this->hasMany('ProgramaArbolObjetivo','idPrograma');
	}

	public function indicadores(){
		return $this->hasMany('ProgramaIndicador','idPrograma');
	}

	public function indicadoresDescripcion(){
		return $this->hasMany('ProgramaIndicador','idPrograma')->contenidoDetalle();
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

	public function proyectos(){
		return $this->hasMany('Proyecto','idPrograma');
	}
}