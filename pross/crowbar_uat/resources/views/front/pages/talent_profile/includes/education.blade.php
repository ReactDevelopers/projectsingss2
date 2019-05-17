@if(!empty($education_list))
    @foreach($education_list as $key => $item)
        <div class="col-md-6 col-sm-12 col-xs-12" id="box-{{$item['id_education']}}"> 
            <div class="educationEditSec"> 
                <span class="company-profile-tag">
                    @if(!empty($item['logo']))
                        <img src="{{$item['logo']}}">
                    @else
                        <img src="{{asset('/images/ge-icon.png')}}">
                    @endif
                </span>
                {{-- <div class="addIcons"> 
                    <a href="javascript:void(0);" class="edit-icon" title="Edit" data-url="{{sprintf(url('ajax/%s?id_education=%s'), EDIT_TALENT_EDUCATION, $item['id_education'] )}}" data-request="edit" data-education_id="{{$item['id_education']}}" data-edit-id="education_id">
                        <img src="{{asset('/images/edit-icon.png')}}">
                    </a> 
                    <a href="javascript:void(0);" title="Delete" data-url="{{sprintf(url('ajax/%s?id_education=%s'), DELETE_TALENT_EDUCATION, $item['id_education'] )}}" data-request="delete" data-education_id="{{$item['id_education']}}" data-edit-id="education_id" data-toremove="box" data-ask="Do you realy want to delete your education?">
                        <img src="{{asset('/images/delete-icon.png')}}">
                    </a>
                </div> --}} 
                <ul>
                    <li>
                        <span>{{trans('website.W0082')}}</span>
                        <span>{{$item['college']}}</span>
                    </li>
                    <li>
                        <span>{{trans('website.W0086')}}</span>
                        <span>{{$item['passing_year']}}</span>
                    </li>
                    <li>
                        <span>{{trans('website.W0084')}}</span>
                        <span>{{___cache('degree_name')[$item['degree']]}} ({{$item['area_of_study']}})</span>
                    </li>
                </ul>
            </div>
        </div>
    @endforeach
@else
    <div class="col-md-12">{{N_A}}</div>
@endif