<?php namespace Modules\Category\Facades;

class CategoryServiceFacade extends \Illuminate\Support\Facades\Facade
{
	protected static function getFacadeAccessor() 
	{
		return "CategoryService";
	}
}