<?php namespace Modules\Audittrail\Services;
use Illuminate\Database\Eloquent\Model;
use \Modules\Audittrail\Contracts\AuditTrail as AuditTrailInterface;
use Modules\Audittrail\Repositories\LogRepository;
use Sentinel;

class AuditTrail implements AuditTrailInterface
{
    protected $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function log($event_name, $data, $options = array())
    {
        $insert_data = [];
        $name = "";

        if($data instanceof Model){
            $insert_data['entity_id'] = $data->getKey();
            $insert_data['entity_type'] = get_class($data);
            $insert_data['old_data'] = json_encode($data->getOriginal());
        }else{
            $insert_data['old_data'] = json_encode($data);
        }

        $insert_data['event_name'] =  $event_name;
        if($performed_user = \Sentinel::check()){
            $insert_data['performed_user_id'] =  $performed_user->getUserId();
            if(!array_get($options,'title') && isset($insert_data['entity_type'])){
//                $insert_data['title'] = $performed_user->present()->fullname()." ".$event_name." ".basename(str_replace('\\', '/', $insert_data['entity_type']));

                $config = config('asgard.audittrail.config.entity.' . $insert_data['entity_type']);
                $module = isset($config) && !empty($config) ? $config : basename(str_replace('\\', '/', $insert_data['entity_type']));

                switch ($module) {
                    case 'Customer':
                        $query = "SELECT u.* 
                                    FROM users u
                                    WHERE u.id = '".$insert_data['entity_id']."'
                                    ";
                        $item = \DB::select($query);
                        $name = $item ? $module . ' ' . $item[0]->first_name : $module;
                        break;
                    case 'Vendor':
                        $query = "SELECT u.*, p.business_name 
                                    FROM users u
                                    LEFT JOIN mm__vendors_profile p ON p.user_id = u.id
                                    WHERE u.id = '".$insert_data['entity_id']."'
                                    ";
                        $item = \DB::select($query);
                        $name = $item ? $module . ' ' . ($item[0]->business_name ? $item[0]->business_name : $item[0]->first_name) : $Module;
                        break;
                    default:
                        $name = $module;
                        break;
                }

                $insert_data['title'] = $name ." with id: ". $insert_data['entity_id']." have been ".$event_name;//." by: ".$performed_user->present()->fullname();
            }
        }



        return $this->logRepository->create($insert_data);
    }

}