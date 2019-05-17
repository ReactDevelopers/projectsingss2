<?php namespace Modules\Audittrail\Forms;

use Modules\Core\Forms\Form;

class LogEditForm  extends Form
{

    function __construct()
    {
        $this->config_form = config("asgard.audittrail.form.log.edit");
    }

    public function buildForm()
    {
        parent::buildForm();
        // Add fields here...
    }
}