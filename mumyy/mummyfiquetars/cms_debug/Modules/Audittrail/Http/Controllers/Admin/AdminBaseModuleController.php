<?php namespace Modules\Audittrail\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use View;

class AdminBaseModuleController extends AdminBaseController
{

    public $module_name;

    public function __construct()
    {
        parent::__construct();
        View::share('hasMultiLanguageTab',false);

        $this->module_name = "audittrail";
    }

}
