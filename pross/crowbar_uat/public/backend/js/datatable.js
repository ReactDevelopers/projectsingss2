var $dataTableInstance = '';

$(function(){
	$('[data-request="datatable-auto"]').each(function() {
	  	var $this 		= $(this);
	  	var $url 		= $this.data('url');
	  	var $columns 	= $this.data('columns');
	  	var $number 	= $this.data('number');
	  	var $primary 	= $this.data('primary');
	  	var $manager 	= $this.data('manager');
	  	var $buttons 	= $this.data('buttons')?($this.data('buttons')).split(","):[];

	    $dataTableInstance = $this.DataTable({
	        paging: true,
	        searching: true,
	        processing: true,
	        serverSide: true,
	        ajax: $url,
	        columns : $columns,
	        order:[
	            [0, "DESC"]
	        ],
	        "columnDefs": [{
	            "targets": 0,
	            "data": null,
	            "render": function (data, type, full, meta) {return parseFloat(meta.row) + parseFloat(1) + parseFloat(meta.settings._iDisplayStart);
	            }
	        },{
	            "targets": $number,
	            "data": null,
	            "render": function (data) {
	            	var $links = "";

	            	if($buttons.indexOf('view') !== -1){
	                	$links += '<a href="'+base_url+'/'+$manager+'/'+data[$primary]+'">View</a>';
	            	}

	            	if($buttons.indexOf('edit') !== -1){
	                	$links += '<a href="'+base_url+'/'+$manager+'/'+data[$primary]+'">Edit</a>';
	            	}

	            	return $links;
	            }
	        }],
	    });
	});
});