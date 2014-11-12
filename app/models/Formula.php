<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Formula extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoFormulas";
}