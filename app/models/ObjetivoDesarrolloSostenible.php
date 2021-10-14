<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ObjetivoDesarrolloSostenible extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoObjetivosDesarrolloSostenible";
}