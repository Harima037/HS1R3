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
        					 ->where('origenAsignacion',$item->origenAsignacion)
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
		return $this->unidadResponsable . $this->finalidad . $this->funcion . $this->subFuncion . $this->subSubFuncion . $this->programaSectorial . $this->programaPresupuestario . $this->origenAsignacion . $this->actividadInstitucional . $this->proyectoEstrategico . str_pad($this->numeroProyectoEstrategico, 3,'0',STR_PAD_LEFT);
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

	public function scopeContenidoSuggester($query){
		$query->select('proyectos.id','nombreTecnico','catalogoEstatusProyectos.descripcion AS estatusProyectoDescripcion',
				'proyectos.idEstatusProyecto','catalogoUnidadesResponsables.descripcion AS unidadResponsableDescripcion',
				'unidadResponsable','finalidad','funcion','subFuncion','subSubFuncion','programaSectorial','programaPresupuestario',
				'origenAsignacion','actividadInstitucional','proyectoEstrategico','numeroProyectoEstrategico','idClasificacionProyecto')
				->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
				->join('catalogoUnidadesResponsables','catalogoUnidadesResponsables.clave','=','proyectos.unidadResponsable')
				->orderBy('proyectos.nombreTecnico','asc');
	}

	public function scopeContenidoReporte($query){
		return $query->leftjoin('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','proyectos.unidadResponsable')
					->leftjoin('catalogoFuncionesGasto AS finalidad','finalidad.clave','=','proyectos.finalidad')
					->leftjoin('catalogoFuncionesGasto AS funcion','funcion.clave','=',DB::raw('concat_ws(".",proyectos.finalidad,proyectos.funcion)'))
					->leftjoin('catalogoFuncionesGasto AS subFuncion','subFuncion.clave','=',DB::raw('concat_ws(".",proyectos.finalidad,proyectos.funcion,proyectos.subFuncion)'))
					->leftjoin('catalogoFuncionesGasto AS subSubFuncion','subSubFuncion.clave','=',DB::raw('concat_ws(".",proyectos.finalidad,proyectos.funcion,proyectos.subFuncion,proyectos.subSubFuncion)'))
					->leftjoin('catalogoProgramasSectoriales AS programaSectorial','programaSectorial.clave','=','proyectos.programaSectorial')
					->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','proyectos.programaPresupuestario')
					->leftjoin('catalogoOrigenesAsignacion AS origenAsignacion','origenAsignacion.clave','=','proyectos.origenAsignacion')
					->leftjoin('catalogoActividades AS actividadInstitucional','actividadInstitucional.clave','=','proyectos.actividadInstitucional')
					->leftjoin('catalogoProyectosEstrategicos AS proyectoEstrategico','proyectoEstrategico.clave','=','proyectos.proyectoEstrategico')
					->leftjoin('catalogoObjetivosPED AS objetivoPED','objetivoPED.id','=','proyectos.idObjetivoPED')
					->leftjoin('catalogoEstrategiasEstatales AS estrategiaEstatal','estrategiaEstatal.id','=','proyectos.idEstrategiaEstatal')
					->leftjoin('catalogoEstrategiasNacionales AS estrategiaNacional','estrategiaNacional.id','=','proyectos.idEstrategiaNacional')
					->leftjoin('estrategia AS objetivoEstrategico','objetivoEstrategico.id','=','proyectos.idObjetivoEstrategico')

					->leftjoin('catalogoObjetivosPED AS ejeRector','ejeRector.clave','=',DB::raw('SUBSTRING(objetivoPED.clave,1,4)'))
					->leftjoin('catalogoObjetivosPED AS politicaPublica','politicaPublica.clave','=',DB::raw('SUBSTRING(objetivoPED.clave,1,6)'))

					->leftjoin('catalogoTiposProyectos AS tipoProyecto','tipoProyecto.id','=','proyectos.idTipoProyecto')
					->leftjoin('catalogoCoberturas AS cobertura','cobertura.id','=','proyectos.idCobertura')
					->leftjoin('catalogoTiposAcciones AS tipoAccion','tipoAccion.id','=','proyectos.idTipoAccion')
					->leftjoin('vistaDirectorio AS liderProyecto','liderProyecto.id','=','proyectos.idLiderProyecto')
					->leftjoin('vistaDirectorio AS jefeInmediato','jefeInmediato.id','=','proyectos.idJefeInmediato')
					->leftjoin('vistaDirectorio AS jefePlaneacion','jefePlaneacion.id','=','proyectos.idJefePlaneacion')
					->leftjoin('vistaDirectorio AS coordinadorGrupoEstrategico','coordinadorGrupoEstrategico.id','=','proyectos.idCoordinadorGrupoEstrategico')
					->leftjoin('vistaDirectorio AS responsableInformacion','responsableInformacion.id','=','proyectos.idResponsable')

					->leftjoin('vistaMunicipios AS municipio','municipio.clave','=','proyectos.claveMunicipio')
					->leftjoin('vistaRegiones AS region','region.region','=','proyectos.claveRegion')

					->select('proyectos.*',

						DB::raw('concat_ws(" ",unidadResponsable.clave,unidadResponsable.descripcion) AS unidadResponsableDescripcion'),
						DB::raw('concat_ws(" ",finalidad.clave,finalidad.descripcion) AS finalidadDescripcion'),
						DB::raw('concat_ws(" ",funcion.clave,funcion.descripcion) AS funcionDescripcion'),
						DB::raw('concat_ws(" ",subFuncion.clave,subFuncion.descripcion) AS subFuncionDescripcion'),
						DB::raw('concat_ws(" ",subSubFuncion.clave,subSubFuncion.descripcion) AS subSubFuncionDescripcion'),
						DB::raw('concat_ws(" ",programaSectorial.clave,programaSectorial.descripcion) AS programaSectorialDescripcion'),
						DB::raw('concat_ws(" ",programaPresupuestario.clave,programaPresupuestario.descripcion) AS programaPresupuestarioDescripcion'),
						DB::raw('concat_ws(" ",objetivoPED.clave,objetivoPED.descripcion) AS objetivoPEDDescripcion'),
						DB::raw('concat_ws(" ",ejeRector.clave,ejeRector.descripcion) AS ejeRectorDescripcion'),
						DB::raw('concat_ws(" ",politicaPublica.clave,politicaPublica.descripcion) AS politicaPublicaDescripcion'),
						DB::raw('concat_ws(" ",origenAsignacion.clave,origenAsignacion.descripcion) AS origenAsignacionDescripcion'),
						DB::raw('concat_ws(" ",proyectoEstrategico.clave,proyectoEstrategico.descripcion) AS proyectoEstrategicoDescripcion'),
						DB::raw('concat_ws(" ",actividadInstitucional.clave,actividadInstitucional.descripcion) AS actividadInstitucionalDescripcion'),
						DB::raw('concat_ws(" ",tipoAccion.clave,tipoAccion.descripcion) AS tipoAccionDescripcion'),
						DB::raw('concat_ws(" ",tipoProyecto.clave,tipoProyecto.descripcion) AS tipoProyectoDescripcion'),
						DB::raw('concat_ws(" ",cobertura.clave,cobertura.descripcion) AS coberturaDescripcion'),

						DB::raw('concat_ws(" ",estrategiaEstatal.claveEstrategia,estrategiaEstatal.descripcion) AS estrategiaEstatalDescripcion'),
						'estrategiaNacional.descripcion AS estrategiaNacionalDescripcion',
						'estrategiaEstatal.claveAlineacion AS claveAlineacion',
						'objetivoEstrategico.objetivoEstrategico AS objetivoEstrategico',
						
						'cobertura.clave AS claveCobertura',

						'municipio.nombre AS municipioDescripcion', 'region.nombre AS regionDescripcion',

						'liderProyecto.nombre AS liderProyecto',
						'jefeInmediato.nombre AS jefeInmediato',
						'jefePlaneacion.nombre AS jefePlaneacion',
						'coordinadorGrupoEstrategico.nombre AS coordinadorGrupoEstrategico',
						'responsableInformacion.nombre AS responsableInformacion',
						'liderProyecto.cargo AS liderProyectoCargo',
						'jefeInmediato.cargo AS jefeInmediatoCargo',
						'jefePlaneacion.cargo AS jefePlaneacionCargo',
						'coordinadorGrupoEstrategico.cargo AS coordinadorGrupoEstrategicoCargo',
						'responsableInformacion.cargo AS responsableInformacionCargo'
					);
	}

	public function scopeIndicadoresResultados($query,$mes,$ejercicio){
		$query->select(
				'proyectos.id', 'proyectos.nombreTecnico', 'proyectos.idClasificacionProyecto',
				'proyectos.unidadResponsable','proyectos.finalidad','proyectos.funcion',
				'proyectos.subFuncion','proyectos.subSubFuncion','proyectos.programaSectorial',
				'proyectos.programaPresupuestario','proyectos.origenAsignacion','estatusMes.indicadorResultadoBeneficiarios',
				'proyectos.actividadInstitucional','proyectos.proyectoEstrategico','estatusMes.idEstatus AS idEstatusAvance',
				'proyectos.numeroProyectoEstrategico','proyectos.idCobertura','cobertura.clave AS claveCobertura',
				DB::raw('count(distinct beneficiarios.idTipoBeneficiario) AS beneficiarios')
			)
			->leftjoin('catalogoCoberturas AS cobertura','cobertura.id','=','proyectos.idCobertura')
			->leftjoin('proyectoBeneficiarios AS beneficiarios',function($join){
				$join->on('beneficiarios.idProyecto','=','proyectos.id')
					->whereNull('beneficiarios.borradoAl');
			})
			->leftjoin('evaluacionProyectoMes AS estatusMes',function($join)use($mes){
				$join->on('estatusMes.idProyecto','=','proyectos.id')
					->where('estatusMes.mes','=',$mes)
					->where('estatusMes.idEstatus','>=',4);
			})
			->where('proyectos.idEstatusProyecto','=',5)
			->where('proyectos.ejercicio','=',$ejercicio)
			->groupBy('proyectos.id')
			->orderBy(DB::raw('count(distinct beneficiarios.idTipoBeneficiario)'),'desc')
			->orderBy('proyectos.id','desc');
	}

	public function scopeFuentesFinanciamientoEP01($query,$mes,$ejercicio){
    	$query->select('proyectos.id AS idProyecto', DB::raw('UPPER(proyectos.nombreTecnico) As nombreTecnico'),
    			'proyectos.unidadResponsable','proyectos.finalidad','proyectos.funcion',
				'proyectos.subFuncion','proyectos.subSubFuncion','proyectos.programaSectorial',
				'proyectos.programaPresupuestario','proyectos.origenAsignacion',
				'proyectos.actividadInstitucional','proyectos.proyectoEstrategico',
				'proyectos.numeroProyectoEstrategico','fuente.clave','fuente.descripcion',
				DB::raw('sum(ep01.presupuestoAprobado) AS presupuestoAprobado'),
				DB::raw('sum(ep01.presupuestoModificado) AS presupuestoModificado'),
				DB::raw('sum(ep01.presupuestoDevengadoModificado) AS presupuestoDevengado'))
    		->leftjoin('cargaDatosEP01 AS ep01',function($join) use ($mes,$ejercicio){
				$join->on('ep01.UR','=','proyectos.unidadResponsable')
					->on('ep01.FI','=','proyectos.finalidad')
					->on('ep01.FU','=','proyectos.funcion')
					->on('ep01.SF','=','proyectos.subFuncion')
					->on('ep01.SSF','=','proyectos.subSubFuncion')
					->on('ep01.PS','=','proyectos.programaSectorial')
					->on('ep01.PP','=','proyectos.programaPresupuestario')
					->on('ep01.OA','=','proyectos.origenAsignacion')
					->on('ep01.AI','=','proyectos.actividadInstitucional')
					->on('ep01.PT','=',DB::raw('concat(proyectos.proyectoEstrategico,LPAD(proyectos.numeroProyectoEstrategico,3,"0"))'))
					->where('ep01.mes','=',$mes)
					//->on('ep01.FF','=','fuente.clave')
					//->on('ep01.DG','LIKE','destinoGasto.destino')
					->where('ep01.ejercicio','=',$ejercicio);
			})
			->leftjoin('catalogoFuenteFinanciamiento AS fuente',function($join){
				$join->on('fuente.clave','=','ep01.FF')->whereNull('fuente.borradoAl');
			})
			->where('proyectos.idEstatusProyecto','=',5)
			->where('proyectos.ejercicio','=',$ejercicio)
			
			->groupBy('proyectos.id')
			->groupBy('ep01.FF')

			->orderBy('proyectos.unidadResponsable','asc')
			->orderBy('proyectos.finalidad','asc')
			->orderBy('proyectos.funcion','asc')
			->orderBy('proyectos.subFuncion','asc')
			->orderBy('proyectos.subSubFuncion','asc')
			->orderBy('proyectos.programaSectorial','asc')
			->orderBy('proyectos.programaPresupuestario','asc')
			->orderBy('proyectos.origenAsignacion','asc')
			->orderBy('proyectos.actividadInstitucional','asc')
			->orderBy('proyectos.proyectoEstrategico','asc')
			->orderBy('proyectos.numeroProyectoEstrategico','asc')
			->orderBy('proyectos.idClasificacionProyecto','asc');
    }

	public function scopeReporteIndicadoresResultados($query,$mes,$ejercicio){
		$query->select(
				'proyectos.id', DB::raw('UPPER(proyectos.nombreTecnico) As nombreTecnico'), 'proyectos.idClasificacionProyecto',
				'proyectos.unidadResponsable','proyectos.finalidad','proyectos.funcion',
				'proyectos.subFuncion','proyectos.subSubFuncion','proyectos.programaSectorial',
				'proyectos.programaPresupuestario','proyectos.origenAsignacion',
				'proyectos.actividadInstitucional','proyectos.proyectoEstrategico',
				'proyectos.numeroProyectoEstrategico','subFuncionGasto.clave AS subFuncionClave',
				DB::raw('UPPER(subFuncionGasto.descripcion) AS subFuncionDescripcion'),'municipios.nombre AS municipio',
				'proyectos.idCobertura',
				DB::raw('0 AS totalPresupuestoAprobado'),DB::raw('0 AS totalPresupuestoModificado'),
				DB::raw('0 AS totalPresupuestoDevengado')
			)

			->leftjoin('catalogoFuncionesGasto AS subFuncionGasto','subFuncionGasto.clave','=',DB::raw('concat_ws(".",proyectos.finalidad,proyectos.funcion,proyectos.subFuncion,proyectos.subSubFuncion)'))

			->leftjoin('vistaMunicipios AS municipios','municipios.clave','=','proyectos.claveMunicipio')

			->with(array('componentes'=>function($componente)use($mes){
				$componente->select('proyectoComponentes.id',
					'proyectoComponentes.idProyecto','proyectoComponentes.indicador',
					'proyectoComponentes.valorNumerador AS metaAnual','unidadesMedida.descripcion AS unidadMedida',
					'dimensionIndicador.descripcion AS descripcionDimensionIndicador',
					'tipoIndicadores.descripcion AS descripcionTipoIndicador',
					'avanceMetas.planMejora','avanceMetas.justificacionAcumulada','avanceMetas.id AS identificador',
					DB::raw('sum(metasMes.avance) AS avanceAcumulado'), DB::raw('sum(metasMes.meta) AS metaAcumulada'))
					->leftjoin('catalogoUnidadesMedida AS unidadesMedida','unidadesMedida.id','=','proyectoComponentes.idUnidadMedida')
					->leftjoin('catalogoDimensionesIndicador AS dimensionIndicador','dimensionIndicador.id','=','proyectoComponentes.idDimensionIndicador')
					->leftjoin('catalogoTiposIndicadores AS tipoIndicadores','tipoIndicadores.id','=','proyectoComponentes.idTipoIndicador')
					->leftjoin('componenteMetasMes AS metasMes',function($join)use($mes){
						$join->on('metasMes.idComponente','=','proyectoComponentes.id')
							->where('metasMes.mes','<=',$mes)
							->whereNull('metasMes.borradoAl');
					})
					->leftjoin('registroAvancesMetas AS avanceMetas',function($join)use($mes){
						$join->on('avanceMetas.idProyecto','=','proyectoComponentes.idProyecto')
							->on('avanceMetas.idNivel','=','proyectoComponentes.id')
							->where('avanceMetas.mes','=',$mes)
							->where('avanceMetas.nivel','=',1)
							->whereNull('avanceMetas.borradoAl');
					})
					->groupBy('proyectoComponentes.id','metasMes.idComponente');

			},'actividades'=>function($actividad)use($mes){
				$actividad->select('componenteActividades.id','componenteActividades.idComponente',
					'componenteActividades.idProyecto','componenteActividades.indicador',
					'componenteActividades.valorNumerador AS metaAnual','unidadesMedida.descripcion AS unidadMedida',
					'dimensionIndicador.descripcion AS descripcionDimensionIndicador',
					'tipoIndicadores.descripcion AS descripcionTipoIndicador',
					'avanceMetas.planMejora','avanceMetas.justificacionAcumulada','avanceMetas.id AS identificador',
					DB::raw('sum(metasMes.avance) AS avanceAcumulado'), DB::raw('sum(metasMes.meta) AS metaAcumulada'))
					->leftjoin('catalogoUnidadesMedida AS unidadesMedida','unidadesMedida.id','=','componenteActividades.idUnidadMedida')
					->leftjoin('catalogoDimensionesIndicador AS dimensionIndicador','dimensionIndicador.id','=','componenteActividades.idDimensionIndicador')
					->leftjoin('catalogoTiposIndicadores AS tipoIndicadores','tipoIndicadores.id','=','componenteActividades.idTipoIndicador')
					->leftjoin('actividadMetasMes AS metasMes',function($join)use($mes){
						$join->on('metasMes.idActividad','=','componenteActividades.id')
							->where('metasMes.mes','<=',$mes)
							->whereNull('metasMes.borradoAl');
					})
					->leftjoin('registroAvancesMetas AS avanceMetas',function($join)use($mes){
						$join->on('avanceMetas.idProyecto','=','componenteActividades.idProyecto')
							->on('avanceMetas.idNivel','=','componenteActividades.id')
							->where('avanceMetas.mes','=',$mes)
							->where('avanceMetas.nivel','=',2)
							->whereNull('avanceMetas.borradoAl');
					})
					->groupBy('componenteActividades.id','metasMes.idActividad');
			}
			,'beneficiariosDescripcion'=>function($beneficiario) use ($mes){
				$beneficiario->leftjoin('registroAvancesBeneficiarios as avanceBenef',function($join)use($mes){
								$join->on('avanceBenef.idProyectoBeneficiario','=','proyectoBeneficiarios.id')
									->on('avanceBenef.idTipoBeneficiario','=','proyectoBeneficiarios.idTipoBeneficiario')
									->where('avanceBenef.mes','<=',$mes)
									->whereNull('avanceBenef.borradoAl');
							})
							->select('proyectoBeneficiarios.id','proyectoBeneficiarios.idProyecto', //'proyectoBeneficiarios.idTipoBeneficiario',
								DB::raw('sum(avanceBenef.total) AS avanceBeneficiario'),
								DB::raw('sum(if(avanceBenef.sexo = "f",avanceBenef.total,0)) AS avanceBeneficiarioF'),DB::raw('sum(if(avanceBenef.sexo = "m",avanceBenef.total,0)) AS avanceBeneficiarioM'),
								'tipoBeneficiario.grupo AS tipoBeneficiario')
							//->groupBy('proyectoBeneficiarios.idProyecto','proyectoBeneficiarios.idTipoBeneficiario')
							->groupBy('proyectoBeneficiarios.idProyecto','tipoBeneficiario.grupo'); //,'proyectoBeneficiarios.sexo'
			},'evaluacionMes'=>function($evaluacionMes)use($mes){
				$evaluacionMes->where('evaluacionProyectoMes.mes','<=',$mes)
							->where('evaluacionProyectoMes.idEstatus','>=',4)
							->where('evaluacionProyectoMes.idEstatus','<',6)
							->groupBy('evaluacionProyectoMes.idProyecto')
							->select('evaluacionProyectoMes.id','evaluacionProyectoMes.idProyecto','evaluacionProyectoMes.mes',
								DB::raw('sum(evaluacionProyectoMes.indicadorResultadoBeneficiariosF) AS indicadorResultadoBeneficiariosF'),
								DB::raw('sum(evaluacionProyectoMes.indicadorResultadoBeneficiariosM) AS indicadorResultadoBeneficiariosM'),
								DB::raw('sum(evaluacionProyectoMes.indicadorResultadoBeneficiarios) AS indicadorResultadoBeneficiarios'));
			}, 'datosUnidadResponsable'
			))
			
			->where('proyectos.idEstatusProyecto','=',5)
			->where('proyectos.ejercicio','=',$ejercicio)
			
			->groupBy('proyectos.id')

			->orderBy('proyectos.unidadResponsable','asc')
			->orderBy('proyectos.finalidad','asc')
			->orderBy('proyectos.funcion','asc')
			->orderBy('proyectos.subFuncion','asc')
			->orderBy('proyectos.subSubFuncion','asc')
			->orderBy('proyectos.programaSectorial','asc')
			->orderBy('proyectos.programaPresupuestario','asc')
			->orderBy('proyectos.origenAsignacion','asc')
			->orderBy('proyectos.actividadInstitucional','asc')
			->orderBy('proyectos.proyectoEstrategico','asc')
			->orderBy('proyectos.numeroProyectoEstrategico','asc')
			->orderBy('proyectos.idClasificacionProyecto','asc');
	}
	
	public function scopeVariacionesGasto($query,$mes,$ejercicio){
		$query->select(
				'proyectos.id', 'proyectos.nombreTecnico', 'proyectos.idClasificacionProyecto',
				'proyectos.unidadResponsable','proyectos.finalidad','proyectos.funcion',
				'proyectos.subFuncion','proyectos.subSubFuncion','proyectos.programaSectorial',
				'proyectos.programaPresupuestario','proyectos.origenAsignacion',
				'proyectos.actividadInstitucional','proyectos.proyectoEstrategico',
				'proyectos.numeroProyectoEstrategico','variacionGasto.razonesAprobado','variacionGasto.razonesDevengado',

				DB::raw('sum(ep01.presupuestoAprobado) AS presupuestoAprobado'),
				DB::raw('sum(ep01.presupuestoModificado) AS presupuestoModificado'),
				DB::raw('sum(ep01.presupuestoDevengadoModificado) AS presupuestoDevengadoModificado'),

				DB::raw('concat_ws(" ",programaPresupuestario.clave,programaPresupuestario.descripcion) AS programaPresupuestarioDescipcion')
			)

			->leftjoin('proyectosVariacionGastoRazones AS variacionGasto',function($join)use($mes){
				$join->on('variacionGasto.idProyecto','=','proyectos.id')
					->where('variacionGasto.mes','=',$mes);
			})

			->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','proyectos.programaPresupuestario')

			->join('cargaDatosEP01 AS ep01',function($join) use ($mes,$ejercicio){
				$join->on('ep01.UR','=','proyectos.unidadResponsable')
					->on('ep01.FI','=','proyectos.finalidad')
					->on('ep01.FU','=','proyectos.funcion')
					->on('ep01.SF','=','proyectos.subFuncion')
					->on('ep01.SSF','=','proyectos.subSubFuncion')
					->on('ep01.PS','=','proyectos.programaSectorial')
					->on('ep01.PP','=','proyectos.programaPresupuestario')
					->on('ep01.OA','=','proyectos.origenAsignacion')
					->on('ep01.AI','=','proyectos.actividadInstitucional')
					->on('ep01.PT','=',DB::raw('concat(proyectos.proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'))
					->where('ep01.mes','=',$mes)
					->where('ep01.ejercicio','=',$ejercicio);
			})

			->where('proyectos.idEstatusProyecto','=',5)
			
			->groupBy('proyectos.id');
	}
	
	public function scopeReporteVariacionesGasto($query,$mes,$ejercicio){
		$query->select(
				'proyectos.id', 'proyectos.nombreTecnico', 'proyectos.idClasificacionProyecto',
				'proyectos.unidadResponsable','proyectos.finalidad','proyectos.funcion',
				'proyectos.subFuncion','proyectos.subSubFuncion','proyectos.programaSectorial',
				'proyectos.programaPresupuestario','proyectos.origenAsignacion',
				'proyectos.actividadInstitucional','proyectos.proyectoEstrategico',
				'proyectos.numeroProyectoEstrategico','variacionGasto.razonesAprobado','variacionGasto.razonesDevengado',

				DB::raw('sum(ep01.presupuestoAprobado) AS presupuestoAprobado'),
				DB::raw('sum(ep01.presupuestoModificado) AS presupuestoModificado'),
				DB::raw('sum(ep01.presupuestoDevengadoModificado) AS presupuestoDevengado'),
				DB::raw('concat_ws(" ",programaPresupuestario.clave,programaPresupuestario.descripcion) AS programaPresupuestarioDescipcion')
			)

			->leftjoin('proyectosVariacionGastoRazones AS variacionGasto',function($join)use($mes){
				$join->on('variacionGasto.idProyecto','=','proyectos.id')
					->where('variacionGasto.mes','=',$mes);
			})

			->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','proyectos.programaPresupuestario')

			->join('cargaDatosEP01 AS ep01',function($join) use ($mes,$ejercicio){
				$join->on('ep01.UR','=','proyectos.unidadResponsable')
					->on('ep01.FI','=','proyectos.finalidad')
					->on('ep01.FU','=','proyectos.funcion')
					->on('ep01.SF','=','proyectos.subFuncion')
					->on('ep01.SSF','=','proyectos.subSubFuncion')
					->on('ep01.PS','=','proyectos.programaSectorial')
					->on('ep01.PP','=','proyectos.programaPresupuestario')
					->on('ep01.OA','=','proyectos.origenAsignacion')
					->on('ep01.AI','=','proyectos.actividadInstitucional')
					->on('ep01.PT','=',DB::raw('concat(proyectos.proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'))
					->where('ep01.mes','=',$mes)
					->where('ep01.ejercicio','=',$ejercicio);
			})
			->where('proyectos.idEstatusProyecto','=',5)			
			->groupBy('proyectos.id');
	}

	public function scopeCedulasAvances($query,$mes,$ejercicio){
		$query->select(
				'proyectos.id', 'proyectos.nombreTecnico', 'proyectos.idClasificacionProyecto',
				'proyectos.unidadResponsable','proyectos.finalidad','proyectos.funcion',
				'proyectos.subFuncion','proyectos.subSubFuncion','proyectos.programaSectorial',
				'proyectos.programaPresupuestario','proyectos.origenAsignacion',
				'proyectos.actividadInstitucional','proyectos.proyectoEstrategico',
				'proyectos.numeroProyectoEstrategico','analisisFunc.finalidadProyecto',

				DB::raw('FORMAT(sum(ep01.presupuestoAprobado),2) AS presupuestoAprobado'),
				DB::raw('FORMAT(sum(ep01.presupuestoModificado),2) AS presupuestoModificado'),
				DB::raw('FORMAT(sum(ep01.presupuestoEjercidoModificado),2) AS presupuestoEjercidoModificado'),

				DB::raw('concat_ws(" ",programaPresupuestario.clave,programaPresupuestario.descripcion) AS programaPresupuestarioDescipcion')
			)

			->leftjoin('evaluacionAnalisisFuncional AS analisisFunc',function($join)use($mes){
				$join->on('analisisFunc.idProyecto','=','proyectos.id')
					->where('analisisFunc.mes','=',$mes);
			})

			->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','proyectos.programaPresupuestario')

			->join('cargaDatosEP01 AS ep01',function($join) use ($mes,$ejercicio){
				$join->on('ep01.UR','=','proyectos.unidadResponsable')
					->on('ep01.FI','=','proyectos.finalidad')
					->on('ep01.FU','=','proyectos.funcion')
					->on('ep01.SF','=','proyectos.subFuncion')
					->on('ep01.SSF','=','proyectos.subSubFuncion')
					->on('ep01.PS','=','proyectos.programaSectorial')
					->on('ep01.PP','=','proyectos.programaPresupuestario')
					->on('ep01.OA','=','proyectos.origenAsignacion')
					->on('ep01.AI','=','proyectos.actividadInstitucional')
					->on('ep01.PT','=',DB::raw('concat(proyectos.proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'))
					->where('ep01.mes','=',$mes)
					->where('ep01.ejercicio','=',$ejercicio);
			})

			->where('proyectos.idEstatusProyecto','=',5)
			
			->groupBy('proyectos.id');
	}

	public function scopeReporteCedulasAvances($query,$mes,$ejercicio){
		$query->cedulasAvances($mes,$ejercicio)

			->with(array('componentes'=>function($componente)use($mes){
				$componente->select('proyectoComponentes.id',
					'proyectoComponentes.idProyecto','proyectoComponentes.indicador',
					DB::raw('proyectoComponentes.valorNumerador AS metaAnual'),
					'unidadesMedida.descripcion AS unidadMedida',
					DB::raw('sum(metasMes.avance) AS avanceAcumulado'))
					->leftjoin('catalogoUnidadesMedida AS unidadesMedida','unidadesMedida.id','=','proyectoComponentes.idUnidadMedida')
					->leftjoin('componenteMetasMes AS metasMes',function($join)use($mes){
						$join->on('metasMes.idComponente','=','proyectoComponentes.id')
							->where('metasMes.mes','<=',$mes)
							->whereNull('metasMes.borradoAl');
					})
					->groupBy('proyectoComponentes.id','metasMes.idComponente');

			},'actividades'=>function($actividad)use($mes){
				$actividad->select('componenteActividades.id','componenteActividades.idComponente',
					'componenteActividades.idProyecto','componenteActividades.indicador',
					DB::raw('componenteActividades.valorNumerador AS metaAnual'),
					'unidadesMedida.descripcion AS unidadMedida',
					DB::raw('sum(metasMes.avance) AS avanceAcumulado'))
					->leftjoin('catalogoUnidadesMedida AS unidadesMedida','unidadesMedida.id','=','componenteActividades.idUnidadMedida')
					->leftjoin('actividadMetasMes AS metasMes',function($join)use($mes){
						$join->on('metasMes.idActividad','=','componenteActividades.id')
							->where('metasMes.mes','<=',$mes)
							->whereNull('metasMes.borradoAl');
					})
					->groupBy('componenteActividades.id','metasMes.idActividad');
			},'beneficiariosDescripcion'=>function($beneficiarios)use($mes){
				$beneficiarios->select('proyectoBeneficiarios.id','proyectoBeneficiarios.idTipoBeneficiario',
					'proyectoBeneficiarios.idProyecto',DB::raw('sum(avanceBenef.total) AS avanceTotal'),
					//DB::raw('sum(proyectoBeneficiarios.total) AS programadoTotal',
					DB::raw('CAST((sum(proyectoBeneficiarios.total) / (count(proyectoBeneficiarios.idTipoBeneficiario)/count(distinct proyectoBeneficiarios.sexo))) AS INTEGER) AS programadoTotal'),
					'tipoBeneficiario.descripcion AS tipoBeneficiario')
					->leftjoin('registroAvancesBeneficiarios AS avanceBenef',function($join)use($mes){
						$join->on('avanceBenef.idTipoBeneficiario','=','proyectoBeneficiarios.idTipoBeneficiario')
							->on('avanceBenef.idProyecto','=','proyectoBeneficiarios.idProyecto')
							->on('avanceBenef.idProyectoBeneficiario','=','proyectoBeneficiarios.id')
							->where('avanceBenef.mes','<=',$mes)
							->whereNull('avanceBenef.borradoAl');
					})
					->groupBy('proyectoBeneficiarios.idProyecto','proyectoBeneficiarios.idTipoBeneficiario','avanceBenef.idTipoBeneficiario','avanceBenef.idProyecto');
			}))

			->orderBy('proyectos.unidadResponsable','asc')
			->orderBy('proyectos.finalidad','asc')
			->orderBy('proyectos.funcion','asc')
			->orderBy('proyectos.subFuncion','asc')
			->orderBy('proyectos.subSubFuncion','asc')
			->orderBy('proyectos.programaSectorial','asc')
			->orderBy('proyectos.programaPresupuestario','asc')
			->orderBy('proyectos.origenAsignacion','asc')
			->orderBy('proyectos.actividadInstitucional','asc')
			->orderBy('proyectos.proyectoEstrategico','asc')
			->orderBy('proyectos.numeroProyectoEstrategico','asc')
			->orderBy('proyectos.idClasificacionProyecto','asc');
	}

	public function scopeReporteResumenAvances($query,$mes,$ejercicio){
		$query->select(
				'proyectos.id', 'proyectos.nombreTecnico', 'proyectos.idClasificacionProyecto',
				'proyectos.unidadResponsable','proyectos.finalidad','proyectos.funcion',
				'proyectos.subFuncion','proyectos.subSubFuncion','proyectos.programaSectorial',
				'proyectos.programaPresupuestario','proyectos.origenAsignacion',
				'proyectos.actividadInstitucional','proyectos.proyectoEstrategico',
				'proyectos.numeroProyectoEstrategico',

				DB::raw('FORMAT(sum(ep01.presupuestoDevengadoModificado),2) AS presupuestoDevengadoModificado')
			)

			->join('cargaDatosEP01 AS ep01',function($join) use ($mes,$ejercicio){
				$join->on('ep01.UR','=','proyectos.unidadResponsable')
					->on('ep01.FI','=','proyectos.finalidad')
					->on('ep01.FU','=','proyectos.funcion')
					->on('ep01.SF','=','proyectos.subFuncion')
					->on('ep01.SSF','=','proyectos.subSubFuncion')
					->on('ep01.PS','=','proyectos.programaSectorial')
					->on('ep01.PP','=','proyectos.programaPresupuestario')
					->on('ep01.OA','=','proyectos.origenAsignacion')
					->on('ep01.AI','=','proyectos.actividadInstitucional')
					->on('ep01.PT','=',DB::raw('concat(proyectos.proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'))
					->where('ep01.mes','=',$mes)
					->where('ep01.ejercicio','=',$ejercicio);
			})

			->where('proyectos.idEstatusProyecto','=',5)
			
			->groupBy('proyectos.id')

			->with(array(
			'componentesMetasMes'=>function($componente)use($mes){
				$componente->select('componenteMetasMes.id',
					'componenteMetasMes.idProyecto',
					DB::raw('componente.valorNumerador AS metaAnual'),
					DB::raw('sum(componenteMetasMes.avance) AS avanceAcumulado'),
					DB::raw('(sum(componenteMetasMes.avance)/componente.valorNumerador)*100 AS porcentajeAcumulado')
					)
					->where('componenteMetasMes.mes','<=',$mes)
					->leftjoin('proyectoComponentes AS componente',function($join)use($mes){
						$join->on('componente.id','=','componenteMetasMes.idComponente')
							->whereNull('componente.borradoAl');
					})
					->groupBy('componenteMetasMes.idComponente');

			},'actividadesMetasMes'=>function($actividad)use($mes){
				$actividad->select('actividadMetasMes.id',
					'actividadMetasMes.idProyecto',
					DB::raw('actividad.valorNumerador AS metaAnual'),
					DB::raw('sum(actividadMetasMes.avance) AS avanceAcumulado'),
					DB::raw('(sum(actividadMetasMes.avance)/actividad.valorNumerador)*100 AS porcentajeAcumulado')
					)
					->where('actividadMetasMes.mes','<=',$mes)
					->leftjoin('componenteActividades AS actividad',function($join)use($mes){
						$join->on('actividad.id','=','actividadMetasMes.idActividad')
							->whereNull('actividad.borradoAl');
					})
					->groupBy('actividadMetasMes.idActividad');
			},'registroAvanceBeneficiarios'=>function($beneficiarios)use($mes){
				$beneficiarios->select('registroAvancesBeneficiarios.id','registroAvancesBeneficiarios.idProyecto',
					'registroAvancesBeneficiarios.idTipoBeneficiario',
					DB::raw('sum(registroAvancesBeneficiarios.total) AS avanceBeneficiario'))
				->where('registroAvancesBeneficiarios.mes','<=',$mes)
				->groupBy('registroAvancesBeneficiarios.idProyecto','registroAvancesBeneficiarios.idTipoBeneficiario');
			},'evaluacionMes'=>function($evaluacionMes)use($mes){
				$evaluacionMes->where('evaluacionProyectoMes.mes','<=',$mes)
					->where('evaluacionProyectoMes.idEstatus','>=',4)
					->where('evaluacionProyectoMes.idEstatus','<',6)
					->groupBy('evaluacionProyectoMes.idProyecto')
					->select('evaluacionProyectoMes.id','evaluacionProyectoMes.idProyecto','evaluacionProyectoMes.mes',
						DB::raw('sum(evaluacionProyectoMes.indicadorResultadoBeneficiarios) AS indicadorResultadoBeneficiarios')
					);
			}))

			->orderBy('proyectos.idClasificacionProyecto','asc')
			->orderBy('proyectos.unidadResponsable','asc')
			->orderBy('proyectos.finalidad','asc')
			->orderBy('proyectos.funcion','asc')
			->orderBy('proyectos.subFuncion','asc')
			->orderBy('proyectos.subSubFuncion','asc')
			->orderBy('proyectos.programaSectorial','asc')
			->orderBy('proyectos.programaPresupuestario','asc')
			->orderBy('proyectos.origenAsignacion','asc')
			->orderBy('proyectos.actividadInstitucional','asc')
			->orderBy('proyectos.proyectoEstrategico','asc')
			->orderBy('proyectos.numeroProyectoEstrategico','asc')
			;
	}

	public function scopeReporteCuentaPublica($query,$mes,$anio){
		$query->select(
				'proyectos.id','proyectos.nombreTecnico', 'proyectos.idClasificacionProyecto',
				'proyectos.unidadResponsable','proyectos.finalidad','proyectos.funcion',
				'proyectos.subFuncion','proyectos.subSubFuncion','proyectos.programaSectorial',
				'proyectos.programaPresupuestario','proyectos.origenAsignacion',
				'proyectos.actividadInstitucional','proyectos.proyectoEstrategico',
				'proyectos.numeroProyectoEstrategico',

				DB::raw('concat_ws(".",proyectos.finalidad,proyectos.funcion,proyectos.subFuncion,proyectos.subSubFuncion) AS subFuncionClave'),

				'unidadesResponsables.descripcion AS unidadResponsableDescipcion',

				DB::raw('concat_ws(" ",programaPresupuestario.clave,programaPresupuestario.descripcion) AS programaPresupuestarioDescipcion'),

				DB::raw('concat_ws(".- ",funcionGasto.clave,funcionGasto.descripcion) AS funcionGasto'),
				DB::raw('UPPER(concat_ws(".- ",subFuncionGasto.clave,subFuncionGasto.descripcion)) AS subFuncionGasto'),

				DB::raw('concat_ws(" ",eje.clave,eje.descripcion) AS ejeDescripcion'),	
				DB::raw('concat_ws(" ",tema.clave,tema.descripcion) AS temaDescripcion'),
				DB::raw('concat_ws(" ",politicaPublica.clave,politicaPublica.descripcion) AS politicaPublicaDescripcion'),

				'politicaPublica.clave AS politicaPublicaClave',

				'cuentaPub.cuentaPublica','cuentaPub.mes',

				DB::raw('count(componenteMetas.meta) + count(actividadMetas.meta) AS totalMetas')
			)
			
			->leftjoin('catalogoUnidadesResponsables AS unidadesResponsables','unidadesResponsables.clave','=','proyectos.unidadResponsable')
			
			->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','proyectos.programaPresupuestario')

			->leftjoin('catalogoObjetivosPED AS objetivoPED','objetivoPED.id','=','proyectos.idObjetivoPED')
			->leftjoin('catalogoObjetivosPED AS eje','eje.clave','=',DB::raw('SUBSTRING(objetivoPED.clave,1,2)'))
			->leftjoin('catalogoObjetivosPED AS tema','tema.clave','=',DB::raw('SUBSTRING(objetivoPED.clave,1,4)'))
			->leftjoin('catalogoObjetivosPED AS politicaPublica','politicaPublica.clave','=',DB::raw('SUBSTRING(objetivoPED.clave,1,6)'))

			->leftjoin('catalogoFuncionesGasto AS funcionGasto','funcionGasto.clave','=',DB::raw('concat_ws(".",proyectos.finalidad,proyectos.funcion)'))

			->leftjoin('catalogoFuncionesGasto AS subFuncionGasto','subFuncionGasto.clave','=',DB::raw('concat_ws(".",proyectos.finalidad,proyectos.funcion,proyectos.subFuncion,proyectos.subSubFuncion)'))
			
			->leftjoin('componenteMetasMes AS componenteMetas',function($join)use($mes){
				$join->on('componenteMetas.idProyecto','=','proyectos.id')
					->where('componenteMetas.mes','<=',$mes)
					->whereNull('componenteMetas.borradoAl');
			})

			->leftjoin('actividadMetasMes AS actividadMetas',function($join)use($mes){
				$join->on('actividadMetas.idProyecto','=','proyectos.id')
					->where('actividadMetas.mes','<=',$mes)
					->whereNull('actividadMetas.borradoAl');
			})
			
			->leftjoin('evaluacionProyectoMes AS proyectoMes',function($join)use($mes,$anio){
				$join->on('proyectoMes.idProyecto','=','proyectos.id')
					->on('proyectoMes.idEstatus','in',DB::raw('(4,5)'))
					->where('proyectoMes.mes','=',$mes)
					->where('proyectoMes.anio','=',$anio)
					->whereNull('proyectoMes.borradoAl');
			})

			->leftjoin('evaluacionAnalisisFuncional AS cuentaPub',function($join){
				$join->on('cuentaPub.idProyecto','=','proyectoMes.idProyecto')
					->on('cuentaPub.mes','=','proyectoMes.mes')
					->whereNull('cuentaPub.borradoAl');
			})

			->where('proyectos.idEstatusProyecto','=',5)
			
			->groupBy('proyectos.id')

			->orderBy('proyectos.finalidad','asc')
			->orderBy('proyectos.funcion','asc')
			->orderBy('proyectos.subFuncion','asc')
			->orderBy('proyectos.subSubFuncion','asc')

			->orderBy('proyectos.idClasificacionProyecto','asc')

			->orderBy('politicaPublica.clave','asc')
			->orderBy('proyectos.programaPresupuestario','asc')
			
			->orderBy('proyectos.unidadResponsable','asc')
			->orderBy('proyectos.programaSectorial','asc')
			->orderBy('proyectos.origenAsignacion','asc')
			->orderBy('proyectos.actividadInstitucional','asc')
			->orderBy('proyectos.proyectoEstrategico','asc')
			->orderBy('proyectos.numeroProyectoEstrategico','asc');
    }

    public function scopeReporteProyectosEvaluacion($query,$ejercicio,$mes){
    	return $query->leftjoin('proyectosEvaluacion AS evaluacion',function($join)use($mes){
						$join->on('evaluacion.idProyecto','=','proyectos.id')
							->where('evaluacion.mes','=',$mes)
							->whereNull('evaluacion.borradoAl');
					})
					->select('proyectos.*','evaluacion.observaciones','evaluacion.justificaciones')
					->where('ejercicio',$ejercicio);
    }

    public function scopeReporteEvaluacionProyectos($query,$ejercicio,$mes){
		$query = $query->leftjoin('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','proyectos.unidadResponsable')
					->leftjoin('catalogoTiposProyectos AS tipoProyecto','tipoProyecto.id','=','proyectos.idTipoProyecto')
					->leftjoin('catalogoCoberturas AS cobertura','cobertura.id','=','proyectos.idCobertura')
					->leftjoin('evaluacionProyectoObservaciones AS observaciones',function($join)use($mes){
						$join->on('observaciones.idProyecto','=','proyectos.id')
							->where('observaciones.mes','=',$mes)
							->whereNull('observaciones.borradoAl');
					})
					->select('proyectos.*',
						'unidadResponsable.descripcion AS unidadResponsableDescripcion',
						'tipoProyecto.descripcion AS tipoProyectoDescripcion',
						'cobertura.descripcion AS coberturaDescripcion', 'observaciones.observaciones'
					)
					->where('ejercicio',$ejercicio)
					->orderBy('proyectos.finalidad','asc')
					->orderBy('proyectos.funcion','asc')
					->orderBy('proyectos.subFuncion','asc')
					->orderBy('proyectos.subSubFuncion','asc')
					->orderBy('proyectos.unidadResponsable','asc')
					->orderBy('proyectos.programaSectorial','asc')
					->orderBy('proyectos.origenAsignacion','asc')
					->orderBy('proyectos.actividadInstitucional','asc')
					->orderBy('proyectos.proyectoEstrategico','asc')
					->orderBy('proyectos.numeroProyectoEstrategico','asc');
		/*if($fuente_financiamiento){
			$query = $query->join('cargadatosep01 as ep01',function($join)use($fuente_financiamiento){
						$join->on('ep01.UR','=','proyectos.unidadResponsable')
								->where('ep01.FI','=','proyectos.finalidad')
								->where('ep01.FU','=','proyectos.funcion')
								->where('ep01.SF','=','proyectos.subfuncion')
								->where('ep01.SSF','=','proyectos.subsubfuncion')
								->where('ep01.PS','=','proyectos.programaSectorial')
								->where('ep01.PP','=','proyectos.programaPresupuestario')
								->where('ep01.OA','=','proyectos.origenAsignacion')
								->where('ep01.AI','=','proyectos.actividadInstitucional')
								->where('ep01.PT','=',DB::raw('concat(proyectos.proyectoEstrategico,LPAD(proyectos.numeroProyectoEstrategico,3,"0"))'));
					});
		}*/
		return $query;
	}

	public function scopeContenidoCompleto($query){
		return $query->with('componentes','beneficiarios','municipio','region','clasificacionProyecto','tipoProyecto','cobertura','tipoAccion',
			'datosUnidadResponsable','datosFinalidad','datosFuncion','datosSubFuncion','datosSubSubFuncion','datosProgramaSectorial',
			'datosProgramaPresupuestario','datosOrigenAsignacion','datosActividadInstitucional','datosProyectoEstrategico',
			'objetivoPed','estatusProyecto','jefeInmediato','liderProyecto','jefePlaneacion','coordinadorGrupoEstrategico','responsableInformacion');
	}

	public function conteoPlanesMejora(){
		return $this->hasMany('EvaluacionPlanMejora','idProyecto')->conteo();
	}

	public function beneficiarios(){
		return $this->hasMany('Beneficiario','idProyecto')->with('tipoBeneficiario')->orderBy('id');
	}

	public function beneficiariosDescripcion(){
		return $this->hasMany('Beneficiario','idProyecto')->conDescripcion()->orderBy('id');
	}

	public function componentes(){
		return $this->hasMany('Componente','idProyecto')->with('usuario');
	}

	public function componentesMetasMes(){
		return $this->hasMany('ComponenteMetaMes','idProyecto');
	}

	public function componentesCompletoDescripcion(){
		return $this->hasMany('Componente','idProyecto')->conDescripcion()->with('desgloseCompleto','actividadesDescripcion.metasMes');
	}
	
	public function componentesDescripcion(){
		return $this->hasMany('Componente','idProyecto')->conDescripcion()->with('actividadesDescripcion.metasMes');
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
	
	public function fichasTecnicasEvaluacion(){
    	return $this->hasMany('FichaTecnicaEvaluacion','idProyecto');
    }

	public function jefeInmediato(){
		//return $this->belongsTo('Titular','idJefeInmediato')->withTrashed();
		return $this->belongsTo('Directorio','idJefeInmediato')->withTrashed();
	}

	public function liderProyecto(){
		//return $this->belongsTo('Titular','idLiderProyecto')->withTrashed();
		return $this->belongsTo('Directorio','idLiderProyecto')->withTrashed();
	}

	public function jefePlaneacion(){
		//return $this->belongsTo('Titular','idJefePlaneacion')->withTrashed();
		return $this->belongsTo('Directorio','idJefePlaneacion')->withTrashed();
	}

	public function coordinadorGrupoEstrategico(){
		//return $this->belongsTo('Titular','idCoordinadorGrupoEstrategico')->withTrashed();
		return $this->belongsTo('Directorio','idCoordinadorGrupoEstrategico')->withTrashed();
	}

	public function responsableInformacion(){
		return $this->belongsTo('Directorio','idResponsable')->withTrashed();
	}

	public function actividades(){
		return $this->hasMany('Actividad','idProyecto')->with('usuario');
	}

	public function actividadesMetasMes(){
		return $this->hasMany('ActividadMetaMes','idProyecto');
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

	public function datosOrigenAsignacion(){
		return $this->belongsTo('OrigenAsignacion','origenAsignacion','clave');
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

	public function estrategiaNacional(){
		return $this->belongsTo('EstrategiaNacional','idEstrategiaNacional');
	}

	public function objetivoEstrategico(){
		return $this->belongsTo('Estrategia','idObjetivoEstrategico');
		//return $this->belongsTo('ObjetivoEstrategico','idObjetivoEstrategico');
	}

	public function estrategiaEstatal(){
		return $this->belongsTo('EstrategiaEstatal','idEstrategiaEstatal');
	}

	public function archivosNormatividad(){
		return $this->hasMany('ControlArchivos','idPadre')->where('claveGrupo','NORM-PROY');
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

	public function evaluacionMes(){
		return $this->hasOne('EvaluacionProyectoMes','idProyecto');
	}

	public function evaluacionProyectoObservacion(){
		return $this->hasOne('EvaluacionProyectoObservacion','idProyecto');
	}

	public function proyectoEvaluacion(){
		return $this->hasOne('ProyectoEvaluacion','idProyecto');
	}

	public function fuentesFinanciamiento(){
        return $this->hasMany('ProyectoFinanciamiento','idProyecto');
    }

    public function programa(){
    	return $this->hasOne('Programa','id','idPrograma');
    }

    public function observaciones(){
    	return $this->hasMany('ObservacionRendicionCuenta','idProyecto');
    }
}