<form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url('administrator/talent-users/'.$user['id_user'].'/update') }}">
    <input type="hidden" name="_method" value="PUT">
    {{ csrf_field() }}

    <div class="panel-body">
        <div class="form-group @if ($errors->has('first_name'))has-error @endif">
            <label for="name">First Name</label>
            <input type="text" class="form-control" name="first_name" placeholder="First Name" value="{{ (old('first_name'))?old('first_name'):$user['first_name'] }}">
            @if ($errors->first('first_name'))
                <span class="help-block">
                    {{ $errors->first('first_name')}}
                </span>
            @endif
        </div>
        <div class="form-group @if ($errors->has('last_name'))has-error @endif">
            <label for="name">Last Name</label>
            <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{ (old('last_name'))?old('last_name'):$user['last_name'] }}">
            @if ($errors->first('last_name'))
                <span class="help-block">
                    {{ $errors->first('last_name')}}
                </span>
            @endif
        </div>
        <div class="form-group @if ($errors->has('email'))has-error @endif">
            <label for="name">Email</label>
            <input readonly="readonly" type="text" class="form-control" name="email" placeholder="Email" value="{{ (old('email'))?old('email'):$user['email'] }}">
            @if ($errors->first('email'))
                <span class="help-block">
                    {{ $errors->first('email')}}
                </span>
            @endif
        </div>
        <div class="form-group @if ($errors->has('birthday'))has-error @endif">
            <label for="name">Date of Birth</label>
            @php $birthday = old('birthday')?:($user['birthday']?:''); @endphp
            <input id="birthday" type="text" class="form-control" name="birthday" placeholder="Date of Birth" value="{{ $birthday ? date('d-m-Y',strtotime($birthday)):'' }}">
            @if ($errors->first('birthday'))
                <span class="help-block">
                    {{ $errors->first('birthday')}}
                </span>
            @endif
        </div>
        <div class="form-group @if ($errors->has('gender'))has-error @endif">
            <label for="name">Gender</label>
            @php
            if(old('gender')){
                $gender = old('gender');
            }
            else{
                $gender = $user['gender'];
            }
            @endphp
            <select class="form-control" name="gender" placeholder="Gender">
                <option {{$gender=='male'?' selected="selected"':''}} value="male">Male</option>
                <option {{$gender=='female'?' selected="selected"':''}} value="female">Female</option>
                <option {{$gender=='other'?' selected="selected"':''}} value="other">Other</option>
            </select>
            @if ($errors->first('gender'))
                <span class="help-block">
                    {{ $errors->first('gender')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('country_code'))has-error @endif">
            <label for="name">Country Code</label>
            @php
            if(old('country_code')){
                $country_code = old('country_code');
            }
            else{
                $country_code = $user['country_code'];
            }
            @endphp
            <div>
                <select class="form-control" name="country_code" placeholder="Country Code">
                </select>
            </div>
            @if ($errors->first('country_code'))
                <span class="help-block">
                    {{ $errors->first('country_code')}}
                </span>
            @endif
        </div>
        <div class="form-group @if ($errors->has('mobile'))has-error @endif">
            <label for="name">Mobile</label>
            <input type="text" class="form-control" name="mobile" placeholder="Mobile" value="{{ (old('mobile'))?old('mobile'):$user['mobile'] }}">
            @if ($errors->first('mobile'))
                <span class="help-block">
                    {{ $errors->first('mobile')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('address'))has-error @endif">
            <label for="name">Address</label>
            <textarea class="form-control" name="address" placeholder="Address">{{ (old('address'))?old('address'):$user['address'] }}</textarea>
            @if ($errors->first('address'))
                <span class="help-block">
                    {{ $errors->first('address')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('country'))has-error @endif">
            <label for="name">Country</label>
            <select class="form-control" name="country" id="country" data-url="{{ url('ajax/state-list') }}" placeholder="Country">
            </select>
            @if ($errors->first('country'))
                <span class="help-block">
                    {{ $errors->first('country')}}
                </span>
            @endif
        </div>
        <div class="form-group @if ($errors->has('state'))has-error @endif">
            <label for="name">State</label>
            <select class="form-control" name="state" id="state" placeholder="State" data-url="{{ url('ajax/city-list') }}">
            </select>
            @if ($errors->first('state'))
                <span class="help-block">
                    {{ $errors->first('state')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('city'))has-error @endif">
            <label for="name">City</label>
            <select class="form-control" name="city" id="city" placeholder="State">
            </select>
            @if ($errors->first('city'))
                <span class="help-block">
                    {{ $errors->first('city')}}
                </span>
            @endif
        </div>

        <div class="form-group @if ($errors->has('postal_code'))has-error @endif">
            <label for="name">Postal Code</label>
            <input type="text" class="form-control" name="postal_code" placeholder="Postal Code" value="{{ (old('postal_code'))?old('postal_code'):$user['postal_code'] }}">
            @if ($errors->first('postal_code'))
                <span class="help-block">
                    {{ $errors->first('postal_code')}}
                </span>
            @endif
        </div>
    </div>
    <div class="panel-footer">
        <a href="{{ $backurl }}" class="btn btn-default">Back</a>
        <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Save</button>
    </div>
</form>
@section('inlinecss')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection
@push('inlinescript')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            setTimeout(function(){
                $('[name="country_code"]').select2({
                    formatLoadMore   : function() {return 'Loading more...'},
                    ajax: {
                        url: base_url+'/country_phone_codes',
                        dataType: 'json',
                        data: function (params) {
                            var query = {
                                search: params.term,
                                type: 'public'
                            }
                            return query;
                        }
                    },
                    data: [{
                        id: '{{$user['country_code']}}',
                        text: '{{ !empty($user['country_code']) ? sprintf('%s (%s)',$user['country_code_name'],$user['country_code']) : ''}}'
                    }],
                    placeholder: function(){
                        $(this).find('option[value!=""]:first').html();
                    }
                });

                $('[name="country"]').select2({
                    formatLoadMore   : function() {return 'Loading more...'},
                    ajax: {
                        url: base_url+'/countries',
                        dataType: 'json',
                        data: function (params) {
                            var query = {
                                search: params.term,
                                type: 'public'
                            }
                            return query;
                        }
                    },
                    data: [{id: '{{$user['country']}}', text: '{{$user['country_name']}}'}],
                    placeholder: function(){
                        $(this).find('option[value!=""]:first').html();
                    }
                }).on('change',function(){
                    $('[name="state"]').val('').trigger('change');
                    $('[name="city"]').val('').trigger('change');
                });


                $('[name="state"]').select2({
                    formatLoadMore   : function() {return 'Loading more...'},
                    ajax: {
                        url: base_url+'/states',
                        dataType: 'json',
                        data: function (params) {
                            var query = {
                                country: $('[name="country"]').val(),
                                search: params.term,
                                type: 'public'
                            }
                            return query;
                        }
                    },
                    data: [{id: '{{$user['state']}}', text: '{{$user['state_name']}}'}],
                    placeholder: function(){
                        $(this).find('option[value!=""]:first').html();
                    }
                }).on('change',function(){
                    $('[name="city"]').val('').trigger('change');
                });

                $('[name="city"]').select2({
                    ajax: {
                        url: base_url+'/cities',
                        dataType: 'json',
                        data: function (params) {
                            var query = {
                                state: $('[name="state"]').val(),
                                search: params.term,
                                type: 'public'
                            }
                            return query;
                        }
                    },
                    data: [{id: '{{$user['city']}}', text: '{{$user['city_name']}}'}],
                    placeholder: function(){
                        $(this).find('option[value!=""]:first').html();
                    }
                });
            },1000);

            $( "#birthday" ).datepicker({
                dateFormat: 'dd-mm-yy',
                maxDate: new Date({{date('Y')+BIRTHDAY_MIN_YEAR_LIMIT}}, {{date('m')-1}}, {{date('d')}}),
                changeMonth: true,
                yearRange: '-100:+0',
                changeYear: true,
            });
        });
    </script>
@endpush
