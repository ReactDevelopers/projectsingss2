<?php namespace Modules\Customer\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Repositories\CustomerRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Customer\Http\Requests\CustomerCreateRequest;
use Modules\Customer\Http\Requests\CustomerUpdateRequest;
use Modules\User\Repositories\RoleRepository;
use Modules\User\Repositories\UserRepository;
use Modules\Customer\Services\CustomerService;
use URL;
use Modules\Vendor\Repositories\VendorPhoneRepository;
use Maatwebsite\Excel\Facades\Excel;
class CustomerController extends AdminBaseController
{
    /**
     * @var CustomerRepository
     */
    private $customer;
    
    /**
     * @var UserRepository
     */
    private $user;

    /**
     * @var RoleRepository
     */
    private $role;

    /**
     * @var CustomerService
     */
    private $customerService;

    /**
     * @var VendorPhoneRepository
     */
    private $vendorPhoneRepository;

    public function __construct(CustomerRepository $customer, UserRepository $user, RoleRepository $role, CustomerService $customerService, VendorPhoneRepository $vendorPhoneRepository )
    {
        parent::__construct();

        $this->customer = $customer;
        $this->customerService = $customerService;
        $this->vendorPhoneRepository = $vendorPhoneRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // $customers = $this->customerService->all();

        // return view('customer::admin.customers.index', compact('customers'));

        $customers = $this->customerService->getItems('list', $request);
        $count = $this->customerService->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'first_name', 'email', 'status', 'created_at','last_login');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $customers->count() - 1;

        return view('customer::admin.customers.index', compact(['customers', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('customer::admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(CustomerCreateRequest $request)
    {
        $this->customerService->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('customer::customers.title.customers')]));

        return redirect()->route('admin.customer.customer.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Customer $customer
     * @return Response
     */
    public function edit(Customer $customer)
    {
        $phone = $this->vendorPhoneRepository->findByAttributes(['user_id' => $customer->id]);

        $previousUrl = URL::previous();
        return view('customer::admin.customers.edit', compact(['customer', 'previousUrl', 'phone']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Customer $customer
     * @param  Request $request
     * @return Response
     */
    public function update(Customer $customer, CustomerUpdateRequest $request)
    {
        $this->customerService->update($customer, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('customer::customers.title.customers')]));

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }

        return redirect()->route('admin.customer.customer.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Customer $customer
     * @return Response
     */
    public function destroy(Customer $customer)
    {
        $this->customerService->destroy($customer);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('customer::customers.title.customers')]));

        return redirect()->route('admin.customer.customer.index');
    }

    /**
     * Export Customer Data List
     *
     * @param  Customer $customer
     * @return Response
     */

    public function getExportCustomer(Request $request){
        $customers = $this->customerService->all();
        Excel::create('Customers', function($excel) use ($customers){
            $excel->sheet('Sheet 1', function($sheet) use ($customers){
                $sheet->loadView('customer::admin.customers.export.customer',compact('customers'));
            });
        })->export('csv');
    }
}
