<div class="mailbox-read-message">
    <div class="form-group">
        <b>Job Name:</b><br>
        <span>{{$project_title}}</span><br/> 
        <span><b>Start Date: </b>{{___d($project_start_date)}}</span> 
        <br/>
        <span><a href="{{url(sprintf('%s/project/detail/%s',ADMIN_FOLDER,$id_project))}}" target="_blank">Details</a></span> 
    </div>
    <div class="form-group">
        <b>Job Posted by:</b><br>
        <span>{{!empty($project_employer['company_name']) ? 'Company Name - '.$project_employer['company_name']:'' }}</span><br>
        <span>{{ !empty($project_employer['name']) ? 'Name - '.$project_employer['name']:''}}</span> 
    </div>
    <div class="form-group">
        <b>Proposals received:</b><br>
        @if(!empty($project_received_proposals))
            <ul>
                @foreach($project_received_proposals as $key=>$val)
                    @php
                        if(!empty($project_accepted_proposal) && $project_accepted_proposal == $val['user_id'] ){
                            $accepted_proposal = '<b style="color:#2C8C3C"> (Accepted)</b>';
                        }else{
                            $accepted_proposal = '';
                        }
                    @endphp
                    <li>{{$val['fullname']}}{!!$accepted_proposal!!}</li>
                @endforeach
            </ul>
            <span><a href="{{url(sprintf('%s/project/detail/%s',ADMIN_FOLDER,$id_project)).'?page=proposal'}}" target="_blank">Details</a></span>
        @else
            <span>No proposals yet.</span>
        @endif 
    </div>
    <div class="form-group">
        <b>Payment completed:</b><br>
        @if(!empty($project_payment_complete))
            <span>Payment done by employer.</span><br/>
            <span><b>Date: </b>{{___d($project_payment_complete['created'])}}</span>
            <br/>
            <span><a href="{{url(sprintf('%s/project/detail/%s',ADMIN_FOLDER,$id_project)).'?page=transactions'}}" target="_blank">Details</a></span> 
        @else
            <span>Payment pending.</span>
        @endif
    </div>
    <div class="form-group">
        <b>Job Started:</b><br>
        @if(!empty($project_start_talent))
            <span>Job started by Talent.</span><br/>
            <span><b>Date: </b>{{___d($project_start_talent['created'])}}</span> 
        @else
            <span>Job start pending.</span>
        @endif
    </div>
    <div class="form-group">
        <b>Job Completed:</b><br>
        @if(!empty($project_complete_talent))
            <span>Job completed by Talent.</span><br/>
            <b>Date</b> <span>{{___d($project_complete_talent_date)}}</span>
        @else
            <span>Job yet to be completed by Talent.</span>
        @endif
    </div>
    <div class="form-group">
        <b>Job Completion:</b><br>
        @if(!empty($project_completion_emp))
            <span>Job completion accepted by Employer.</span><br/>
            <b>Date</b> <span>{{___d($project_completion_emp_date)}}</span> 
        @else
            <span>Job completion pending.</span>
        @endif
    </div>
    @if(!empty($project_raise_dispute))
        <div class="form-group">
            <b style="color:red">Raised Dispute:</b><br>
            <span><a href="{{url(sprintf('%s/raise-dispute/detail?dispute_id=%s',ADMIN_FOLDER,$project_raise_dispute_id))}}" target="_blank">Details</a></span>
        </div>
    @endif
    <div class="form-group">
        <b>Payments Involved:</b><br/>
        @if(!empty($disputeList))
            @foreach($disputeList as $key=>$val)
                @php
                    if($val->user_type == 'employer' && $val->transaction_status == 'confirmed'){
                        echo 'Escrowed- '.PRICE_UNIT.___format($val->transaction_subtotal).'<br/>';
                    }elseif($val->transaction_status == 'refunded'){
                        echo 'Refunded- '.PRICE_UNIT.___format($val->transaction_subtotal).'<br/>';
                    }elseif($val->user_type == 'talent'){
                        echo 'Paid- '.PRICE_UNIT.___format($val->transaction_subtotal).'<br/>';
                    }else{
                        echo 'Failed- '.PRICE_UNIT.___format($val->transaction_subtotal).'<br/>';
                    }           
                @endphp
            @endforeach
            <span><a href="{{url(sprintf('%s/project/detail/%s',ADMIN_FOLDER,$id_project)).'?page=transactions'}}" target="_blank">Details</a></span>
        @endif
    </div>
</div>