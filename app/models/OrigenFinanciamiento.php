<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class OrigenFinanciamiento extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoOrigenesFinanciamiento";
}