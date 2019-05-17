<form role="form" method="post" enctype="multipart/form-data" action="{{ url($url.'/talent-users/'.$user['id_user'].'/update-industry') }}">
    <input type="hidden" name="_method" value="PUT">
    {{ csrf_field() }}

    <div class="panel-body">
        <div class="form-group @if ($errors->has('industry'))has-error @endif">
            <label for="name">Industry</label>
            @php
            if(old('industry')){
                $industry = old('industry');
            }
            else{
                $industry = $user['industry'];
            }
            @endphp
            <select class="form-control" name="industry" id="industry" data-url="{{ url('ajax/subindustry-list') }}" placeholder="Industry">
                @foreach($industries as $c)
                    <option {{$industry==$c->id_industry?' selected="selected"':''}} value="{{$c->id_industry}}">{{$c->name}}</option>
                @endforeach
            </select>
            @if ($errors->first('industry'))
                <span class="help-block">
                    {{ $errors->first('industry')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('subindustry'))has-error @endif">
            <label for="name">Sub Industry</label>
            @php
            if(old('subindustry')){
                $subindustry = old('subindustry');
            }
            else{
                $subindustry = $user['subindustry'];
            }
            @endphp
            <select class="form-control" name="subindustry" id="subindustry" placeholder="Industry">
                @foreach($subindustries as $c)
                    <option {{$subindustry==$c->id_industry?' selected="selected"':''}} value="{{$c->id_industry}}">{{$c->name}}</option>
                @endforeach
            </select>
            @if ($errors->first('subindustry'))
                <span class="help-block">
                    {{ $errors->first('subindustry')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('skill'))has-error @endif">
            <label for="name">Skill</label>
            <select data-request="tags" multiple="" class="form-control" name="skill[]" placeholder="Skill">
                @foreach($all_skill as $s)
                <option value="{{$s['skill_name']}}"{{in_array($s['skill_name'], $user_skill) ? ' selected="selected"' : ''}}>{{$s['skill_name']}}</option>
                @endforeach
            </select>
            @if ($errors->first('skill'))
                <span class="help-block">
                    {{ $errors->first('skill')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('experience'))has-error @endif">
            <label for="name">Expertise Level</label>
            @php
            if(old('expertise')){
                $expertise = old('expertise');
            }
            else{
                $expertise = $user['expertise'];
            }
            @endphp

            <div class="radio radio-inline">
                <input type="radio" id="expert-novice" class="" name="expertise"
            placeholder="Expertise" value="novice"{{$expertise == 'novice' ? ' checked="checked"' : ''}}>
                <label for="expert-novice">Novice</label>
            </div>

            <div class="radio radio-inline">
                <input type="radio" id="expert-proficient" class="" name="expertise"
            placeholder="Expertise" value="proficient"{{$expertise == 'proficient' ? ' checked="checked"' : ''}}>
                <label for="expert-proficient">Proficient</label>
            </div>

            <div class="radio radio-inline">
                <input type="radio" id="expert-expert" class="" name="expertise"
            placeholder="Expertise" value="expert"{{$expertise == 'expert' ? ' checked="checked"' : ''}}>
                <label for="expert-expert">Expert</label>
            </div>

            @if ($errors->first('expertise'))
                <span class="help-block">
                    {{ $errors->first('expertise')}}
                </span>
            @endif
        </div>
        <div class="form-group @if ($errors->has('experience'))has-error @endif">
            <label for="name">No. of Years(in Years)</label>
            <input type="text" class="form-control" name="experience" placeholder="Experience" value="{{ (old('experience'))?old('experience'):$user['experience'] }}">
            @if ($errors->first('experience'))
                <span class="help-block">
                    {{ $errors->first('experience')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('experience'))has-error @endif">
            <label for="name">Interested In</label>
            <div class="checkbox-wrapper clearfix">
                @foreach(employment_types('talent_personal_information') as $key => $value)
                    <div class="admin-checkbox checkbox checkbox-inline" style="margin-left: 0px;min-width: 100px;">
                        <input type="checkbox" data-request="show-hide-multiple" data-condition="fulltime" data-target="[name='interests']" data-true-condition=".salary-section" data-false-condition=".workrate-section" name="interests[]" {{in_array($value['type'],$interested) ? 'checked' : ''}} id="interests-{{$value['type']}}" value="{{$value['type']}}">
                        <label for="interests-{{$value['type']}}"><span class="check"></span>{{$value['type_name']}}</label>
                    </div>
                @endforeach
            </div>
            @if ($errors->first('experience'))
                <span class="help-block">
                    {{ $errors->first('experience')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('workrate'))has-error @endif">
            <label for="name">Work Rate</label>
            <input type="text" class="form-control" name="workrate" placeholder="Work Rate" value="{{ (old('workrate'))?old('experience'):$user['workrate'] }}">

            <input type="text" class="form-control" name="workrate_max" placeholder="Work Rate" value="{{ (old('workrate_max'))?old('workrate_max'):$user['workrate_max'] }}">

            @php
            if(old('workrate_unit')){
                $workrate_unit = old('workrate_unit');
            }
            else{
                $workrate_unit = $user['workrate_unit'];
            }
            @endphp
            <select class="form-control" name="workrate_unit" id="workrate_unit" data-url="{{ url('ajax/subindustry-list') }}" placeholder="Work Rate">
                <option value="">{{ trans('general.M0255') }}</option>
                <option value="{{ trim(trans('general.M0247')) }}" @if(trim(trans('general.M0247')) == $workrate_unit) selected="selected" @endif>{{ trans('general.M0247') }}</option>
                <option value="{{ trim(trans('general.M0248')) }}" @if(trim(trans('general.M0248')) == $workrate_unit) selected="selected" @endif>{{ trans('general.M0248') }}</option>
                <option value="{{ trim(trans('general.M0249')) }}" @if(trim(trans('general.M0249')) == $workrate_unit) selected="selected" @endif>{{ trans('general.M0249') }}</option>
                <option value="{{ trim(trans('general.M0250')) }}" @if(trim(trans('general.M0250')) == $workrate_unit) selected="selected" @endif>{{ trans('general.M0250') }}</option>
            </select>

            @if ($errors->first('workrate'))
                <span class="help-block">
                    {{ $errors->first('workrate')}}
                </span>
            @endif
            @if ($errors->first('workrate'))
                <span class="help-block">
                    {{ $errors->first('workrate')}}
                </span>
            @endif
            @if ($errors->first('workrate_unit'))
                <span class="help-block">
                    {{ $errors->first('workrate_unit')}}
                </span>
            @endif
        </div>


        <div class="form-group @if ($errors->has('workrate_information'))has-error @endif">
            <label for="name">Other Details</label>
            <textarea class="form-control" name="workrate_information" placeholder="Other Details">{{ (old('workrate_information'))?old('workrate_information'):$user['workrate_information'] }}</textarea>
            @if ($errors->first('workrate_information'))
                <span class="help-block">
                    {{ $errors->first('workrate_information')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('certificate'))has-error @endif">
            <label for="name">Industry Affiliations & Certifications</label>
            <select data-request="tags" multiple="" class="form-control" name="certificate[]" placeholder="Industry Affiliations & Certifications">
                @foreach($certificate_list as $s)
                <option value="{{$s['certificate_name']}}"{{in_array($s['certificate_name'], $user_certificates) ? ' selected="selected"' : ''}}>{{$s['certificate_name']}}</option>
                @endforeach
            </select>
            @if ($errors->first('certificate'))
                <span class="help-block">
                    {{ $errors->first('certificate')}}
                </span>
            @endif
        </div>
    </div>
    <div class="panel-footer">
        <a href="{{url($backurl.'/user-list?page=talent')}}" class="btn btn-default">Back</a>
        <button type="submit" class="btn btn-default">Save</button>
    </div>
</form>
@push('inlinescript')
<script type="text/javascript">
$(document).ready(function(){
    $('#industry').change(function(){
        var industry = $('#industry').val();
        var url = $('#industry').data('url');
        if(industry > 0){
            $.ajax({
            method: "POST",
            url: url,
            data: { record_id: industry}
            })
            .done(function(data) {
                $('#subindustry').html(data);
            });
        }
    });
});
</script>
@endpush
