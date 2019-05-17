<?php


return [
    'log' => [
        'event_name' => [
            'column_name' => 'event_name',
            'filter_input' => [
                'type' => 'choice',
                'optional' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Event name',
                    ],
                    'choices' => function(){
                        return app('Modules\Audittrail\Repositories\LogRepository')->getModel()->selectRaw("event_name")->groupBy("event_name")->get()->pluck("event_name","event_name")->toArray();
                    },
                ],
            ],
            'filter_type' => '=',
        ],
        'entity_type' => [
            'column_name' => 'entity_type',
            'filter_input' => [
                'type' => 'choice',
                'optional' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Entity type',
                    ],
                    'choices' => function(){
                        return app('Modules\Audittrail\Repositories\LogRepository')->getModel()->selectRaw("entity_type")->groupBy("entity_type")->get()->pluck("entity_type","entity_type")->toArray();
                    },
                ],
            ],
            'filter_type' => '=',
        ],
        'title' => [
            'column_name' => 'title',
            'filter_input' => [
                'type' => 'text',
                'optional' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Title',
                    ],
                ],
            ],
            'filter_type' => 'LIKE',
        ],
    ],
];