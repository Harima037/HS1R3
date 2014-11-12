<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class UnidadMedida extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoUnidadesMedida";
}