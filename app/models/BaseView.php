<?php
abstract class BaseView extends Eloquent {
/**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'modificadoAl';



    /**
     * The name of the "deleted at" column.
     *
     * @var string
     */
    const DELETED_AT = 'borradoAl';

    /**
     * Set the update and creation timestamps on the model.
     */

    public static function boot(){
        parent::boot();

        static::creating(function($item){
            if(Sentry::check()){
               // $item->creadoPor = Sentry::getUser()->id;
              //  $item->actualizadoPor = Sentry::getUser()->id;
            }
        });

        static::updating(function($item){
            if(Sentry::check()){
               // $item->actualizadoPor = Sentry::getUser()->id;
            }
        });
    }
}