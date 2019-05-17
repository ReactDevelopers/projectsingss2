<?php namespace Modules\Audittrail\Contracts;


interface AuditTrail
{
    public function log($event_name,$data,$options = array());
}