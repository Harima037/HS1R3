<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Programa extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "programa";

	public function scopeContenidoSuggester($query){
		$query->select('programa.id','programaPresupuestal.id as idProgramaPresupuestario','programa.claveProgramaPresupuestario','programa.claveUnidadResponsable','programa.idEstatus',
				DB::raw('concat_ws(" ",programaPresupuestal.clave,programaPresupuestal.descripcion) as programaPresupuestario'),
				DB::raw('concat_ws(" ",programa.claveUnidadResponsable,unidadResponsable.descripcion) AS unidadResponsable'),
				'estatus.descripcion AS estatus','programa.idUsuarioValidacionSeg','programa.idUsuarioRendCuenta'
			)
			->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestal','programaPresupuestal.clave','=','programa.claveProgramaPresupuestario')
			->leftjoin('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','programa.claveUnidadResponsable')
			->leftjoin('catalogoEstatusProyectos AS estatus','estatus.id','=','programa.idEstatus');
	}

	public function scopeContenidoDetalle($query){
		$query->select('programa.*',
						DB::raw('concat_ws(" ",programaPresupuestal.clave,programaPresupuestal.descripcion) as programaPresupuestarioDescripcion'),
						DB::raw('concat_ws(" ",programa.claveUnidadResponsable,unidadResponsable.descripcion) AS unidadResponsable'),
						DB::raw('concat_ws(" ",ODS.clave,ODS.descripcion) AS ODS'), 'modalidad.clave AS claveModalidad', 'modalidad.descripcion AS modalidad', 
						DB::raw('concat_ws(" ",sectorial.clave,sectorial.descripcion) AS programaSectorial'),
						DB::raw('concat_ws(" ",objetivosPED.clave,objetivosPED.descripcion) AS objetivoPED'),

						DB::raw('concat_ws(" ",eje.clave,eje.descripcion) AS eje'),
						DB::raw('concat_ws(" ",tema.clave,tema.descripcion) AS tema'),
						DB::raw('concat_ws(" ",politicaPublica.clave,politicaPublica.descripcion) AS politicaPublica'),

						DB::raw('concat_ws(" ",objetivosPND.clave,objetivosPND.descripcion) AS objetivoPND'),
						'titular.email AS liderCorreo','titular.telefono AS liderTelefono', 'responsable.nombre AS nombreResponsable', 'responsable.cargo AS cargoResponsable',
						'liderPrograma.nombre AS liderPrograma',
						'liderPrograma.cargo AS liderProgramaCargo',
						'jefePlaneacion.nombre AS jefePlaneacion',
						'jefePlaneacion.cargo AS jefePlaneacionCargo',
						'coordinadorGrupoEstrategico.nombre AS coordinadorGrupoEstrategico',
						'coordinadorGrupoEstrategico.cargo AS coordinadorGrupoEstrategicoCargo'
					)
				->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestal','programaPresupuestal.clave','=','programa.claveProgramaPresupuestario')
				->leftjoin('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','programa.claveUnidadResponsable')
				->leftjoin('catalogoObjetivosDesarrolloSostenible as ODS','ODS.id','=','programa.idOds')
				->leftjoin('catalogoModalidad as modalidad','modalidad.id','=','programa.idModalidad')
				->leftjoin('catalogoProgramasSectoriales as sectorial','sectorial.clave','=','programa.claveProgramaSectorial')
				->leftjoin('catalogoObjetivosPED as objetivosPED','objetivosPED.id','=','programa.idObjetivoPED')
				->leftjoin('catalogoObjetivosPED AS eje','eje.clave','=',DB::raw('SUBSTRING(objetivosPED.clave,1,2)'))
				->leftjoin('catalogoObjetivosPED AS tema','tema.clave','=',DB::raw('SUBSTRING(objetivosPED.clave,1,4)'))
				->leftjoin('catalogoObjetivosPED AS politicaPublica','politicaPublica.clave','=',DB::raw('SUBSTRING(objetivosPED.clave,1,6)'))
				->leftjoin('catalogoObjetivosPND as objetivosPND','objetivosPND.id','=','programa.idObjetivoPND')
				->leftjoin('vistaDirectorio As titular','titular.id','=','programa.idLiderPrograma')
				->leftjoin('vistaDirectorio As responsable','responsable.id','=','programa.idResponsable')
				->leftjoin('vistaDirectorio AS liderPrograma','liderPrograma.id','=','programa.idLiderPrograma')
				->leftjoin('vistaDirectorio AS jefePlaneacion','jefePlaneacion.id','=','programa.idJefePlaneacion')
				->leftjoin('vistaDirectorio AS coordinadorGrupoEstrategico','coordinadorGrupoEstrategico.id','=','programa.idCoordinadorGrupoEstrategico')
				;
				
	}

	public function liderPrograma(){
		return $this->belongsTo('Directorio','idLiderPrograma')->withTrashed();
	}

	public function jefePlaneacion(){
		return $this->belongsTo('Directorio','idJefePlaneacion')->withTrashed();
	}

	public function coordinadorGrupoEstrategico(){
		return $this->belongsTo('Directorio','idCoordinadorGrupoEstrategico')->withTrashed();
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