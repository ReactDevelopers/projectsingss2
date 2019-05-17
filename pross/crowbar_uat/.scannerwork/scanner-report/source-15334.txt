<div class="mailbox-read-message">
    <div class="form-group">
        <b>Project Type</b><br>
        <span>{{employment_types('post_job',$project_detail['employment'])}}</span> 
    </div>
    <div class="form-group">
        <b>Profession</b><br>
        @if(!empty(array_column(array_column($project_detail['industries'], 'industries'),'name')))
            {!! ___tags(array_column(array_column($project_detail['industries'], 'industries'),'name'),'<span class="small-tags">%s</span>','') !!}
        @else
            {{ N_A }}
        @endif
    </div>
    <b>Industry</b>
    <p>
        @if(!empty(array_column(array_column($project_detail['skills'], 'skills'),'skill_name')))
            {!! ___tags(array_column(array_column($project_detail['skills'], 'skills'),'skill_name'),'<span class="small-tags">%s</span>','') !!}
        @else
            {{ N_A }}
        @endif
    </p>
    <div class="form-group">
        <b>Specialisation</b><br>
        @if(!empty(array_column(array_column($project_detail['subindustries'], 'subindustries'),'name')))
            {!! ___tags(array_column(array_column($project_detail['subindustries'], 'subindustries'),'name'),'<span class="small-tags">%s</span>','') !!}
        @else
            {{ N_A }}
        @endif
    </div>
    <div class="form-group">
        <b>Expertise Level</b><br> 
        <span>{{ !empty($project_detail['expertise']) ? expertise_levels($project_detail['expertise']) : N_A}}</span>
    </div>
    <div class="form-group">
        <b>Timeline</b> <br>
        <span>
            {{___date_difference($project_detail['startdate'],$project_detail['enddate'])}}
        </span>
    </div>
    <br>
    <b>Description</b>
    <p>{!!nl2br($project_detail['description'])!!}</p>                                    
    <br>
</div>

