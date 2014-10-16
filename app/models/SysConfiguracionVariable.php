<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class SysConfiguracionVariable extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "sysConfiguracionVariables";
}