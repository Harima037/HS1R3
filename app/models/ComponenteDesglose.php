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

	public function metasMesAcumuladas(){
		return $this->hasOne('DesgloseMetasMes','idComponenteDesglose')->acumulado();
	}

	public function beneficiarios(){
		return $this->hasMany('DesgloseBeneficiario','idComponenteDesglose');
	}

	public function beneficiariosDescripcion(){
		return $this->hasMany('DesgloseBeneficiario','idComponenteDesglose')->conDescripcion();
	}

	public function scopeListarMunicipios($query){
		$query->select('componenteDesglose.id','componenteDesglose.idComponente','componenteDesglose.idAccion','componenteDesglose.claveJurisdiccion','municipio.*')
                ->leftjoin('vistaMunicipios AS municipio','municipio.clave','=','componenteDesglose.claveMunicipio')
                ->groupBy('componenteDesglose.claveJurisdiccion','municipio.clave')
                ->orderBy('municipio.nombre','ASC');
	}

	public function scopeListarDatos($query){
		$query->select('componenteDesglose.*','localidad.nombre AS localidad','municipio.nombre AS municipio',
				'jurisdiccion.nombre AS jurisdiccion')
				->leftjoin('vistaJurisdicciones AS jurisdiccion','jurisdiccion.clave','=','claveJurisdiccion')
                ->leftjoin('vistaMunicipios AS municipio','municipio.clave','=','claveMunicipio')
                ->leftjoin('vistaLocalidades AS localidad',function($join){
                    return $join->on('localidad.clave','=','claveLocalidad')
                         ->on('localidad.idMunicipio','=','municipio.id');
                })->orderBy('municipio.nombre','ASC')->orderBy('localidad.nombre','ASC');
	}

	public function municipio(){
    	return $this->belongsTo('Municipio','claveMunicipio','clave');
    }

    public function localidad(){
    	return $this->belongsTo('Localidad','claveLocalidad','clave')->where('idMunicipio',$this->municipio->id);
    }
}