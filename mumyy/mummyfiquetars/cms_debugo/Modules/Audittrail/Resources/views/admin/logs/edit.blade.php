@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('audittrail::logs.title.edit log') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ URL::route('admin.audittrail.log.index') }}">{{ trans('audittrail::logs.title.logs') }}</a></li>
        <li class="active">{{ trans('audittrail::logs.title.edit log') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
    {!! Theme::style('css/vendor/iCheck/flat/blue.css') !!}
@stop

@section('content')
    {!! Form::open(['route' => ['admin.audittrail.log.update', $log->id], 'method' => 'put' , 'name'=>'editlog', 'id' => 'admin-audittrail-log-edit']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                @include('audittrail::admin.logs.partials.edit-fields')
                <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
                        <a class="btn btn-danger pull-right btn-flat" href="{{ URL::route('admin.audittrail.log.index')}}"><i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('footer')
@stop
@section('shortcuts')
@stop
@section('scripts')
@append