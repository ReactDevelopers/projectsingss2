@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('banner::banners.title.banners') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('banner::banners.title.banners') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.banner.banner.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('banner::banners.button.create banner') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="data-table table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>{{ trans('banner::banners.table.id') }}</th>
                            <th>{{ trans('banner::banners.table.title') }}</th>
                            <th>{{ trans('banner::banners.table.photo') }}</th>
                            <th>{{ trans('banner::banners.table.status') }}</th>
                            <th>{{ trans('banner::banners.table.type') }}</th>
                            <th>{{ trans('banner::banners.table.created_at') }}</th>
                            <th>{{ trans('banner::banners.table.updated_at') }}</th>
                            <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($banners)): ?>
                        <?php foreach ($banners as $banner): ?>
                        <tr>
                            <td>{{ $banner->id }}</td>
                            <td>{{ $banner->title }}</td>
                            <td class="cell-image"><img src="{{ ($banner) ? ($banner->path ? MediaService::getImage($banner->path) : URL::to('/') . '/assets/media/no-image.png' ) : URL::to('/') . '/assets/media/no-image.png' }}" width="auto" height="100px"></td>
                            <td>{{ ($banner->status==0) ? 'InActive' : 'Active' }}</td>
                            <td>{{ ($banner->type==0) ? 'External' : 'Filter'  }}</td>
                            <td>{{ $banner->created_at && $banner->created_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($banner->created_at)->format('d/m/Y H:i:s') : "" }}</td>
                            <td>{{ $banner->updated_at && $banner->updated_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($banner->updated_at)->format('d/m/Y H:i:s') : "" }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.banner.banner.edit', [$banner->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                    <button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.banner.banner.destroy', [$banner->id]) }}"><i class="fa fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>{{ trans('core::core.table.created at') }}</th>
                            <th>{{ trans('core::core.table.actions') }}</th>
                        </tr>
                        </tfoot>
                    </table>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    @include('core::partials.delete-modal')
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('banner::banners.title.create banner') }}</dd>
    </dl>
@stop

@section('scripts')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.banner.banner.create') ?>" }
                ]
            });
        });
    </script>
    <?php $locale = locale(); ?>
    <script type="text/javascript">
        $(function () {
            $('.data-table').dataTable({
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
