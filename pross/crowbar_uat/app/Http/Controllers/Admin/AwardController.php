<?php

namespace App\Http\Controllers\Admin;

    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use Illuminate\Support\Facades\Cookie;
    use Illuminate\Validation\Rule;
    use Yajra\Datatables\Html\Builder;
    use App\Models\Interview as Interview;
    use App\Models\Forum;
    use Auth;
    use Crypt;
    use Illuminate\Pagination\Paginator;
    use Symfony\Component\HttpFoundation\StreamedResponse;

    use Voucherify\VoucherifyClient;
    use Voucherify\ClientException;

    use Illuminate\Support\Facades\Input;
    use Maatwebsite\Excel\Facades\Excel;

class AwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Builder $htmlBuilder)
    {
        $data['page_title'] = 'Group list';
            $data['add_url']    = url(sprintf('%s/group/add',ADMIN_FOLDER));
            
            if ($request->ajax()) {
                $groupList = \Models\Group::getGroupList();
                return \Datatables::of($groupList)                  
                ->editColumn('status',function($groupList){
                return $groupList->status = ucfirst($groupList->status);
                })                    
                ->editColumn('action',function($groupList) use($request){
                    $html = '';
                    $html .= sprintf('<a href="%s" class="btn badge bg-black">Edit</a> ',url(sprintf('%s/group/edit?id=%s',ADMIN_FOLDER,___encrypt($groupList->id))));
                    $html .= '<a href="javascript:void(0);" data-url="'.url(sprintf('%s/group/delete?id=%s',ADMIN_FOLDER,___encrypt($groupList->id))).'" data-request="status" data-ask="Do you really want to continue with this action?" class="badge bg-red" >Delete</a>';
                    return $html;
                })
                ->make(true);
            }
            $data['html'] = $htmlBuilder
            ->addColumn(['data' => 'row_number', 'name' => 'row_number', 'title' => '#','width' => '1'])
            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Group Name'])
            // ->addColumn(['data' => 'status', 'name' => 'status', 'title' => 'Status'])
            ->addColumn(['data' => 'created', 'name' => 'created', 'title' => 'Created Date'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Actions','searchable' => false, 'orderable' => false, 'width' => '120']);
            return view('backend.award.list')->with($data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
