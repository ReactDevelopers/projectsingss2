<?php

namespace Modules\Vendor\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Vendor\Services\VendorService;

class VendorController extends Controller
{
    /**
     * @var vendorService
     */
    private $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function getStates(Request $request){
        $data = $request->all();
        $response = [];
        if($data){
            $id = $data['country_id'];

            $results = $this->vendorService->getStateArray($id);
            $first_state_id = $results ? array_keys($results)[0] : '';
            $cities = $results ? $this->vendorService->getCityArray($first_state_id) : [];
            $response = [
                    'id' => $id,
                    'data' => $results,
                    'dataCities' => $cities,
                    'state_id' => $first_state_id,
            ];
        }   
        return response()->json($response);
    }

    public function getCities(Request $request){
        $data = $request->all();
        $response = [];
        if($data){
            $id = $data['country_id'];
            
            if(isset($data['vendor_id']) && !empty($data['vendor_id'])){
                $vendor_id = $data['vendor_id'];
                $vendor = $this->vendorService->findBy('id', $vendor_id);

                $results = $this->vendorService->getVendorCitiesArr($vendor, $id);
                $locationDetail = $this->vendorService->getVendorLocation($vendor_id, key($results));
                $response = [
                        'id' => $id,
                        'data' => [
                            'cities' => $results,
                            'phonecode' => $this->vendorService->getPhonecodeByCountry($id),
                            'zip_code' => $locationDetail->zip_code,
                            'business_phone' => $locationDetail->business_phone,
                            'phonecodes' => $this->vendorService->getVendorPhonecodesArr($vendor),
                        ],    
                ];
            }else{
                $results = $this->vendorService->getCitiesArr($id);
                $response = [
                        'id' => $id,
                        'data' => [
                            'cities' => $results,
                            'phonecode' => $this->vendorService->getPhonecodeByCountry($id),
                            'phonecodes' => $this->vendorService->getPhonecodesAjax(),
                        ],    
                ];
            }
            
        }        
        return response()->json($response);
    }

    public function getVendors(Request $request){

        $data = $request->all();
        $query = $data && isset($data['q']) ?  $data['q'] : "";
        $page = $data && isset($data['page']) ?  $data['page'] : 1;

        $limit = 10;
        $offset = ($page - 1) * $limit;

        $response['items'] = $this->vendorService->getList('list', $query, 'business_name', 'ASC', $limit, $offset);
        if(!$response['items']){
            $response['incomplete_results'] = false;
            $response['items'] = [];
        }

        $response['total_count'] = $this->vendorService->getList('total_count', $query, 'first_name', 'ASC', $limit, $offset);
        return response()->json($response);
    }

    public function getCategories(Request $request){
        $data = $request->all();
        $vendor_id = $data['vendor_id'];

        $categories = $this->vendorService->getVendorCategoryArray($vendor_id);
        $response = [
                'vendor_id' => $vendor_id,
                'data' => $categories
        ];
        // return Response::json($response);
        return response()->json($response);
    }

    public function getVendorDatatable(Request $request){
        // // DB table to use
        // $table = 'users';
        // // Table's primary key
        // $primaryKey = 'id';
        // // Array of database columns which should be read and sent back to DataTables.
        // // The `db` parameter represents the column name in the database, while the `dt`
        // // parameter represents the DataTables column identifier. In this case simple
        // // indexes
        // $columns = array(
        //     array( 'db' => 'first_name', 'dt' => 0 ),
        //     array( 'db' => 'last_name',  'dt' => 1 ),
        //     array( 'db' => 'email',   'dt' => 2 ),
        //     // array( 'db' => 'office',     'dt' => 3 ),
        //     // array(
        //     //     'db'        => 'start_date',
        //     //     'dt'        => 4,
        //     //     'formatter' => function( $d, $row ) {
        //     //         return date( 'jS M y', strtotime($d));
        //     //     }
        //     // ),
        //     // array(
        //     //     'db'        => 'salary',
        //     //     'dt'        => 5,
        //     //     'formatter' => function( $d, $row ) {
        //     //         return '$'.number_format($d);
        //     //     }
        //     // )
        // );
        // // SQL server connection information
        // $sql_details = array(
        //     'user' => ENV('DB_USERNAME'),
        //     'pass' => ENV('DB_PASSWORD'),
        //     'db'   => ENV('DB_DATABASE'),
        //     'host' => ENV('DB_HOST'),
        // );
        // /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
        //  * If you just want to use the basic configuration for DataTables with PHP
        //  * server-side, there is no need to edit below this line.
        //  */
        // // require( 'ssp.class.php' );
        // // echo json_encode(
        // //     SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
        // // );
        
        // // $response = SSP::simple( $request->all(), $sql_details, $table, $primaryKey, $columns );
        // $response = abc();//SSP::test();
        // return response()->json($response); 
        
        $count = $this->vendorService->getList('total_count');

        $data = $this->vendorService->getList(false, false, 'id', 'desc');
        $data = $this->vendorService->transform($data);
        $response = [
            "draw" => rand(), 
            "recordsTotal"=> $count, 
            "recordsFiltered"=> $count, 
            "data"=> $data,
        ];
       return response()->json($response); 
    }

    public function getVendorLocation(Request $request){
        $data = $request->all();
        $response = [];
        if($data){
            $id = $data['city_id'];
            $vendor_id = $data['vendor_id'];
            $vendor = $this->vendorService->findBy('id', $vendor_id);

            $locationDetail = $this->vendorService->getVendorLocation($vendor_id, $id);
            $vendorPhones = $this->vendorService->getVendorPhonesArr($locationDetail->id, $vendor_id);
            $phonecodesArr = $this->vendorService->getVendorPhonecodesAjax($vendor);
            $response = [
                    'id' => $id,
                    'data' => [
                        'zip_code' => $locationDetail->zip_code,
                        'phonecode' => $locationDetail->country->phonecode,
                        'phonecodes' => $phonecodesArr,
                        'business_phone' => $vendorPhones, 
                    ],    
            ];
        }        
        return response()->json($response);
    }

    public function updateVendorCredit(){
        $this->vendorService->updateVendorCredit();

        flash()->success(trans('vendor::vendors.messages.update credit success', ['name' => trans('vendor::vendors.title.vendors')]));
        
        return redirect()->route('admin.vendor.vendor.index');
    }

    public function getLocationPhonecode(Request $request){
        $country_id = $request->country_id;

        $country = $this->vendorService->getCountryById($country_id);
        if(count($country)){
            $response = [
                'status' => true,
                'data' => $country->phonecode,
            ];
        }
        else{
            $response = [
                'status' => false,
                'message' => 'country not found',
            ];
        }
        return response()->json($response);
    }
}
