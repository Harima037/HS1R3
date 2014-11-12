<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class UnidadResponsable extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoUnidadesResponsables";
}