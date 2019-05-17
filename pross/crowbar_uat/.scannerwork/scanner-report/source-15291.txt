@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="form-add-workfield" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'workfields/add'))}}" method="post">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="question">Work Field</label>
                                <input type="text" class="form-control" name="field_name" value="{{ !empty($workfield) ? $workfield->field_name : '' }}" placeholder="{{ trans('admin.A0088') }}" style="width:100%;"/>
                            </div>
                        </div>
                        <input type="hidden" name="id_workfield" value="{{ !empty($workfield) ? ___encrypt($workfield->id_workfield) : ''}}">
                    </form>
                    <div class="panel-footer">
                        <div class="row form-group button-group">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row form-btn-set">
                                    <div class="col-md-12 col-sm-5 col-xs-6">
                                        <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                                        <button 
                                            type="button" 
                                            data-request="ajax-submit" 
                                            data-target='[role="form-add-workfield"]'
                                            class="btn btn-default">
                                            Save
                                        </button>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </section>
@endsection
{{-- @push('inlinescript')
    <script type="text/javascript">
        @if(!empty($workfield->parent_industry))
            $(window).load(function(){
                $('[name="industry"]').trigger('change');
            });
        @endif
    </script>
@endpush --}}