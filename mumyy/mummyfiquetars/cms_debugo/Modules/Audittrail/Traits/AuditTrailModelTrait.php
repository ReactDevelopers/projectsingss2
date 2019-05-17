<?php
/**
 * Created by PhpStorm.
 * User: nguyentantam
 * Date: 5/6/16
 * Time: 11:06 AM
 */

namespace Modules\Audittrail\Traits;

use AuditTrail;
use Modules\Audittrail\Entities\Log;

trait AuditTrailModelTrait
{
    static function bootAuditTrailModelTrait()
    {
        /*if (static::class == 'Modules\Audittrail\Entities\Log' || \App::runningInConsole()) {
            return false;
        }*/
        // dd(static::updated(function ($model) {
        //     dd($model);
        //     AuditTrail::log('updated', $model);
        // }));
        static::created(function ($model) {
            AuditTrail::log('created', $model);
        });
        static::updated(function ($model) {
            AuditTrail::log('updated', $model);
        });
        static::deleted(function ($model) {
            AuditTrail::log('deleted', $model);
        });

    }
}