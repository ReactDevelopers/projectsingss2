<?php namespace Modules\Media\Providers;

use Modules\Media\Services\MediaService;

class FacadeServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function register()
	{
		\App::bind('MediaService', function () {
			return app(MediaService::class);
		});
	}
}