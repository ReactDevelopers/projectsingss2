<?php namespace Modules\Version\Services;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Modules\Version\Repositories\VersionRepository;
use Modules\Setting\Entities\Setting;
use Modules\Setting\Repositories\SettingRepository;

class VersionService {

    /**
     *
     * @var VersionRepository
     */
    private $repository;

    /**
     *
     * @var SettingRepository
     */
    private $settingRepository;

    private $config;

    public function __construct(VersionRepository $repository, SettingRepository $settingRepositorys) {
        $this->repository = $repository;

        $this->settingRepositorys = $settingRepositorys;

        $this->config = config('asgard.version.config.config');
    }

    public function getItem(){
        if(empty($this->config)){
            return false;
        }

        $data = new \stdClass();
        foreach ($this->config as $key => $item) {
            $data->$item['title'] = "";
            $result = $this->settingRepositorys->findByAttributes(['name' => $item['name']]);
            if(count($result)){
                $data->$item['title'] = $result->plainValue;
            }
        }

        return $data;
    }

    public function update($data){
        if(empty($this->config)){
            return false;
        }

        foreach($this->config as $key => $item){
            $result = $this->settingRepositorys->findByAttributes(['name' => $item['name']]);
            $value =  isset($data[$item['title']]) && $data[$item['title']] !== false ? $data[$item['title']] : "";
            if(count($result)){
                $result->update(['plainValue' => $value]);
            }else{
                $this->settingRepositorys->create(['name' => $item['name'], 'plainValue' => $value, 'isTranslatable' => 0]);
            }
        }
    }


}