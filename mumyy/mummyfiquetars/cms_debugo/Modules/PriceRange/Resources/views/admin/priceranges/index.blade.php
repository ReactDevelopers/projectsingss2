@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('pricerange::priceranges.title.priceranges') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('pricerange::priceranges.title.priceranges') }}</li>
    </ol>
@stop
@section('styles')
    {!! Theme::style('css/custom/custom.css') !!}
@stop
<?php
    $current_url                    = Request::url();
    $query_url                      = $_SERVER['QUERY_STRING'];
?>
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.pricerange.pricerange.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('pricerange::priceranges.button.create pricerange') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                </div>
                <!-- /.box-header -->
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
                        <div class="col-sm-6 col-md-6">
                            <div style="white-space:nowrap; text-align: right;">
                                <label>
                                    <span>Search: </span><input style="display: inline-block; font-weight: normal;" type="text" name="keyword" value="{{ isset($keyword) ? $keyword : "" }}" class="form-control-right" placeholder="">
                                    <button type="submit" class="form-control btn-primary" style="width: auto; display: inline-block;">Search</button>
                                </label>
                            </div>
                        </div>
                    </div>
                    <table class="data-table table table-bordered table-hover">
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
                            <th>
                                <a class="header-table" href="{{ get_url_query($current_url, $query_url, ['order_field' => 'price_name', 'sort' => $order_field == "price_name" ? ($sort == "DESC" ? "ASC" : "DESC") : "DESC"]) }}" >
                                    {{ trans('core::core.table.name') }}
                                    @if($order_field == 'price_name')
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
                            <th>{{ trans('core::core.table.status') }}</th>
                            <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($priceranges)): ?>
                        <?php foreach ($priceranges as $item): ?>
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->price_name }}</td>
                            <td>{{ Config('constant.status.' . $item->status) }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.pricerange.pricerange.edit', [$item->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                    <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.pricerange.pricerange.destroy', [$item->id]) }}"><i class="fa fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>{{ trans('core::core.table.id') }}</th>
                            <th>{{ trans('core::core.table.name') }}</th>
                            <th>{{ trans('core::core.table.status') }}</th>
                            <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                        </tr>
                        </tfoot>
                    </table>
                    <!-- /.box-body -->
                    <div class="row">
                        <div class="col-sm-5" style="margin-top: 15px;"> 
                            <span>Showing {{ $start }} to {{ $offset }} of {{ $count }} entries</span>
                        </div>
                        <div class="col-sm-7" style="text-align: right">
                            {!! $priceranges->appends(['limit' => $limit, 'keyword' => $keyword, 'page' => $page])->render() !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    @include('core::partials.delete-modal')
@stop

@section('footer')
    <!-- <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp; -->
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('pricerange::priceranges.title.create pricerange') }}</dd>
    </dl>
@stop

@section('custom-styles')
    <style>
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
@stop

@section('scripts')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.pricerange.pricerange.create') ?>" }
                ]
            });
        });
    </script>
    <?php $locale = locale(); ?>
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
@stop
