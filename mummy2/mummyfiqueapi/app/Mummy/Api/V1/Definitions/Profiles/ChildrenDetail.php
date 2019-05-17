<?php
namespace App\Mummy\V1\Definitions\Profiles;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="Customer"))
 */
class ChildrenDetail
{
	 /**
     * @SWG\Property(
     *      property="childrens",
     *      type="array",
     *      @SWG\Items(
     *          type="object",
     *          @SWG\Property(property="name", type="string", default="Bin"),
     *          @SWG\Property(property="dob", type="integer", default="1"),
     *          @SWG\Property(property="age", type="integer", default="1"),
     *      )
     *  ),
     */
    
}