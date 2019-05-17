<?php

namespace App\Mummy\Api\V1\Presenters;

use App\Mummy\Api\V1\Transformers\CustomerTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class CustomerPresenter
 *
 * @package namespace App\Mummy\Api\V1\Presenters;
 */
class CustomerPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new CustomerTransformer();
    }
}
