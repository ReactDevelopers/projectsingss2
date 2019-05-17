<?php namespace Modules\Audittrail\Forms;

use Modules\Core\Forms\Form;

class LogCreateForm  extends Form
{

    function __construct()
    {
        $this->config_form = config("asgard.audittrail.form.log.create");
    }

    public function buildForm()
    {
        parent::buildForm();
        // Add fields here...
    }
}