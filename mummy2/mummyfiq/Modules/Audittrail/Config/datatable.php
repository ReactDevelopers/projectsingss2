<?php

return [
        "log"=>[
            'id'=>[
                'name'=>'id',
                'title'=>'ID',
                'visible'=>false,
            ],
            'event_name'=>[
                'name'=>'event_name',
                'title'=>'Event name',
            ],
            'performed_user_id'=>[
                'name'=>'performed_user_id',
                'title'=>'User',
                'with'=>['performedUser'],
                'editColumn'=>function($obj){
                    if($performedUser = $obj->performedUser){
                        return $performedUser->present()->fullname();
                    }
                }
            ],
            'created_at'=>[
                'name'=>'created_at',
                'title'=>'Date time',
                'editColumn'=>function($obj){
                    if($obj->created_at instanceof  \Carbon\Carbon){
                        return (string) $obj->created_at. "(".$obj->created_at->diffForHumans().")";
                    }
                    return $obj->created_at;
                }
            ],
            'title'=>[
                'name'=>'title',
                'title'=>'Title',
            ],
        ],
//add here
];