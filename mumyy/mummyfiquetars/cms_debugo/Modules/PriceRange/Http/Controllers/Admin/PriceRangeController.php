<?php namespace Modules\PriceRange\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\PriceRange\Entities\PriceRange;
use Modules\PriceRange\Repositories\PriceRangeRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\PriceRange\Http\Requests\PriceRangeCreateRequest;
use Modules\PriceRange\Http\Requests\PriceRangeUpdateRequest;
use Modules\PriceRange\Services\PriceRangeService;
use URL;

class PriceRangeController extends AdminBaseController
{
    /**
     * @var PriceRangeRepository
     */
    private $pricerange;

    /**
     * @var PriceRangeService
     */
    private $service;

    public function __construct(PriceRangeRepository $pricerange, PriceRangeService $service)
    {
        parent::__construct();

        $this->pricerange = $pricerange;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $priceranges = PriceRange::all();

        // return view('pricerange::admin.priceranges.index', compact('priceranges'));

        $priceranges = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'price_name');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $priceranges->count() - 1;

        return view('pricerange::admin.priceranges.index', compact(['priceranges', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('pricerange::admin.priceranges.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(PriceRangeCreateRequest $request)
    {
        $this->pricerange->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('pricerange::priceranges.title.priceranges')]));

        return redirect()->route('admin.pricerange.pricerange.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  PriceRange $pricerange
     * @return Response
     */
    public function edit(PriceRange $pricerange)
    {
        $previousUrl = URL::previous();

        return view('pricerange::admin.priceranges.edit', compact(['pricerange', 'previousUrl']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PriceRange $pricerange
     * @param  Request $request
     * @return Response
     */
    public function update(PriceRange $pricerange, PriceRangeUpdateRequest $request)
    {
        $this->pricerange->update($pricerange, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('pricerange::priceranges.title.priceranges')]));

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }
        
        return redirect()->route('admin.pricerange.pricerange.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PriceRange $pricerange
     * @return Response
     */
    public function destroy(PriceRange $pricerange)
    {
        $this->pricerange->destroy($pricerange);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('pricerange::priceranges.title.priceranges')]));

        return redirect()->route('admin.pricerange.pricerange.index');
    }
}
