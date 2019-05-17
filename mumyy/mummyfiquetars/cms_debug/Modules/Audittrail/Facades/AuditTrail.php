<?php namespace Modules\Audittrail\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * User: nguyentantam
 * Date: 9/3/15
 * Time: 10:48 AM
 */
class AuditTrail extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'audit-trail'; // the IoC binding.
    }
}