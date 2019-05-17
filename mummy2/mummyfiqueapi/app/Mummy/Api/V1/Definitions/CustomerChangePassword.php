<?php
namespace App\Mummy\V1\Definitions;
/**
 * @SWG\Definition(type="object", @SWG\Xml(name="UserChangePassword"))
 */
class CustomerChangePassword
{
    /**
     * @SWG\Property(format="int64",example="7c4a8d09ca3762af61e59520943dc26494f8941b")
     * @var string
     */
    public $currentPassword;
    /**
     * @SWG\Property(format="int64",example="Q8nJ3JCCM4zmdP899PubGPyGrQriiuMS4KX9bs2Z")
     * @var string
     */
    public $newPassword;
}