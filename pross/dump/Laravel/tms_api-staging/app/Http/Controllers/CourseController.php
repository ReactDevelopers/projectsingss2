<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Models\ProgrammeCategory;
use App\Lib\DataVerify\CourseDataVerify;
use App\Models\Course;
use App\Lib\General;
use PhpOffice\PhpSpreadsheet\IOFactory as ExcelReader;
use App\Lib\DataVerify\FilesHeaderVerify;

class CourseController extends Controller
{   

    /**
     * Fetching the course base on the filter and sorting
     */
    public function index(Request $request) {

        $courses = Course::select(
            'courses.id as id',
            'courses.title as course_title',
            'courses.duration_in_days',
            'courses.course_code',
            'programme_category_id',
            'programme_type_id',
            'department_id',
            'assessment_type_id',
            'mandatory',
            'training_location_id',
            'delivery_method_id',
           // 'course_provider_id',
            'type_of_grant',
            'compulsory',
            'cts_approve_future_placement',
            'placement_criteria',
            'cost_per_pax',
            'subsidy',
            'subsidy_value',
            'vendor_email',
            'pc.prog_category_name',
            'py.prog_type_name',
            'dpt.dept_name',
            'courses.vendor_email',
            'courses.subsidy',
            'courses.subsidy_value',
            'courses.cost_per_pax',
           // 'courses.delivery_method',
            'courses.mandatory',
            'loc.location',
            'courses.course_provider as provider_name',
            'at.assessment_type_name'
        )
        ->join('programme_categories as pc','pc.id','=', 'courses.programme_category_id')
        ->leftJoin('departments as dpt','dpt.id','=', 'courses.department_id')
        ->leftJoin('training_locations as loc','loc.id','=', 'courses.training_location_id')
        //->leftJoin('course_providers as cp','cp.id','=', 'courses.course_provider_id')
        ->leftJoin('assessment_types as at', 'at.id', '=', 'courses.assessment_type_id')
        ->with('deliveryMethodData')
        ->join('programme_types as py','py.id','=', 'courses.programme_type_id');

        /** start custom filters **/
        if($request->has('customFilters')) {            

            $general = new General();
            $keys = [
                'course_code' => 'courses.course_code',
                'course_title' => 'courses.title',
                'duration_in_days' =>   'courses.duration_in_days',
                'prog_type_name' => 'py.id',
                'prog_category_name' => 'pc.id'
            ];

            $general->applyFilters($courses, $request->customFilters, $keys);
        }
        /** end custom filters **/

        if($request->has('searchdata')){
            
            $s = $request->get('searchdata');
            $courses->where(function($q) use($s) {
                $q->orWhere('courses.title', 'LIKE', "%{$s}%");
                $q->orWhere('courses.course_code', 'LIKE', "%{$s}%");
            });
        }

        

        /** start sorting **/
        if($request->has('sortName')):
            switch ($request->sortName) {
                case 'course_code':
                    $courses->orderBy('courses.course_code',$request->sortOrder);
                    break; 
                case 'course_title':
                    $courses->orderBy('courses.title',$request->sortOrder)->orderBy('courses.id','DESC');
                    break; 
                case 'duration_in_days':
                    $courses->orderBy('courses.duration_in_days',$request->sortOrder);   
                    break;
                case 'prog_type_name':
                    $courses->orderBy('py.prog_type_name',$request->sortOrder); 
                    break;
                case 'prog_category_name':
                    $courses->orderBy('pc.prog_category_name',$request->sortOrder); 
                    break;
                
                default:
                    # code...
                    break;
            }
        endif;
        /** end sorting **/

        if($request->get('export')) {
            
            /** if selected record **/
                if($request->has('selected') && $request->get('export')):
                    $courses->whereIn('courses.id', $request->get('selected'));
                endif;
            /** selected record **/
            $data = $courses->get();

            General::DownloadExcel(Excel::create('Course', function($excel) use ($data) {
                $excel->sheet('Course DATA', function($sheet) use ($data)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Course Code');});
                    $sheet->cell('B1', function($cell) {$cell->setValue('Course Title');});
                    $sheet->cell('C1', function($cell) {$cell->setValue('Duration (No. of Days)');});
                    $sheet->cell('D1', function($cell) {$cell->setValue('Programme Category');});
                    $sheet->cell('E1', function($cell) {$cell->setValue('Programme Type');});
                    $sheet->cell('F1', function($cell) {$cell->setValue('Competency Level (if applicable)');});
                    $sheet->cell('G1', function($cell) {$cell->setValue('Assessment Type');});
                    $sheet->cell('H1', function($cell) {$cell->setValue('Mandatory Y/N');});
                    $sheet->cell('I1', function($cell) {$cell->setValue('Compulsory Y/N');});
                    $sheet->cell('J1', function($cell) {$cell->setValue('Delivery Method');});
                    $sheet->cell('K1', function($cell) {$cell->setValue('Training Location');});
                    $sheet->cell('L1', function($cell) {$cell->setValue('Course Provider');});
                    $sheet->cell('M1', function($cell) {$cell->setValue('Cost/Pax (without GST)');});
                    $sheet->cell('N1', function($cell) {$cell->setValue('Grant');});
                    $sheet->cell('O1', function($cell) {$cell->setValue('Funding Type');});
                    $sheet->cell('P1', function($cell) {$cell->setValue('Placement Criteria');});
                    $sheet->cell('Q1', function($cell) {$cell->setValue('CTS to approve Future Placements');});

                    $sheet->cell('A1:Q1', function($cell) {
                           $cell->setFontWeight('bold');
                    });

                    $sheet->cell('A1:Q1', function($cell) {
                           $cell->setBackground('#f7e7ad'); 
                    });

                    $sheet->setBorder('A1:Q1', 'thin');

                    $sheet->freezeFirstRowAndColumn();

                    foreach ($data as $index => $row) {
                        $key = $index+2;
                        $sheet->cell('A'.$key, $row->course_code);
                        $sheet->cell('B'.$key, $row->course_title);
                        $sheet->cell('C'.$key, $row->duration_in_days);
                        $sheet->cell('D'.$key, $row->prog_category_name);
                        $sheet->cell('E'.$key, $row->prog_type_name);
                        $sheet->cell('F'.$key, $row->dept_name);
                        $sheet->cell('G'.$key, $row->assessment_type_name);
                        $sheet->cell('H'.$key, $row->mandatory);
                        $sheet->cell('I'.$key, $row->compulsory);
                        $sheet->cell('J'.$key, $row->delivery_method);
                        $sheet->cell('K'.$key, $row->location);
                        $sheet->cell('L'.$key, $row->provider_name);
                        $sheet->cell('M'.$key, $row->cost_per_pax);
                        $sheet->cell('N'.$key, $row->type_of_grant);
                        $sheet->cell('O'.$key, $row->vendor_email);
                        $sheet->cell('P'.$key, $row->placement_criteria);
                        $sheet->cell('Q'.$key, $row->cts_approve_future_placement);

                        $sheet->getStyle('A'.$key.':Q'.$key)->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }

                    $sheet->setAutoFilter();
                });

            }));

        }
        else {
            $this->data = $courses->paginate($this->sizePerPage);
            return $this->response();
        }
    }
    /**
     * Handle the Course Upload Request.
     */
    public function upload(Request  $request) {

        $this->validate($request,[
            'file' => 'required|ext_in:xlsx,xls,ods'
        ],[
           'file.ext_in' => 'System allows only xls, xlsx and ods extension to upload the data.' 
        ]);
        
        
        # Read The Excel file data
        $path = $request->file('file')->getRealPath();
        $sheet = ExcelReader::load($path)->getActiveSheet();        
        $no_of_rows = $sheet->getHighestDataRow();
        $i = 1;
        $total = 0;
        $header = [];
        $insertData = [];
        
        while($i <= $no_of_rows ) {

            if($i ==1) {

                $header = $sheet->rangeToArray('A'.$i.':Q'.$i);                
                $header = $header[0];

                array_walk($header, function(&$v) {
                    $v = str_slug($v,'_');
                });

                $verifyHeader = new FilesHeaderVerify($header);
                $verifyHeader->course();

            } else {

                $row = $sheet->rangeToArray('A'.$i.':Q'.$i,null, false, false);
                $row = $row[0];
                array_walk($row, function (&$v) {
                    $v = trim($v);
                });

                if(!implode('', $row)) {
                    $i++;
                    continue;
                }

                $total++;
                $course_data = array_combine($header, $row);
                
                $check = new CourseDataVerify($course_data);

                $result =  $check->run();

                if($result['status']) {
                    
                    $insertData[] = $result['data']['translate'];

                } else {

                    $this->errors[] = [
                        'data' => $result['data']['original'],
                        'errors' => $result['errors'],
                        'row_no' => $i-1
                    ];
                }
            }
            $i++;
        }   

        if(count($insertData)) {

            $response = Course::batchInsertUpdate($insertData, ['title',
                'duration_in_days',
                'programme_category_id',
                'programme_type_id',
                'department_id',
                'assessment_type_id',
                'mandatory',
                'training_location_id',
                'delivery_method_id',
                'course_provider',
                'cost_per_pax',
                'subsidy',
                'subsidy_value',
                'vendor_email',
                'compulsory',
                'cts_approve_future_placement',
                'placement_criteria',
                'updated_at',
                'deleted_at',
                'updater_id'
            ]);           
        }

        $this->data['updated'] = isset($response) ? $response['updated'] : 0;
        $this->data['inserted'] = isset($response) ? $response['inserted'] : 0;
        $this->data['total'] = $total;
        $this->data['skipped'] = count($this->errors);
        return $this->response();
    }

    /**
     * Getting the course detail information by course_id 
     * @param  Request $request [description]
     * @param  [type]  $course_id [description]
     * @return Json
     */
    public function get(Request $request,$course_id)
    {    
        $course = Course::select(
            'courses.id as id',
            'courses.title as course_title',
            'courses.duration_in_days',
            'courses.course_code',
            'programme_category_id',
            'programme_type_id',
            'department_id',
            'assessment_type_id',
            'mandatory',
            'training_location_id',
            'delivery_method_id',
            'type_of_grant',
            'compulsory',
            'cts_approve_future_placement',
            'placement_criteria',
            //'course_provider_id',
            'cost_per_pax',
            'subsidy',
            'subsidy_value',
            'vendor_email',
            'pc.prog_category_name',
            'py.prog_type_name',
            'dpt.dept_name',
            'loc.location as training_location_name',
            'courses.course_provider as course_provider_name',
            'at.assessment_type_name'
        )
        ->join('programme_categories as pc','pc.id','=', 'courses.programme_category_id')
        ->leftJoin('departments as dpt','dpt.id','=', 'courses.department_id')
        ->leftJoin('training_locations as loc','loc.id','=', 'courses.training_location_id')
        //->leftJoin('course_providers as cp','cp.id','=', 'courses.course_provider_id')
        ->leftJoin('assessment_types as at', 'at.id', '=', 'courses.assessment_type_id')
        ->join('programme_types as py','py.id','=', 'courses.programme_type_id')
        ->where('courses.id', $course_id)->get()->first();

        $this->data = $course;
        return $this->response($course? 200: 404);
    }


    /**
     * This function uses to update the event status as deleted
     * @param  Request $request [description]
     * @param  [type]  $course_id [description]
     * @return [type]           [description]
     */ 
    public function delete(Request $request,$course_id)
    {
        $this->message = 'Course has been deleted successfully.';
        Course::find($course_id)->delete();
        return $this->response();
    }

    /**
     * To Delete the Bulk Course
     */
    public function deleteBulkCourse(Request $request) {

        $this->validate($request,[
            'ids' => 'required|array'
        ]);

        Course::whereIn('id', $request->get('ids'))->delete();
        
        $this->message = 'Data has been Deleted';
        
        return $this->response();
    }

    /**
     * This function uses to get the list of dropdown
     * @param  Request $request [description]
     * @return JSON
     */ 
    public function getList(Request $request)
    {
        $general = new General();
        $this->data = $general->getList();
        return $this->response();
    }
}