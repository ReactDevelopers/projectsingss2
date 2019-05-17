<?php namespace Modules\Media\Services;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Modules\Media\Events\FileWasLinked;
use Modules\Media\Events\FileWasUnlinked;
use Modules\Media\UrlResolvers\BaseUrlResolver;
use Modules\Media\Image\Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Media\Entities\File;

class MediaService {

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     *
     * @var FileRepository
     */
    private $file;

    /**
     *
     * @var Imagy
     */
    private $imagy;

    public function __construct(FileService $fileService, FileRepository $file, Imagy $imagy)
    {
        $this->fileService = $fileService;
        $this->file = $file;
        $this->imagy = $imagy;
    }

	public function getImage($path){
        if(!$path){
            return '';
        }

        if(filter_var($path, FILTER_VALIDATE_URL)){
            return $path;
        }

        $resolver = new BaseUrlResolver();
        $resolvedPath = $resolver->resolve($path);

        return $resolvedPath;
    }

    /**
     * Link the given entity with a media file
     *
     * @param Request $request
     */
    public function linkMedia(Request $request)
    {
        $mediaId = $request->get('mediaId');
        $entityClass = $request->get('entityClass');
        $entityId = $request->get('entityId');
        $order = $request->get('order');

        $entity = $entityClass::find($entityId);
        $zone = $request->get('zone');
        $entity->files()->attach($mediaId, [
            'imageable_type' => $entityClass,
            'zone' => $zone,
            'order' => $order
        ]);
        $imageable = DB::table('media__imageables')->whereFileId($mediaId)
            ->whereZone($zone)
            ->whereImageableType($entityClass)
            ->first();
        $file = $this->file->find($imageable->file_id);

        if (str_contains($file->mimetype, 'video')) {
            $thumbnailPath = $file->path->getRelativeUrl();
            $mediaType = 'video';
        } else {
            $thumbnailPath = $this->imagy->getThumbnail($file->path, 'mediumThumb');
            $mediaType = 'image';
        }

        event(new FileWasLinked($file, $entity));

        return Response::json([
            'error' => false,
            'message' => 'The link has been added.',
            'result' => [
                'path' => $thumbnailPath,
                'imageableId' => $imageable->id,
                'mediaType' => $mediaType
            ]
        ]);
    }

    /**
     * Remove the record in the media__imageables table for the given id
     *
     * @param Request $request
     */
    public function unlinkMedia(Request $request)
    {
        $imageableId = $request->get('imageableId');
        $deleted = DB::table('media__imageables')->whereId($imageableId)->delete();
        if (! $deleted) {
            return Response::json([
                'error' => true,
                'message' => 'The file was not found.'
            ]);
        }

        event(new FileWasUnlinked($imageableId));

        return Response::json([
            'error' => false,
            'message' => 'The link has been removed.'
        ]);
    }

        /**
     * [getItems description]
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'filename');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';
        if($option == 'list'){
            $items = File::where(function($query) use ($keyword) {
                            $query->where('id','=',$keyword);
                            $query->orWhere('filename', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return File::where(function($query) use ($keyword) {
                            $query->where('id','=',$keyword);
                            $query->orWhere('filename', 'like', '%' . $keyword . '%');
                        })->count();
        }
        
    }
}