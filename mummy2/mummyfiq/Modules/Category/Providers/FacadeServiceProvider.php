<?php namespace Modules\Category\Providers;

use Modules\Category\Services\CategoryService;

class FacadeServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function register()
	{
		\App::bind('CategoryService', function () {
			return app(CategoryService::class);
		});
	}
}