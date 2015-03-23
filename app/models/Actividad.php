<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Actividad extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteActividades";

	public function scopeContenidoCompleto($query){
		return $query->with('formula','dimension','frecuencia','tipoIndicador','unidadMedida');
	}

	public function registroAvance(){
    	return $this->hasMany('RegistroAvanceMetas','idNivel')->where('nivel','=',2);
    }

    public function planMejora(){
    	return $this->hasMany('EvaluacionPlanMejora','idNivel')->where('nivel','=',2);
    }

	public function metasMes(){
		return $this->hasMany('ActividadMetaMes','idActividad');
	}

	public function metasMesAgrupado(){
		return $this->hasMany('ActividadMetaMes','idActividad')->agrupadoMes();
	}

	public function metasMesJurisdiccion(){
		return $this->hasMany('ActividadMetaMes','idActividad')->agrupadoJurisdiccion();
	}

	public function usuario(){
		return $this->belongsTo('SentryUser','actualizadoPor');
	}

	public function formula(){
		return $this->belongsTo('Formula','idFormula');
	}

	public function dimension(){
		return $this->belongsTo('Dimension','idDimensionIndicador');
	}

	public function frecuencia(){
		return $this->belongsTo('Frecuencia','idFrecuenciaIndicador');
	}

	public function tipoIndicador(){
		return $this->belongsTo('TipoIndicador','idTipoIndicador');
	}

	public function unidadMedida(){
		return $this->belongsTo('UnidadMedida','idUnidadMedida');
	}
	public function comentarios(){
    	return $this->hasMany('EvaluacionComentario','idElemento')->where('tipoElemento','=',3);
    }
}