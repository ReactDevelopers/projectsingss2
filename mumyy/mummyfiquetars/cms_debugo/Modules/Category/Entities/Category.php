<?php namespace Modules\Category\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Audittrail\Traits\AuditTrailModelTrait;

class Category extends Model
{
    // use Translatable;
    use MediaRelation;
    use SoftDeletes, AuditTrailModelTrait;
    const DELETED_AT = 'is_deleted';

    protected $table = 'mm__categories';
    public $timestamps = false;
    public $translatedAttributes = [];
    protected $fillable = [
    	'name',
		'description',
		'sorts',
		'country_id',
		'status',
        'is_deleted',
		'photo',
    ];

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
