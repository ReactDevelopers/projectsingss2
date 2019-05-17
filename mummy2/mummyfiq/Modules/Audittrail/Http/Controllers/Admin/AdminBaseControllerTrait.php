<?php namespace Modules\Audittrail\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use Modules\Core\Common\Datatable\DatatableCommon;
use Modules\Core\Forms\FormBuilder;
use Modules\Core\Repositories\BaseRepository;
use Laracasts\Flash\Flash;
use DB;

/**
 * Class AdminBaseControllerTrait
 * @package Modules\Core\Http\Controllers\Admin
 * @property string module_name
 */
trait AdminBaseControllerTrait
{

	use FormBuilderTrait;

	/**
	 * @public @property  $module_name
	 */

	/**
	 * @var BaseRepository;
	 */
	protected $model_repository;

	/**
	 * @var \Model;
	 */
	protected $model;

	/**
	 * @var string
	 */
	protected $entity_name;

	/**
	 * @var string
	 */
	protected $entity_name_plural;

	/**
	 * @var string
	 */
	protected $module_view;

	/**
	 * @var Request;
	 */
	protected $request;

	/**
	 * @var array
	 */
	protected $config_datatable = [
		'edit'              => true,
		'delete'            => true,
		'multi_delete'      => true,
		'edit_route_name'   => '',
		'delete_route_name' => '',
		'other_buttons'     => [],
		'view'              => '',
		'checkbox'          => true,
	];
	/**
	 * @var array
	 */
	protected $form_filter = [];

	/**
	 * @var array
	 */
	protected $setupColumnName = [];


	/**
	 * setup view for action column in table
	 *
	 * @return array
	 */
	protected function setupDatatableConfig()
	{
		$attrs = $this->config_datatable;
		$config = [];

		if (count($attrs['other_buttons'])) {
			$config['buttons'] = $attrs['other_buttons'];
		}
		if (isset($attrs['other_action_items']) && count($attrs['other_action_items'])) {
			$config['other_action_items'] = $attrs['other_action_items'];
		}

		if ($attrs['edit']) {
			if (!empty($attrs['edit_route_name'])) {
				$config['buttons']['edit']['route_name'] = $attrs['edit_route_name'];
			} else {
				$config['buttons']['edit']['route_name'] = 'admin.' . $this->module_name . '.' . $this->entity_name . '.edit';
			}
		}

		if ($attrs['delete']) {
			if (!empty($attrs['delete_route_name'])) {
				$config['buttons']['delete']['route_name'] = $attrs['delete_route_name'];
			} else {
				$config['buttons']['delete']['route_name'] = 'admin.' . $this->module_name . '.' . $this->entity_name . '.destroy';
			}
		}
		if (isset($attrs['multi_delete'])) {
			if (!empty($attrs['multi_delete_route_name'])) {
				$config['buttons']['multi_delete']['route_name'] = $attrs['multi_delete_route_name'];
			} else {
				$config['buttons']['multi_delete']['route_name'] = 'admin.' . $this->module_name . '.' . $this->entity_name . '.multi_destroy';
			}
		}


		if (!empty($attrs['view'])) {
			$config['view'] = $attrs['view'];
		}
		if (!empty($attrs['button_view'])) {
			$config['button_view'] = $attrs['button_view'];
		}

		return $config;
	}

	protected function setupColumnName($titles = null,$main_model =null)
	{

		if ($this->setupColumnName) {
			return $this->setupColumnName;
		}

		if(is_null($main_model)){
			$main_model = $this->model_repository->getModel();
		}

		if (is_null($titles)) {
			$titles = config('asgard.' . $this->module_name . '.datatable.' . $this->entity_name);
		}
		$columns = array_keys($titles);
		$with               = [];
		$relations          = [];
		$main_table_columns = [];

		foreach ($columns as $col) {
			if (array_get($titles[$col], 'selectAble', true) !== false) {
				if (strpos($col, '__') !== false) {
					$tmp                  = explode('__', $col);
					$relations[$tmp[0]][] = $tmp[1];

					if(!str_is('*.*',$titles[$col]['name']) && $main_model){
						$child_model     = $main_model->{$tmp[0]}()->getModel();
						$foreign_key     = $main_model->{$tmp[0]}()->getForeignKey();
						$name_table_join = $child_model->getTable() . "_" . $tmp[0];
						$titles[$col]['name'] = $name_table_join.".". $tmp[1];
					}

//					$titles[$col]['name'] = $tmp[0] . '__' . $tmp[1];  //$this->model->{$tmp[0]}()->getModel()->getTable().'.'.$titles[$col]['name'];
				} else {
					$main_table_columns[] = $col;
					$titles[$col]['name'] = $this->model->getTable() . '.' . $titles[$col]['name'];
				}
			} else {
				$titles[$col]['orderable']  = false;
				$titles[$col]['searchable'] = false;
			}

			$with = array_merge(array_get($titles[$col], 'with', []), $with);
		}
		//Toan add

		if(!is_default_lang() && isset($this->model->translatedAttributes))
		{

			$translation_class_name = get_class($this->model)."Translation";
			$translation_table = new $translation_class_name;

			foreach ($titles as &$k)
			{
				if(in_array(str_replace($this->model->getTable().'.','',$k['name']),$this->model->translatedAttributes)) {
					$k['name'] = str_replace ( $this->model->getTable().".",$translation_table->getTable().'.',$k['name']);;
				}
			}
		}
		//
		$result['with']               = $with;
		$result['relations']          = $relations;
		$result['main_table_columns'] = $main_table_columns;
		$result['titles']             = $titles;
		$this->setupColumnName = $result;

		return $result;
	}

	protected function setUpQueryBuilder($query_builder, Model $main_model = null)
	{

		$columns            = $this->setupColumnName();
		$relations          = $columns['relations'];
		$main_table_columns = $columns['main_table_columns'];
		if (!$query_builder) {
			$query_builder = $this->model->newQuery();
		}

		if (is_null($main_model)) {
			$main_model = $this->model_repository->getModel();
		}

		$with = array_get($columns, 'with');
		if ($with) {
			$with          = is_array($with) ? $with : [$with];
			$query_builder = $query_builder->with($with);
		}

		if (!in_array('id', $main_table_columns)) {
			$main_table_columns[] = 'id';
		}

		$query_builder = $query_builder->select($main_model->getTable() . '.' . $main_table_columns[0]);

		if(!$this->request->has('order')){
			$query_builder = $query_builder->orderBy($main_model->getTable().'.id','DESC');
		}

		if ($this->request->has('_include')) {
			$query_builder = $query_builder->with($this->request->get('_include'));
		}


		foreach ($main_table_columns as $k => $col) {
			if ($k > 0) {
				$select_column = $main_model->getTable() . '.' . $col;
				$query_builder->addSelect($select_column);

			}
		}


		if (count($relations)) {
			foreach ($relations as $rel => $val) {
				$child_model     = $main_model->{$rel}()->getModel();
				$foreign_key     = $main_model->{$rel}()->getForeignKey();
				$name_table_join = $child_model->getTable() . "_" . $rel;

				if (strpos($foreign_key, '.') !== false) {
					$tmp_foreign_key = explode('.', $foreign_key);
					$foreign_key     = last($tmp_foreign_key);
				}

				foreach ($val as $v) {
					$query_builder->addSelect(DB::raw($name_table_join . ".$v as " . $rel . "__" . $v));
				}

				if ($main_model->{$rel}() instanceof BelongsTo) {
					$query_builder->leftJoin($child_model->getTable() . " as " . $name_table_join, $name_table_join . '.' . $child_model->getKeyName(), '=', $main_model->getTable() . "." . $foreign_key);
				} else {
					$query_builder->leftJoin($child_model->getTable() . " as " . $name_table_join, $name_table_join . '.' . $foreign_key, '=', $main_model->getTable() . "." . $main_model->getKeyName());
				}

			}
		}


		$advance_filter = $this->request->get('advance_filter');

		if ($advance_filter) {
			$form_filter = $this->form_filter;

			foreach ($advance_filter as $name => $value_filter) {

				// get config of this input field
				$data_filter = array_get($form_filter, $name);

				// get column name to use in where function
				if(!$data_filter){
					continue;
				}
				$column_name = array_get($data_filter, 'column_name');
				if(!is_default_lang() && isset($main_model->translatedAttributes)  && in_array($column_name,$main_model->translatedAttributes))
				{
					$column_name = DB::raw($main_model->getTable().'_translations.'.$column_name);
				}
				if (isset($data_filter) && $value_filter != ''){

					$filter_type     = array_get($data_filter, 'filter_type');
					$filter_function = array_get($data_filter, 'filter_function');
					/**
					 * @var \Illuminate\Database\Query\Builder $query_builder
					 */
					if (in_array(strtoupper($filter_type), ['=', '<>'])) {
						$query_builder->where($column_name, $filter_type, $value_filter);
					} elseif (strtoupper($filter_type) == "IN") {
						$query_builder->whereIn($column_name, (array)$value_filter);
					} elseif (strtoupper($filter_type) == "LIKE") {
						$query_builder->where($column_name, $filter_type, '%' . $value_filter . '%');
					} elseif (strtoupper($filter_type) == "NOT IN") {
						$query_builder->whereNotIn($column_name, (array)$value_filter);
					} elseif ($filter_function instanceof \Closure) {
						if (strtoupper($filter_type) == "NESTED") {
							// filter function should not return anything
							$query_builder->whereNested($filter_function);
						} elseif (strtoupper($filter_type) == "CUSTOM") {
							// we pass the current query builder and this $filter_function can process it and return it, return an QueryBuilder instance is required
							$query_builder = $filter_function($query_builder);
						}
					}
				}
			}
		}
		return $query_builder;
	}

	protected function getData($query_builder = null, $main_model = null)
	{
		return $this->setUpQueryBuilder($query_builder, $main_model);
	}


	protected function handleRequestDataTableAction($dataTableHelper,$export_action,$query_builder){

		$config = $this->setupDatatableConfig();
		$config['buttons'] = false;
		$config['checkbox'] = false;

		$dataTableHelper = $dataTableHelper->setConfig($config);


		$data = $dataTableHelper->setDataAjax($query_builder);

		$data_array = $this->processDataBeforeExport($data);

		$file = app('excel')->create("export_".time(), function ($excel) use($data_array){
			$excel->sheet('exported-data', function ($sheet)use($data_array) {
				$sheet->fromArray($data_array);
			});
		});

		return $file->download($export_action);

	}


	protected function processDataBeforeExport($data){
		$data  = $data->getData()->data;
		$titles = array_get($this->setupColumnName(),'titles');
		$data_response = [];
		if($titles){
			$title_row = [];
			foreach($titles as $column=>$data_title){
				$title_row[] = $data_title["title"];
			}
			$data_response[] = $title_row;
			foreach($data as $v){
				$row = [];
				foreach($titles as $column=>$data_title){
					$row[] = $v->{$column};
				}

				$data_response[] = $row;
			}
		}else{
			$data_response = object_to_array($data);
		}

		return $data_response;
	}


	public function index(DatatableCommon $dataTableHelper)
	{
		$config          = $this->setupDatatableConfig();
		$dataTableHelper = $dataTableHelper->setConfig($config);
		$columns         = $this->setupColumnName();
		$titles          = $columns['titles'];
		$dataTableHelper = $dataTableHelper->setTitles($titles);

		// use for multiselect action
		$multi_select_action_list = null;// config("asgard.".$this->module_name.".config.datatable_multi_select_action.".$this->entity_name."",[]);



		if($this->request->has('export_action') && !$this->request->ajax()){
			$export_action = $this->request->get('export_action');
			$getData = $this->getData();
			return $this->handleRequestDataTableAction($dataTableHelper,$export_action,$getData);
		}

		if ($this->request->ajax() || $this->request->wantsJson()) {
			$getData = $this->getData();


			if ($this->request->has('action') && $multi_select_action_list) {
				$response = $dataTableHelper->progressSubmitAction(array_get($multi_select_action_list, $this->request->get('action')), $getData);
				if ($response) {
					return $response;
				}
			}
			$columns = array();

			$translation = false;

			if(!is_default_lang() && isset($this->model->translatedAttributes))
			{

				$translation_class_name = get_class($this->model)."Translation";
				$translation_table = new $translation_class_name;

				$attr = array_diff(array_values(array_get($this->setupColumnName(),'main_table_columns',[])), $this->model->translatedAttributes);

				foreach ($attr as $k)
				{
					$columns[] = $this->model->getTable().'.'.$k;
				}

				foreach ($this->model->translatedAttributes as $k)
				{
					$columns[] = $translation_table->getTable().'.'.$k;
				}
				$translation = true;
			}
			$data = $dataTableHelper->setDataAjax($getData,$columns, $translation);
			return $data;
		}

		$datatables = $dataTableHelper->make(['stateSave'=>true,"dom"=>'<"clearfix"Blfrtip>',]);


		$table_attributes = ['id' => "dataTable_" . $this->entity_name];

		return view($this->module_name . '::admin.' . $this->entity_name_plural . '.index', compact('datatables', 'dataTableHelper', 'table_attributes'));
	}


	public function create(FormBuilder $formBuilder)
	{
		$form = $formBuilder->create('Modules\\' . ucfirst($this->module_name) . '\Forms\\' . ucfirst($this->entity_name) . 'CreateForm');
		return view($this->module_name . '::admin.' . $this->entity_name_plural . '.create', compact('form'));
	}


	public function store()
	{

		$form = $this->form('Modules\\' . ucfirst($this->module_name) . '\Forms\\' . ucfirst($this->entity_name) . 'CreateForm');

		if (!$form->isValid()) {
			return redirect()->back()->withErrors($form->getErrors())->withInput();
		}
		$this->model_repository->create($this->request->all());

		Flash::success(trans($this->module_name . '::' . $this->entity_name_plural . '.messages.' . $this->entity_name . ' created'));

		return redirect()->route('admin.' . $this->module_name . '.' . $this->entity_name . '.index');
	}


	public function edit($model, FormBuilder $formBuilder)
	{
		if (!$model instanceof Model) {
			$model = $this->model_repository->find($model);
		}
		$form = $formBuilder->create('Modules\\' . ucfirst($this->module_name) . '\Forms\\' . ucfirst($this->entity_name) . 'EditForm', [
			'model' => $model,
		]);

		return view($this->module_name . '::admin.' . $this->entity_name_plural . '.edit', array($this->entity_name => $model, 'form' => $form));
	}


	public function update($model)
	{

		if (!$model instanceof Model) {
			$model = $this->model_repository->find($model);
		}

		$form = $this->form('Modules\\' . ucfirst($this->module_name) . '\Forms\\' . ucfirst($this->entity_name) . 'EditForm',[
			'model'=>$model
		]);

		if (!$form->isValid()) {
			return redirect()->back()->withErrors($form->getErrors())->withInput();
		}

		$all_input = $this->request->all();
		$this->model_repository->update($model, $all_input);

		Flash::success(trans($this->module_name . '::' . $this->entity_name_plural . '.messages.' . $this->entity_name . ' updated'));

		return redirect()->route('admin.' . $this->module_name . '.' . $this->entity_name . '.index');
	}


	public function destroy($model)
	{
		if (!$model instanceof Model) {
			$model = $this->model_repository->find($model);
		}
		$this->model_repository->destroy($model);

		Flash::success(trans($this->module_name . '::' . $this->entity_name_plural . '.messages.' . $this->entity_name . ' deleted'));

		return redirect()->route('admin.' . $this->module_name . '.' . $this->entity_name . '.index');
	}

	public function getUniqueSlug()
	{
		$text = $this->request->get('text');
		if($text){
			return $this->model_repository->getUniqueSlug($text);
		}else{
			return $text;
		}
	}

}