@extends('layouts.backend.dashboard')

@section('requirecss')
    <link rel="stylesheet" type="text/css" href="{{asset('backend/plugins/iCheck/square/square.css')}}">
@endsection

@section('requirejs')
    <script src="{{ asset('backend/plugins/iCheck/icheck.min.js') }}"></script>
@endsection

@section('inlinejs')
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square',
                radioClass: 'iradio_square',
                increaseArea: '20%'
            }); 
        });
    </script>
@endsection

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="edit-plan" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/plan/edit?id_plan=%s',ADMIN_FOLDER,___encrypt($planData->id_plan))) }}">
                        {{ csrf_field() }}
                        <div class="panel-body">
                            <div class="form-group">
                                <input type="hidden" name="plan_features">
                            </div>
                            @foreach($features as $key => $item)
                                <div class="checkbox icheck">
                                    <label>
                                        <input style="display: none;" type="checkbox" name="features[]" value="{{ $item->id_feature }}" @if(in_array($item->id_feature,$planFeatures))  checked @endif /> {{ $item->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        </div>                       
                        <div class="panel-footer">
                            <a href="{{url($backurl)}}" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="edit-plan"]' class="btn btn-default">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection



