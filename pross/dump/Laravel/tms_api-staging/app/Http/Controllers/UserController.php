<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Excel;
use App\Lib\General;
use App\Lib\DataVerify\SupervisorDataVerify;
use App\Lib\DataVerify\FilesHeaderVerify;

class UserController extends Controller
{
    public function index(Request $request) {

        $users = User::select([
            'users.id as id',
            \DB::raw('IFNULL(roles.title,"N/A") as role_name'),
            'users.role_id',
            \DB::raw( 'IF(users.deleted_at is NULL, users.name, CONCAT(users.name, " (inactive)")) as name'),
            'users.email',
            'users.designation',
            'supervisors.name as supervisor_name',
            'users.supervisor_personnel_number',
            'departments.dept_name as user_dept_name',
            'users.personnel_number',
            'users.division',
            'users.branch',
            'users.section'
        ])
        ->leftJoin('roles','roles.id','=','users.role_id')
        ->leftJoin('departments','departments.id','users.department_id')
        ->withTrashed()
        ->leftJoin('users as supervisors','supervisors.personnel_number','users.supervisor_personnel_number');

        /** start custom filters **/
        if($request->has('customFilters')) {            

            $general = new General();
            $keys = [
                'name' => 'users.name',
                'email' => 'users.email',
                'designation' =>   'users.designation',
                'division' =>   'users.division',
                'role_name' => 'users.role_id',
                'prog_category_name' => 'pc.prog_category_name',
                'personnel_number' => 'users.personnel_number',
                'supervisor_personnel_number' => 'users.supervisor_personnel_number',
                'supervisor_name' => 'supervisors.name',
                'user_dept_name' => 'departments.id'
            ];

            $general->applyFilters($users, $request->customFilters, $keys);
        }

        

        /** start sorting **/
        if($request->has('sortName')):
            switch ($request->sortName) {
                case 'name':
                    $users->orderBy('users.name',$request->sortOrder)->orderBy('users.id','DESC');
                    break; 
                case 'email':
                    $users->orderBy('users.email',$request->sortOrder);
                    break; 
                case 'personnel_number':
                    $users->orderBy('users.personnel_number',$request->sortOrder);   
                    break;
                case 'supervisor_personnel_number':
                    $users->orderBy('users.supervisor_personnel_number',$request->sortOrder); 
                    break;
                case 'supervisor_name':
                    $users->orderBy('supervisors.name',$request->sortOrder); 
                    break;
                case 'user_dept_name':
                    $users->orderBy('departments.dept_name',$request->sortOrder); 
                    break;
                case 'division':
                    $users->orderBy('users.division',$request->sortOrder); 
                    break;
                
                case 'role_name':
                    $users->orderBy(\DB::raw('CAST(roles.title AS CHAR)'),$request->sortOrder); 
                    break;
                //
                default:
                    # code...
                    break;
            }
        endif;
        /** end sorting **/

        if($request->get('export')) {
            /** if selected record **/
            if($request->has('selected')):
                $users->whereIn('users.id', $request->get('selected'));
            endif;
            /** selected record **/

            $data = $users->get();

            General::downloadExcel(\Excel::create('Users', function($excel) use ($data) {
                $excel->sheet('Users DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Per ID');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Sup ID');});
                    //$sheet->cell('A1', function($cell) {$cell->setValue('Personnel Number');});
                    // $sheet->cell('B1', function($cell) {$cell->setValue('Officer Name');});
                    // $sheet->cell('C1', function($cell) {$cell->setValue('Email');});
                    // $sheet->cell('D1', function($cell) {$cell->setValue('Department');});
                    // $sheet->cell('E1', function($cell) {$cell->setValue('Designation');});
                    // $sheet->cell('F1', function($cell) {$cell->setValue('Division');});
                    // $sheet->cell('G1', function($cell) {$cell->setValue('Branch');});
                    // $sheet->cell('H1', function($cell) {$cell->setValue('Section');});
                    // $sheet->cell('I1', function($cell) {$cell->setValue('Role');});
                    // $sheet->cell('J1', function($cell) {$cell->setValue('Supervisor Name');});
                    // $sheet->cell('K1', function($cell) {$cell->setValue('Supervisor Personnel Number');});


                    $sheet->cell('A1:B1', function($cell) {
                           $cell->setFontWeight('bold');
                    });

                    $sheet->cell('A1', function($cell) {
                           $cell->setBackground('#4286f4');
                    });

                    $sheet->cell('A1:B1', function($cell) {
                           $cell->setBackground('#f7e7ad'); 
                    });

                    $sheet->setBorder('A1:B1', 'thin');

                    $sheet->freezeFirstRowAndColumn();

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->personnel_number);
                        // $sheet->cell('B'.$key, $row->name);
                        // $sheet->cell('C'.$key, $row->email);
                        // $sheet->cell('D'.$key, $row->dept_name);
                        // $sheet->cell('E'.$key, $row->designation);
                        // $sheet->cell('F'.$key, $row->division);
                        // $sheet->cell('G'.$key, $row->branch);
                        // $sheet->cell('H'.$key, $row->section);
                        // $sheet->cell('I'.$key, $row->role_name);
                        // $sheet->cell('J'.$key, $row->supervisor_name);
                        $sheet->cell('B'.$key, $row->supervisor_personnel_number);

                        $sheet->getStyle('A'.$key.':B'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));

        } else {

            $this->data = $users->paginate($this->sizePerPage);
            return $this->response();
        }

    }

    public function changeRole(Request $request, $user_id){
     
        $user = User::findOrFail($user_id);
        $role_id = $request->has('role_id') && $request->get('role_id') ? $request->get('role_id') :  null;
        $user = $user->update(['role_id'=>$role_id]);
        $this->data = $user;
        $this->message = 'Role has been Changed.';
        return $this->response();
    }

    public function upload(Request $request){

        \Log::info('Setp1');
        \Log::info(date('Y-m-d H:i:s'));
        $this->validate($request,[
            'file' => 'required|ext_in:xlsx,xls,ods'
        ],[
           'file.ext_in' => 'System allows only xls, xlsx and ods extension to upload the data.' 
        ]);

        # Read The Excel file data
        $path = $request->file('file')->getRealPath();
        //$data = Excel::load($path)->first();
        $data = \App\Lib\ReadExcelFile::getCollection($path,'B');
        

        # Verifing the File Header
        $header = $data->first() ? $data->first()->keys()->toArray() : [];
        $verfiy_header =  new FilesHeaderVerify($header);
        $verfiy_header->supervisor();
        # End

        $insertData = [];
        $per_ids = $data->pluck('per_id')->toArray();
        $sup_ids = $data->pluck('sup_id')->toArray();

        $all_Ids = array_unique(array_merge($per_ids, $sup_ids));

        $pubnet_ids = User::whereIn('personnel_number', $all_Ids)->get(['personnel_number'])->pluck('personnel_number')->toArray();
        $total = 0;

        foreach($data as $key => $row) {

           $user_data = $row->toArray();

            if(!implode('', $user_data)) {
                continue;
            }

            $total++;
            $check = new SupervisorDataVerify($user_data, $pubnet_ids);
            $result =  $check->run();

            if($result['status']) {
                
                $insertData[] = $result['data']['translate'];

            } else {

                $this->errors[] = [
                    'data' => $result['data']['original'],
                    'errors' => $result['errors'],
                    'row_no' => ($key+1)
                ];
            }
        }

        if(count($insertData)) {

            $response =  User::batchInsertUpdate($insertData,['supervisor_personnel_number', 'updated_at']);
        }

        $this->data['updated'] = isset($response) ? $response['updated'] : 0;
        $this->data['inserted'] = isset($response) ? $response['inserted'] : 0;
        $this->data['total'] = $total;
        $this->data['skipped'] = count($this->errors);
        return $this->response();
    }

    
}