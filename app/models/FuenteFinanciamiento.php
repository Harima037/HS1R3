<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FuenteFinanciamiento extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoFuenteFinanciamiento";
}