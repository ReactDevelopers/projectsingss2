@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="form-add-industry" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'plan/add-feature'))}}" method="post">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="question">ENGLISH</label>
                                <input type="text" class="form-control" name="en" value="{{ !empty($features_data) ? $features_data['en'] : '' }}" placeholder="ENGLISH" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">INDONESIA</label>
                                <input type="text" class="form-control" name="id" value="{{ !empty($features_data) ? $features_data['id'] : '' }}" placeholder="INDONESIA" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">MANDARIN</label>
                                <input type="text" class="form-control" name="cz" value="{{ !empty($features_data) ? $features_data['cz'] : '' }}" placeholder="MANDARIN" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">TAMIL</label>
                                <input type="text" class="form-control" name="ta" value="{{ !empty($features_data) ? $features_data['ta'] : '' }}" placeholder="TAMIL" style="width:100%;"/>
                            </div>
                            <div class="form-group">
                                <label for="question">HINDI</label>
                                <input type="text" class="form-control" name="hi" value="{{ !empty($features_data) ? $features_data['hi'] : '' }}" placeholder="HINDI" style="width:100%;"/>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <input type="hidden" name="id_feature" value="{{ !empty($features_data) ? ___encrypt($features_data['id_feature']) : ''}}">
                            <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="form-add-industry"]' class="btn btn-default">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
