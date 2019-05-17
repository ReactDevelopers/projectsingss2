<?php namespace Modules\Advertisement\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Advertisement\Entities\Advertisement;
use Modules\Advertisement\Entities\AdvertisementType;
use Modules\Advertisement\Repositories\AdvertisementRepository;
use Modules\Advertisement\Services\AdvertisementService;
use Modules\Advertisement\Http\Requests\AdvertisementCreateRequest;
use Modules\Advertisement\Http\Requests\AdvertisementUpdateRequest;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Services\FileService;
use Modules\Media\Repositories\FileRepository;
use URL;

class AdvertisementController extends AdminBaseController
{
    /**
     * @var AdvertisementRepository
     */
    private $advertisement;

    /**
     *
     * @var FileService
     */
    private $fileService;

    /**
     * @var FileRepository
     */
    private $file;

    /**
     * @var AdvertisementService
     */
    private $advertisementService;

    public function __construct(AdvertisementRepository $advertisement, FileRepository $file, AdvertisementService $advertisementService)
    {
        parent::__construct();

        $this->advertisement            = $advertisement;
        $this->file                     = $file;
        $this->advertisementService     = $advertisementService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $types = $this->advertisementService->getTypesForSearching();
        $types = ['' => "All"] + $types;
        // $advertisements = $this->advertisement->all();

        // return view('advertisement::admin.advertisements.index', compact(['advertisements', 'types']));

        $advertisements = $this->advertisementService->getItems('list', $request);
        $count = $this->advertisementService->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $type = $request->get('type');
        $page = $request->get('page');
        $array_field = array('id', 'title', 'type_name', 'status');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $advertisements->count() - 1;

        return view('advertisement::admin.advertisements.index', compact(['advertisements', 'types', 'type','limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $types = $this->advertisementService->getAdvertisementTypeArray();

        return view('advertisement::admin.advertisements.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(AdvertisementCreateRequest $request)
    {
        $this->advertisementService->create($request->all());     

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('advertisement::advertisements.title.advertisements')]));

        return redirect()->route('admin.advertisement.advertisement.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Advertisement $advertisement
     * @return Response
     */
    public function edit(Advertisement $advertisement)
    {
        $types = $this->advertisementService->getAdvertisementTypeArray();

        //inject file to media module view
        $image = $this->file->findFileByZoneForEntity('image', $advertisement);

        $previousUrl = URL::previous();

        return view('advertisement::admin.advertisements.edit', compact(['advertisement', 'image', 'types', 'previousUrl']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Advertisement $advertisement
     * @param  Request $request
     * @return Response
     */
    public function update(Advertisement $advertisement, AdvertisementUpdateRequest $request)
    {
        $this->advertisementService->update($advertisement, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('advertisement::advertisements.title.advertisements')]));

        // return redirect()->route('admin.advertisement.advertisement.index');
        $types = $this->advertisementService->getAdvertisementTypeArray();

        //inject file to media module view
        $image = $this->file->findFileByZoneForEntity('image', $advertisement);

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }
        
        return redirect()->route('admin.advertisement.advertisement.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Advertisement $advertisement
     * @return Response
     */
    public function destroy(Advertisement $advertisement)
    {
        // if($advertisement->adv_id == 3){
        //     flash()->error(trans('core::core.messages.resource deleted failure', ['name' => trans('advertisement::advertisements.title.advertisements')]));
        //     return redirect()->route('admin.advertisement.advertisement.index');
        // }        

        $this->advertisement->destroy($advertisement);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('advertisement::advertisements.title.advertisements')]));
        return redirect()->route('admin.advertisement.advertisement.index');
    }
}
