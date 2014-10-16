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
}