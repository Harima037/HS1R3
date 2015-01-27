<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EntregableTipo extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoEntregablesTipos";
}