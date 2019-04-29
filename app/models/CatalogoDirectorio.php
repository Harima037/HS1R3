<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class CatalogoDirectorio extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogosSSA.Directorio";
}