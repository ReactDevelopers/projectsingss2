@extends('layouts.master-list-datatable')

@section('content-header')
    <h1>
        {{ trans('audittrail::logs.title.logs') }}

        {{--<small>--}}
            {{--<a href="{{ URL::route('admin.audittrail.log.create') }}" class="btn btn-primary btn-flat">--}}
                {{--{{ trans('core::core.button.add new') }}--}}
            {{--</a>--}}
        {{--</small>--}}
    </h1>

    <ol class="breadcrumb">
        <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ config('asgard.audittrail.log.title.index') }}</li>
    </ol>
@stop