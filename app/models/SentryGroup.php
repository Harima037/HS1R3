<?php
use Cartalyst\Sentry\Groups\Eloquent\Group as SentryGroupModel;

class SentryGroup extends SentryGroupModel {
	protected $table = 'sentryGroups';
	protected static $userGroupsPivot = 'sentryUsersGroups';

	const CREATED_AT = 'creadoAl';    
    const UPDATED_AT = 'modificadoAl';
    //const DELETED_AT = 'borradoAl';
}