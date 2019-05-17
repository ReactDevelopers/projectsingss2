@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="form-add-skill" action="{{url(sprintf("%s/%s",ADMIN_FOLDER,'skill/add'))}}" method="post">
                        <div class="panel-body">                       
                            @if(0)
                                <div class="form-group">
                                    <label for="question">INDUSTRY</label>
                                    <div>
                                        <select class="form-control" name="industry_id">
                                            {!! ___dropdown_options($subindustries_name,trans("admin.A0018"),!empty($skill) ? $skill->industry_id : '') !!}
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="question">Skill</label>
                                <input type="text" class="form-control" name="skill_name" maxlength="{{ TAG_LENGTH }}" value="{{ !empty($skill) ? $skill->skill_name : '' }}" placeholder="{{ trans('admin.A0051') }}" style="width:100%;"/>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <input type="hidden" name="id_skill" value="{{ !empty($skill) ? ___encrypt($skill->id_skill) : ''}}">
                            <input type="hidden" name="action" value="submit">
                            <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="form-add-skill"]' class="btn btn-default">Save</button>
                        </div>                                            
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
{{-- @push('inlinescript')
    <script type="text/javascript">
        @if(!empty($skill->parent_industry))
            $(window).load(function(){
                $('[name="industry"]').trigger('change');
            });
        @endif
    </script>
@endpush --}}