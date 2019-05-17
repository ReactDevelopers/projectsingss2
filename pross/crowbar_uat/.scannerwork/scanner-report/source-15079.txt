@if(!empty($work_experience_list))
    @foreach($work_experience_list as $key => $item)
        <div class="col-md-6 col-sm-12 col-xs-12 experience-white-box" id="experience-{{$item['id_experience']}}">
            <div class="educationEditSec">
                <span class="company-profile-tag">
                    @if(!empty($item['logo']))
                        <img src="{{$item['logo']}}">
                    @else
                        <img src="{{asset('/images/ge-icon.png')}}">
                    @endif
                </span>
                <div class="addIcons">
                    <a href="javascript:void(0);" class="edit-icon" title="Edit" data-url="{{sprintf(url('ajax/%s?id_experience=%s'), EDIT_TALENT_EXPERIENCE, $item['id_experience'] )}}" data-request="edit" data-experience_id="{{$item['id_experience']}}" data-edit-id="experience_id">
                        <img src="{{asset('/images/edit-icon.png')}}">
                    </a>
                    <a href="javascript:void(0);" data-edit-id="experience_id" title="Delete" data-url="{{sprintf(url('ajax/%s?id_experience=%s'), DELETE_TALENT_EXPERIENCE, $item['id_experience'] )}}" data-request="delete" data-experience_id="{{$item['id_experience']}}" data-delete-id="experience_id" data-toremove="experience" data-ask="Do you realy want to delete your experience?">
                        <img src="{{asset('/images/delete-icon.png')}}">
                    </a>
                </div>
                <ul>
                    <li>
                        <span>{{trans('website.W0094')}}</span>
                        <span>{{$item['jobtitle']}}</span>
                    </li>
                    <li>
                        <span>{{trans('website.W0096')}}</span>
                        <span>{{$item['company_name']}}</span>
                    </li>
                    <li>
                        <span>{{trans('website.W0368')}}</span>
                        <span>
                            {{date('F',strtotime(sprintf("%s-%s-%s",'2017',$item['joining_month'],'01')))}}, {{$item['joining_year']}}
                            @if($item['is_currently_working'] == 'yes')
                                {{trans('website.W0670')}}{{trans('website.W0230')}}
                            @else
                                {{trans('website.W0670')}}{{date('F',strtotime(sprintf("%s-%s-%s",'2017',$item['relieving_month'],'01')))}}, {{$item['relieving_year']}}
                            @endif
                            ({{employment_types('talent_curriculum_vitae',$item['job_type'])}})
                        </span>
                    </li>
                    <li>
                        <span>{{trans('website.W0201')}}</span>
                        <span>
                            {{$item['country_name']}}@if($item['state_name'] != N_A), {{$item['state_name']}} @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    @endforeach
@else
    <div class="col-md-12">{{N_A}}</div>
@endif