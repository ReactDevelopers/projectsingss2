<?php

namespace App\Providers;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Blade::directive('money', function ($expression) {
            $vars = $this->parseMultipleArgs($expression);
            $symbol = (isset($vars[1]) ? $vars[1] : '$');
            $amount = (isset($vars[0]) ? $vars[0] : 0);

            return "<?php echo ($amount < 0) ? '-' . $symbol . number_format(abs($amount), 2) : $symbol . number_format($amount, 2); ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
        if ($this->app->environment() === 'local') {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Parses multiple arguments in a blade directive expression
     *
     * @param  mixed $expression
     *
     * @return Collection
     */
    public static function parseMultipleArgs($expression)
    {
        return collect(explode(',', $expression))->map(function ($item) {
            return trim($item);
        });
    }
}
