<?php namespace Modules\Banner\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Media\Support\Traits\MediaRelation;

class Banner extends Model
{
    use SoftDeletes;
    use MediaRelation;
    // use Translatable;
    const DELETED_AT = 'is_deleted';

    protected $table = 'mm__banner';
    public $translatedAttributes = [];
    protected $fillable = ['id','title','link','image','type','status','is_deleted','created_at','updated_at'];


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
