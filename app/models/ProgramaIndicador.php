<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProgramaIndicador extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "programaIndicador";
	
	public function scopeContenidoDetalle($query){
		$query->select('programaIndicador.*','dimensiones.descripcion as dimensionIndicador','tipos.descripcion as tipoIndicador',
			'unidades.descripcion AS unidadMedida','formulas.descripcion AS formula','frecuencias.descripcion as frecuencia')
		      ->leftjoin('catalogoDimensionesIndicador AS dimensiones','programaIndicador.idDimensionIndicador','=','dimensiones.id')
			  ->leftjoin('catalogoTiposIndicadores AS tipos','tipos.id','=','programaIndicador.idTipoIndicador')
			  ->leftjoin('catalogoUnidadesMedida as unidades','unidades.id','=','programaIndicador.idUnidadMedida')
			  ->leftjoin('catalogoFormulas as formulas','formulas.id','=','programaIndicador.idFormula')
			  ->leftjoin('catalogoFrecuenciasIndicador as frecuencias','frecuencias.id','=','programaIndicador.idFrecuenciaIndicador');
	}

	public function registroAvance(){
		return $this->hasMany('RegistroAvancePrograma','idIndicador');
	}
}