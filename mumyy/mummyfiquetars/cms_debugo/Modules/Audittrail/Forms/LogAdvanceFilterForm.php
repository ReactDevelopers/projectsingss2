<?php 
namespace Modules\Audittrail\Forms;

use Modules\Core\Forms\AdvanceFilterForm;

class LogAdvanceFilterForm  extends AdvanceFilterForm
{
    public function __construct()
    {
        $datatable = config("asgard.audittrail.form_filter.log",[]);

        $this->setAdvanceFilterConfig($datatable);

        $this->config_form = array_column_index_key($datatable,$this->key_data_input);

        parent::__construct();
    }

}