<?php
use Cartalyst\Sentry\Users\Eloquent\User as SentryModel;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class SentryUser extends SentryModel {
	use SoftDeletingTrait;
	
	protected $table = 'sentryUsers';
	protected static $userGroupsPivot = 'sentryUsersGroups';
    protected $dates = ['borradoAl'];


    const CREATED_AT = 'creadoAl';    
    const UPDATED_AT = 'modificadoAl';
    const DELETED_AT = 'borradoAl';

    public function nombreCompleto(){
    	return $this->nombres.' '.$this->apellidoPaterno.' '.$this->apellidoMaterno;
    }

    public function proyectosAsignados(){
        return $this->hasOne('UsuarioProyecto','idSentryUser');
    }

    public function caratulas(){
        return $this->hasMany('Proyecto','idUsuarioCaptura');
    }

    public function proyectos(){
        if($this->idDepartamento == 2){
            return $this->hasMany('Proyecto','idUsuarioValidacionSeg');
        }else{
            return $this->hasMany('Proyecto','idUsuarioRendCuenta');
        }
    }
    
    public function programas(){
        if($this->idDepartamento == 2){
            return $this->hasMany('Programa','idUsuarioValidacionSeg')->contenidoSuggester();
        }else{
            return $this->hasMany('Programa','idUsuarioRendCuenta')->contenidoSuggester();
        }
    }

    public function scopeUsuariosProyectos($query){
        return $query->select('sentryUsers.id','sentryUsers.idDepartamento','usuariosProyectos.proyectos','usuariosProyectos.ejercicio')
                    ->leftjoin('usuariosProyectos','usuariosProyectos.idSentryUser','=','sentryUsers.id');
    }
}