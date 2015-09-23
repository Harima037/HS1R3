<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ActividadDesglose extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "actividadDesglose";

	public function metasMes(){
		return $this->hasMany('ActividadDesgloseMetasMes','idActividadDesglose');
	}

	public function metasMesAcumuladas(){
		return $this->hasOne('ActividadDesgloseMetasMes','idActividadDesglose')->acumulado();
	}

	public function beneficiarios(){
		return $this->hasMany('ActividadDesgloseBeneficiario','idActividadDesglose');
	}

	public function beneficiariosDescripcion(){
		return $this->hasMany('ActividadDesgloseBeneficiario','idActividadDesglose')->conDescripcion();
	}

	public function scopeListarMunicipios($query){
		$query->select('actividadDesglose.id','actividadDesglose.idActividad','actividadDesglose.idAccion','actividadDesglose.claveJurisdiccion','municipio.*')
                ->leftjoin('vistaMunicipios AS municipio','municipio.clave','=','actividadDesglose.claveMunicipio')
                ->groupBy('actividadDesglose.claveJurisdiccion','municipio.clave')
                ->orderBy('municipio.nombre','ASC');
	}

	public function scopeListarDatos($query){
		$query->select('actividadDesglose.*','localidad.nombre AS localidad','municipio.nombre AS municipio',
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