<?php namespace Modules\Media\Http\Controllers\Admin;

use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Image\ThumbnailsManager;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\MediaService;
use Illuminate\Http\Request;

class MediaGridController extends AdminBaseController
{
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var ThumbnailsManager
     */
    private $thumbnailsManager;
    /**
     * @var MediaService
     */
    private $service;

    public function __construct(FileRepository $file, ThumbnailsManager $thumbnailsManager, MediaService $service)
    {
        parent::__construct();

        $this->file = $file;
        $this->thumbnailsManager = $thumbnailsManager;
        $this->service = $service;
    }

    /**
     * A grid view for the upload button
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // $files = $this->file->all();
        $files = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $thumbnails = $this->thumbnailsManager->all();

        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'filename');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $files->count() - 1;

        return view('media::admin.grid.general', compact('files', 'count', 'thumbnails', 'limit', 'keyword', 'page', 'order_field', 'sort', 'start', 'offset'));
    }

    /**
     * A grid view of uploaded files used for the wysiwyg editor
     * @return \Illuminate\View\View
     */
    public function ckIndex()
    {
        $files = $this->file->all();
        $thumbnails = $this->thumbnailsManager->all();

        return view('media::admin.grid.ckeditor', compact('files', 'thumbnails'));
    }
}
