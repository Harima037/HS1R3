<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class SubFuenteFinanciamiento extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoSubFuenteFinanciamiento";
}