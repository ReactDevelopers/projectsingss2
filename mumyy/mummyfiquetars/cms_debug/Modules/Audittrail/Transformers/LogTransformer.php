<?php namespace Modules\Audittrail\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Audittrail\Entities\Log;

class LogTransformer extends TransformerAbstract  implements LogTransformerInterface
{
  /**
   * Turn this item object into a generic array.
   *
   * @param $item
   * @return array
   */
  public function transform(Log $item)
  {
      return [
          'id' => (int)$item->id,
          'created_at' => (string)$item->created_at,
          'updated_at' => (string)$item->updated_at,
      ];
  }
}
