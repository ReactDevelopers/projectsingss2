<?php namespace Modules\Portfolio\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class PortfolioMedia extends Model
{
    use MediaRelation, AuditTrailModelTrait;
    protected $table = 'mm__vendors_portfolio_media';
    public $translatedAttributes = [];
    public $timestamps = false;
    protected $fillable = ['id','portfolio_id','media_url','media_url_thumb','photo_resize','media_type','media_source','sorts','status', 'dimension'];
}
