<?php namespace Modules\Audittrail\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Audittrail\Entities\Log;
use Modules\Audittrail\Repositories\LogRepository;
use Modules\Core\Forms\FormBuilder;
use Modules\Audittrail\Http\Controllers\Admin\AdminBaseControllerTrait;
use Modules\Audittrail\Services\AudittrailService;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Flash;
use Maatwebsite\Excel\Facades\Excel;

class LogController extends AdminBaseController
{
    /**
     * @var LogRepository
     */
    private $log;

    /**
     * @var AudittrailService
     */
    private $service;

    public function __construct(Request $request, LogRepository $log, AudittrailService $service)
    {
        parent::__construct();

        $this->request = $request;
        $this->model_repository = $log;
        $this->model = $this->model_repository->getModel();
        $this->entity_name = 'log';
        $this->entity_name_plural = str_plural($this->entity_name);
        $this->service = $service;

    }


    public function index(Request $request)
    {
//         $table_attributes = ['stateSave'=>true,"dom"=>'<"clearfix"Blfrtip>'];
//         $datatables = $this->buildDataTable($this->model_repository->getModel()->newQuery(),config('asgard.audittrail.datatable.log'))->setMainModel($this->model_repository->getModel());
// //        $datatables->setMultiSelectActionList(config('asgard.audittrail.datatable_multi_select_action.log'));


//         if($this->request->has('action')){
//             $response = $datatables->progressSubmitAction(array_get(config('asgard.audittrail.datatable_multi_select_action.log'),$this->request->get('action')),$this->model_repository->getModel());
//             if($response){
//                 return $response;
//             }
//         }

//         if($this->request->ajax()){
//             return $datatables->makeData();
//         }
//         $datatables = $datatables->buildTable(['url'=>route('admin.audittrail.log.index')],$table_attributes);
//         return view($this->module_name . '::admin.' . $this->entity_name_plural . '.index', compact('datatables'));
        $audittrails = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $audittrails->count() - 1;

        return view('audittrail::admin.logs.index', compact(['audittrails', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
        
    }
    
    /**
     * @param $model
     * @param array $config
     * @return \DatatableHelper
     */
    protected function buildDataTable($model, array $config)
    {
        $form = app('Modules\Core\Forms\AdvanceFilterFormBuilder')->create('Modules\Audittrail\Forms\LogAdvanceFilterForm');

        $datatable = \DatatableHelper::setUp($model, $config)
            ->setFilterForm($form)
        ;
        return $datatable;
    }

    /**
     * Export Vendors Data List
     *
     * @param  Vendors $vendor
     * @return Response
     */

    public function getExportVendors(Request $request){
        $logs = $this->service->all();
        Excel::create('Logs', function($excel) use ($logs){
            $excel->sheet('Sheet 1', function($sheet) use ($logs){
                $sheet->loadView('audittrail::admin.logs.export.log',compact('logs'));
            });
        })->export('csv');
    }
}
