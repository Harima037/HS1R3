<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class TipoCaptura extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoTiposCaptura";
}