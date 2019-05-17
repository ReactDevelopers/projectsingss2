<?php namespace Modules\Page\Http\Controllers\Admin;

use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Page\Entities\Page;
use Modules\Page\Http\Requests\CreatePageRequest;
use Modules\Page\Http\Requests\UpdatePageRequest;
use Modules\Page\Repositories\PageRepository;
use Modules\Page\Events\PageWasDeleted;
use Modules\Media\Repositories\FileRepository;
use Modules\Page\Services\PageService;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use URL;

class PageController extends AdminBaseController
{
    /**
     * @var PageRepository
     */
    private $page;

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     *
     * @var PageService
     */
    private $service;

    public function __construct(PageRepository $page, FileRepository $file, PageService $service)
    {
        parent::__construct();

        $this->page = $page;
        $this->file = $file;
        $this->service = $service;
        $this->assetPipeline->requireCss('icheck.blue.css');
    }

    public function index(Request $request)
    {
        // $pages = $this->page->all();

        // return view('page::admin.index', compact('pages'));

        $pages = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'title');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $pages->count() - 1;

        return view('page::admin.index', compact(['pages', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->assetPipeline->requireJs('ckeditor.js');

        return view('page::admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreatePageRequest $request
     * @return Response
     */
    public function store(CreatePageRequest $request)
    {
        $this->page->create($request->all());

        flash(trans('page::messages.page created'));

        return redirect()->route('admin.page.page.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Page $page
     * @return Response
     */
    public function edit(Page $page)
    {
        $this->assetPipeline->requireJs('ckeditor.js');

        //inject file to media module view
        $background = $this->file->findFileByZoneForEntity('background', $page->pageTranslation);

        $previousUrl = URL::previous();

        return view('page::admin.edit', compact(['page', 'background', 'previousUrl']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Page $page
     * @param  UpdatePageRequest $request
     * @return Response
     */
    public function update(Page $page, UpdatePageRequest $request)
    {
        $this->page->update($page, $request->all());

        flash(trans('page::messages.page updated'));

        // if ($request->get('button') === 'index') {
        //     return redirect()->route('admin.page.page.index');
        // }

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }

        return redirect()->route('admin.page.page.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Page $page
     * @return Response
     */
    public function destroy(Page $page)
    {
        $this->page->destroy($page);

        flash(trans('page::messages.page deleted'));

        return redirect()->route('admin.page.page.index');
    }
}
