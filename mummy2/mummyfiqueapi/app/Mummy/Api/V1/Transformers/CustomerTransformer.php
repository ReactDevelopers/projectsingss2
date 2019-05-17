<?php

namespace App\Mummy\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use App\Mummy\Api\V1\Entities\Customer;

/**
 * Class CustomerTransformer
 * @package namespace App\Mummy\Api\V1\Transformers;
 */
class CustomerTransformer extends TransformerAbstract
{

    /**
     * Transform the \Customer entity
     * @param \Customer $model
     *
     * @return array
     */
    public function transform(Customer $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
