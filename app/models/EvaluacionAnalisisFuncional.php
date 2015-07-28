<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionAnalisisFuncional extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionAnalisisFuncional";
	
	public function comentarios(){
    	return $this->hasMany('EvaluacionComentario','idElemento')->where('tipoElemento','=',4);
    }
	
    public function scopeCuentaPublica($query,$mes=NULL,$anio=NULL){
    	$query =  $query->join('proyectos','proyectos.id','=','evaluacionAnalisisFuncional.idProyecto')
    				->join('evaluacionProyectoMes',function($join){
    					return $join->on('evaluacionProyectoMes.idProyecto','=','evaluacionAnalisisFuncional.idProyecto')
    								->on('evaluacionProyectoMes.mes','=','evaluacionAnalisisFuncional.mes');
    				})
    				//->select('evaluacionAnalisisFuncional.*','proyectos.nombreTecnico','evaluacionProyectoMes.idEstatus',
    				//'evaluacionProyectoMes.anio','estatus.descripcion AS estatus',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresupuestaria'));
    				->select('evaluacionAnalisisFuncional.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresupuestaria'),
    					'proyectos.nombreTecnico','evaluacionProyectoMes.mes','evaluacionAnalisisFuncional.cuentaPublica',
                        'proyectos.unidadResponsable','proyectos.idUsuarioValidacionSeg');
    	if($mes){
    		$query = $query->where('evaluacionProyectoMes.mes','=',$mes);
    	}

    	if($anio){
    		$query = $query->where('evaluacionProyectoMes.anio','=',$anio);
    	}

    	$query = $query->whereIn('evaluacionProyectoMes.idEstatus',array(4,5));

        $query = $query->orderBy('proyectos.finalidad','asc')
            ->orderBy('proyectos.funcion','asc')
            ->orderBy('proyectos.subFuncion','asc')
            ->orderBy('proyectos.subSubFuncion','asc')
            ->orderBy('proyectos.unidadResponsable','asc')
            ->orderBy('proyectos.programaSectorial','asc')
            ->orderBy('proyectos.programaPresupuestario','asc')
            ->orderBy('proyectos.programaEspecial','asc')
            ->orderBy('proyectos.actividadInstitucional','asc')
            ->orderBy('proyectos.proyectoEstrategico','asc')
            ->orderBy('proyectos.numeroProyectoEstrategico','asc')
            ->orderBy('proyectos.idClasificacionProyecto','asc');

    	return $query;
    }
}