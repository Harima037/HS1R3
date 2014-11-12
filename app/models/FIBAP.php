<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FIBAP extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibap";
}