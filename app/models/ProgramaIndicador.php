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
		      ->join('catalogoDimensionesIndicador AS dimensiones','programaIndicador.idDimensionIndicador','=','dimensiones.id')
			  ->join('catalogoTiposIndicadores AS tipos','tipos.id','=','programaIndicador.idTipoIndicador')
			  ->join('catalogoUnidadesMedida as unidades','unidades.id','=','programaIndicador.idUnidadMedida')
			  ->join('catalogoFormulas as formulas','formulas.id','=','programaIndicador.idFormula')
			  ->join('catalogoFrecuenciasIndicador as frecuencias','frecuencias.id','=','programaIndicador.idFrecuenciaIndicador');
	}

	public function registroAvance(){
		return $this->hasMany('RegistroAvancePrograma','idIndicador');
	}
}