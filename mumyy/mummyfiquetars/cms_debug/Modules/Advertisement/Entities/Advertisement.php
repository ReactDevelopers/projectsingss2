<?php namespace Modules\Advertisement\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Advertisement extends Model
{
    // use Translatable;
	use MediaRelation;
    use SoftDeletes;
	use AuditTrailModelTrait;
    const DELETED_AT = 'is_deleted';

    public $timestamps = false;
    protected $table = 'mm__advs_items';
    public $translatedAttributes = [];
    protected $fillable = [
    	'adv_id',
		'media',
		'media_thumb',
		'type',
		'sorts',
		'total_click',
		'status',
		'is_deleted',
		'dimension',
		'title',
		'description',
        'link',
        'by',
    ];

    public function advType(){
        return $this->belongsTo('Modules\Advertisement\Entities\AdvertisementType', 'adv_id');
    }

    /**
     * Get thumbnail image
     * @param  string $thumbnailName
     * @return url
     *
     * Supported sizes are smallThumb, mediumThumb, largeThumb
     */
    public function getThumbnailImage($zone = 'image', $thumbnailName = 'original')
    {
    	if(substr($this->media,0,4) == 'http') return $this->media;
    	// using MediaRelation
    	$file = $this->getMediaByZone($zone);
    
    	if (isset($file) && !empty($file)) {
    		// get image uri
    		if ($thumbnailName == 'original') {
    			return $file->path;
    		}
    		return \Imagy::getThumbnail($file->path, $thumbnailName);
    	}
    
    	$noImage = '/assets/media/no-image.png';

    	return url().$noImage;
    }

    /**
     * Get Media by zone name
     * @param  string $zone
     * @return Model
     */
    protected function getMediaByZone($zone = '')
    {
    	$file = $this->files()->where('zone',$zone)->first();
    
    	return $file;
    }
}
