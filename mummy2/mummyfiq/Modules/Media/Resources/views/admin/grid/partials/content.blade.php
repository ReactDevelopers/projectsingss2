<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ trans('media::media.file picker') }}</title>
    {!! Theme::style('vendor/bootstrap/dist/css/bootstrap.min.css') !!}
    {!! Theme::style('vendor/admin-lte/dist/css/AdminLTE.css') !!}
    {!! Theme::style('vendor/datatables.net-bs/css/dataTables.bootstrap.min.css') !!}
    <link href="{!! Module::asset('media:css/dropzone.css') !!}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background: #ecf0f5;
            margin-top: 20px;
        }
        .dropzone {
            border: 1px dashed #CCC;
            min-height: 227px;
            margin-bottom: 20px;
            display: none;
        }

        td.cell-image{
            overflow: auto;
            width: 250px !important;
            height: 100px !important;
            display: block;
        }
        .span-header-table{
            float: right;
            color: grey;
            margin-top: 3px;
        }
        a { 
            color: black; 
        } 
        a:hover { 
            color: black; 
        } 
        a:selected { 
            color: black; 
        } 

    </style>
    @include('partials.asgard-globals')
</head>
<body>
<?php
    $current_url                    = Request::url();
    $query_url                      = $_SERVER['QUERY_STRING'];
?>
<div class="container">
    <div class="row">
        <form method="POST" class="dropzone">
            {!! Form::token() !!}
        </form>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ trans('media::media.choose file') }}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool jsShowUploadForm" data-toggle="tooltip" title="" data-original-title="Upload new">
                        <i class="fa fa-cloud-upload"></i>
                        Upload new
                    </button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open(array('url' => Request::fullUrl(), 'method'=>"GET", 'name' => "form_searchResult", 'id' => "form_searchResult") ) !!}
                <div class="row">
                    <div class="col-sm-6 col-md-6">
                        <div style="white-space:nowrap;" id="dataTables_length">
                            <label>
                            <span>Show</span> <select style="display:inline-block; font-weight: normal;" name="limit" aria-controls="DataTables_Table_0" class="form-control input-sm">
                                    <option value="10" @if($limit == 10) selected @endif>10</option>
                                    <option value="25" @if($limit == 25) selected @endif>25</option>
                                    <option value="50" @if($limit == 50) selected @endif>50</option>
                                    <option value="100" @if($limit == 100) selected @endif>100</option>
                                </select> <span>entries</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-5 col-md-">
                        <div style="white-space:nowrap; text-align: right;">
                            <label><span>Search: </span><input style="display: inline-block; font-weight: normal;" type="text" name="keyword" value="{{ isset($keyword) ? $keyword : "" }}" class="form-control" placeholder=""></label>
                        </div>
                    </div>
                </div>
                <table class="data-table table table-bordered table-hover jsFileList data-table">
                    <thead>
                    <tr>
                        <th>
                            <a class="header-table" href="{{ get_url_query($current_url, $query_url, ['order_field' => 'id', 'sort' => $order_field == "id" ? ($sort == "DESC" ? "ASC" : "DESC") : "DESC"]) }}" >
                                {{ trans('core::core.table.id') }}
                                @if($order_field == 'id')
                                    @if($sort == 'DESC')
                                        <span class="fa fa-sort-amount-desc span-header-table"></span>
                                    @else
                                        <span class="fa fa-sort-amount-asc span-header-table"></span>
                                    @endif
                                @else
                                    <span class="fa fa-sort span-header-table"></span>
                                @endif
                            </a>
                        </th>
                        <th>{{ trans('core::core.table.thumbnail') }}</th>
                        <th>
                            <a class="header-table" href="{{ get_url_query($current_url, $query_url, ['order_field' => 'filename', 'sort' => $order_field == "filename" ? ($sort == "DESC" ? "ASC" : "DESC") : "DESC"]) }}" >
                                {{ trans('media::media.table.filename') }}
                                @if($order_field == 'filename')
                                    @if($sort == 'DESC')
                                        <span class="fa fa-sort-amount-desc span-header-table"></span>
                                    @else
                                        <span class="fa fa-sort-amount-asc span-header-table"></span>
                                    @endif
                                @else
                                    <span class="fa fa-sort span-header-table"></span>
                                @endif
                            </a>
                            
                        </th>
                        <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($files): ?>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td>{{ $file->id }}</td>
                            <td>
                                <?php if ($file->isImage()): ?>
                                <img src="{{ Imagy::getThumbnail($file->path, 'smallThumb') }}" alt=""/>
                                <?php else: ?>
                                <i class="fa fa-file" style="font-size: 20px;"></i>
                                <?php endif; ?>
                            </td>
                            <td>{{ $file->filename }}</td>
                            <td>
                                <div class="btn-group">
                                    <?php if ($isWysiwyg === true): ?>
                                    <button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        {{ trans('media::media.insert') }} <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <?php foreach ($thumbnails as $thumbnail): ?>
                                        <li data-file-path="{{ Imagy::getThumbnail($file->path, $thumbnail->name()) }}"
                                            data-id="{{ $file->id }}" class="jsInsertImage">
                                            <a href="">{{ $thumbnail->name() }} ({{ $thumbnail->size() }})</a>
                                        </li>
                                        <?php endforeach; ?>
                                        <li class="divider"></li>
                                        <li data-file-path="{{ $file->path }}" data-id="{{ $file->id }}" class="jsInsertImage">
                                            <a href="">Original</a>
                                        </li>
                                    </ul>
                                    <?php else: ?>
                                    <a href="" class="btn btn-primary jsInsertImage btn-flat" data-id="{{ $file->id }}"
                                       data-file-path="{{ Imagy::getThumbnail($file->path, 'mediumThumb') }}">
                                        {{ trans('media::media.insert') }}
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-5" style="margin-top: 15px;"> 
                        <span>Showing {{ $start }} to {{ $offset }} of {{ $count }} entries</span>
                    </div>
                    <div class="col-sm-7" style="text-align: right">
                        {!! $files->appends(['limit' => $limit, 'keyword' => $keyword, 'page' => $page])->render() !!}
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
{!! Theme::script('vendor/jquery/jquery.min.js') !!}
{!! Theme::script('vendor/bootstrap/dist/js/bootstrap.min.js') !!}
{!! Theme::script('vendor/datatables.net/js/jquery.dataTables.min.js') !!}
{!! Theme::script('vendor/datatables.net-bs/js/dataTables.bootstrap.min.js') !!}

<script src="{!! Module::asset('media:js/dropzone.js') !!}"></script>
<?php $config = config('asgard.media.config'); ?>
<script>
    var maxFilesize = '<?php echo $config['max-file-size'] ?>',
        acceptedFiles = '<?php echo $config['allowed-types'] ?>';
</script>
<script src="{!! Module::asset('media:js/init-dropzone.js') !!}"></script>
<script>
    $( document ).ready(function() {
        $('.jsShowUploadForm').on('click',function (event) {
            event.preventDefault();
            $('.dropzone').fadeToggle();
        });
    });
</script>

<?php $locale = App::getLocale(); ?>
<script type="text/javascript">
    $(function () {
        $('#dataTables_length').change(function(){   
            var url_string = window.location.href;
            var url = new URL(url_string);
            // var limit = url.searchParams.get("limit");
            var page = url.searchParams.get("page");
            //var keyword = url.searchParams.get("keyword");

            if(page){
                $('#form_searchResult').append('<input type="hidden" name="page" value="'+page+'">');
            }
            //if(keyword){
            //    $('#form_searchResult').append('<input type="hidden" name="keyword" value="'+keyword+'">');
            //}

            $('#form_searchResult').submit();
        });
        
        $('.data-table1').dataTable({
            "paginate": true,
            "lengthChange": true,
            "filter": true,
            "sort": true,
            "info": true,
            "autoWidth": true,
            "order": [[ 0, "desc" ]],
            "language": {
                "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
            }
        });
    });
</script>
