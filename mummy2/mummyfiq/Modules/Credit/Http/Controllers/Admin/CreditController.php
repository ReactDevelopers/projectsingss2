<?php namespace Modules\Credit\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Credit\Entities\Credit;
use Modules\Vendor\Entities\UserRole;
use Modules\Vendor\Entities\Vendor;
use Modules\Credit\Repositories\CreditRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use DB;
use Modules\Credit\Http\Requests\CreateCreditRequest;
use Modules\Credit\Services\CreditService;
use URL;

class CreditController extends AdminBaseController
{
    /**
     * @var CreditRepository
     */
    private $credit;

    public function __construct(CreditRepository $credit, CreditService $service)
    {
        parent::__construct();

        $this->credit = $credit;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {

        //  $credits = DB::select('
        // SELECT p.vendor_id,p.created_at, SUM(p.point) as Sum_Point
        // FROM mm__vendors_credit AS p
        // GROUP BY p.vendor_id
        // ORDER BY p.created_at DESC');
        //  dd($credits);
        // return view('credit::admin.credits.index', compact('credits'));

        $credits = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('created_at', 'business_name', 'email');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $credits->count() - 1;

        return view('credit::admin.credits.index', compact(['credits', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $vendors = [];
        $arr = UserRole::where('role_id',3)->get();
        if($arr)
        {
            foreach ($arr as $key => $value) {
                $vendor = Vendor::find($value->user_id);
                if($vendor){
                    $vendors += [$key =>$vendor];
                }              
            }
        }
        return view('credit::admin.credits.create',compact('vendors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(CreateCreditRequest $request)
    {
        $credit = Credit::where('vendor_id',$request->vendor_id)->first();
        if(isset($credit) && !empty($credit))
        {
            $credit->point = $credit->point + $request->point;
            $credit->save();
        }
        else
        {
            $this->credit->create($request->all());
        }
        

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('credit::credits.title.credits')]));

        return redirect()->route('admin.credit.credit.index');
    }

    public function show($vendorId)
    {
        $credits = Credit::where('vendor_id',$vendorId)->get();
         return view('credit::admin.credits.show',compact('credits'));
    }
}
