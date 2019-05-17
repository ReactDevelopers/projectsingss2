<?php namespace Modules\Package\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Package\Entities\Package;
use Modules\Package\Entities\Plan;
use Modules\Package\Entities\PlanFeature;
use Modules\Package\Repositories\PlanRepository;

class PackageService {

    /**
     * @var PlanRepository
     */
    private $repository;

    public function __construct(PlanRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getItems($option = 'list', $request){
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $keyword = $request->get('keyword') ? $request->get('keyword') : "";

        $array_field = array('id', 'name', 'price', 'type');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        if($option == 'list'){
            $items = Plan::select('mm__plans.*')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__plans.id', '=', $keyword);
                            $query->orWhere('mm__plans.name', 'like', '%'.$keyword.'%');
                        })
                        ->orderBy($order_field, $sort)
                        ->paginate($limit);

            return $items;
        }else{
            return Plan::select('mm__plans.*')
                         ->where(function($query) use ($keyword) {
                            $query->where('mm__plans.id', '=', $keyword);
                            $query->orWhere('mm__plans.name', 'like', '%'.$keyword.'%');
                        })
                         ->count();
        }
        
    }

    public function getFeaturesPackage(Plan $package){
        $features = PlanFeature::where('plan_id', $package->id)->orderBy('sort_order', 'ASC')->get();

        return count($features) ? $features : array();
    }
    public function update($model, $data){

        // update data
        $model->update($data);

        // update feature
        PlanFeature::where('plan_id', $model->id)->update(['value' => 'N']);
        if(isset($data['feature']) && !empty($data['feature'])){
            foreach ($data['feature'] as $item) {
                PlanFeature::where('plan_id', $model->id)->where('code', $item)->update(['value' => 'Y']);
            }
        }

        return $model;
    }
}