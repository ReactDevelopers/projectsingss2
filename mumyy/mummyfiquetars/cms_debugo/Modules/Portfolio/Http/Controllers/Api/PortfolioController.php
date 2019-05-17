<?php

namespace Modules\Portfolio\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Modules\Media\Http\Requests\UploadMediaRequest;
use Modules\Media\Image\Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;
use Modules\Media\Services\MediaService;
use Modules\Portfolio\Repositories\PortfolioRepository;
use Modules\Portfolio\Repositories\PortfolioMediaRepository;
use Modules\Portfolio\Services\PortfolioService;

class PortfolioController extends Controller
{

    /**
     *
     * @var PortfolioService
     */
    private $portfolioService;

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     *
     * @var MediaService
     */
    private $mediaService;

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

    public function __construct(PortfolioService $portfolioService, PortfolioMediaRepository $portfolioMediaRepository, FileService $fileService, FileRepository $file, Imagy $imagy, MediaService $mediaService)
    {
        $this->portfolioService = $portfolioService;
        $this->portfolioMediaRepository = $portfolioMediaRepository;
        $this->fileService = $fileService;
        $this->file = $file;
        $this->imagy = $imagy;
        $this->mediaService = $mediaService;
    }

    public function all()
    {
        $files = $this->file->all();

        return [
            'count' => $files->count(),
            'data' => $files
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UploadMediaRequest $request
     * @return Response
     */
    public function store(UploadMediaRequest $request)
    {
        $savedFile = $this->fileService->store($request->file('file'));

        if (is_string($savedFile)) {
            return Response::json([
                'error' => $savedFile
            ], 409);
        }

        event(new FileWasUploaded($savedFile));

        return Response::json($savedFile->toArray());
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
        $file = $this->file->find($mediaId);

        $dimension = "";
        if (str_contains($file->mimetype, 'video')) {
            $path = getPathImage($file);
            $pathThumb = getPathThumbImage($file);
            $thumbnailPath = $file->path->getRelativeUrl();
            $mediaType = 'VIDEO';
        } else {
            $path = getPathImage($file);
            $pathThumb = getPathThumbImage($file, 'largeThumb');
            $pathResizeThumb = getPathThumbImage($file, 'resizeThumb');
            $imageInfo = getimagesize(convertLinkS3ToHttp($this->mediaService->getImage($pathResizeThumb)));
            $dimension = json_encode(array('width' => $imageInfo[0], 'height' => $imageInfo[1]));
            $thumbnailPath = $this->imagy->getThumbnail($file->path, 'mediumThumb');
            $mediaType = 'IMAGE';
        }

        // event(new FileWasLinked($file, $entity));
        $data = [
            'portfolio_id' => $entityId,
            'media_url' => $path,
            'media_url_thumb' => $pathThumb,
            'photo_resize' => $pathResizeThumb,
            'media_type' => $mediaType,
            'media_source' => 'local',
            'dimension' => $dimension,
            'sorts' => $this->portfolioService->getSortMedia($entityId) + 1,
            'status' => 1
        ];

        $this->portfolioService->addMedia($data);

        return Response::json([
            'error' => false,
            'message' => 'The link has been added.',
            'result' => [
                'path' => $thumbnailPath,
                'imageableId' => $entityId,
                'mediaType' => strtolower($mediaType)
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
        // $deleted = DB::table('media__imageables')->whereId($imageableId)->delete();
        $deleted = $this->portfolioService->removeMedia($imageableId);
        if (! $deleted) {
            return Response::json([
                'error' => true,
                'message' => 'The file was not found.'
            ]);
        }

        // event(new FileWasUnlinked($imageableId));

        return Response::json([
            'error' => false,
            'message' => 'The link has been removed.'
        ]);
    }

    /**
     * Sort the record in the media__imageables table for the given array
     * @param Request $request
     */
    public function sortMedia(Request $request)
    {
        $imageableIdArray = $request->get('sortable');

        $order = 1;

        foreach ($imageableIdArray as $id) {
            DB::table('media__imageables')->whereId($id)->update(['order' => $order]);
            $order++;
        }

        return Response::json(['error' => false, 'message' => 'The items have been reorder.']);
    }

    public function updateDimension(){
        $portfolioMedia = $this->portfolioService->all();
        dd($portfolioMedia);
    }
}
