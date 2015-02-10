<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ComponenteDesglose extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteDesglose";

	public function metasMes(){
		return $this->hasMany('DesgloseMetasMes','idComponenteDesglose');
	}

	public function scopeListarDatos($query){
		$query->select('componenteDesglose.*','localidad.nombre AS localidad','municipio.nombre AS municipio',
				'jurisdiccion.nombre AS jurisdiccion')
				->leftjoin('vistaJurisdicciones AS jurisdiccion','jurisdiccion.clave','=','claveJurisdiccion')
                ->leftjoin('vistaMunicipios AS municipio','municipio.clave','=','claveMunicipio')
                ->leftjoin('vistaLocalidades AS localidad',function($join){
                    return $join->on('localidad.clave','=','claveLocalidad')
                         ->on('localidad.idMunicipio','=','municipio.id');
                });
	}

	public function municipio(){
    	return $this->belongsTo('Municipio','claveMunicipio','clave');
    }

    public function localidad(){
    	return $this->belongsTo('Localidad','claveLocalidad','clave')->where('idMunicipio',$this->municipio->id);
    }
}