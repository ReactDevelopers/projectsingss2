@if($if_added_member != 'rejected')
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="member-info">
            <div class="member_profile {{($if_added_member != 'accepted')? '' : 'red'}}">
                <img src="{{$picture}}">
            </div>
            <div class="member_detail">
                <h6><a href="{{url(sprintf("%s/view/%s",TALENT_ROLE_TYPE,___encrypt($id_user)))}}">{{$name}}</a></h6>
                @if($industry_name !='' && $country!='')
                    <p>{{$industry_name}} {{'('.$country.')'}}</p>
                @endif
                <span>{{trans('website.W0439')}} {{date('jS F Y',strtotime($created))}}</span>
                <br>

                @if($if_added_member == '' && $if_added_member2 == '')
                    <a class="hire-me" data-target="#add-member" data-request="ajax-modal" data-url="{{ url(sprintf('%s/add-to-circle?talent_id=%s&user_name=%s',TALENT_ROLE_TYPE,$id_user,$name)) }}" href="javascript:void(0);"><img src="{{ asset('images/add.png') }}">{{trans('website.W0899')}}</a>
                @elseif($if_added_member == 'pending')
                    <a  href="javascript:void(0);">Request pending</a>
                @elseif($if_added_member2 == 'pending')
                    <a  href="javascript:void(0);">Request pending</a>
                @else
                    <a href="javascript:void(0);"><img src="{{ asset('images/member-added.png') }}">{{trans('website.W0900')}}</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif