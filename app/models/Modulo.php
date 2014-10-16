<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Modulo extends BaseModel
{
	use SoftDeletingTrait;
    protected $dates = ['borradoAl'];
	protected $table = "modulo";
}