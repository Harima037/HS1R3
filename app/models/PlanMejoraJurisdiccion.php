<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class PlanMejoraJurisdiccion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "planMejoraJurisdiccion";
}