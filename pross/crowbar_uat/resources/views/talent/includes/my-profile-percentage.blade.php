<div class="profile-completion-block">
    <h3>Profile Completion <span>{{ ___decimal($user['profile_percentage_count']) }}%</span></h3>
    <div class="clearfix"></div>
    <div class="completion-bar">
        <span style="width: {{ ___decimal($user['profile_percentage_count'],'no-decimal') }}%;"></span>
    </div>
    <ul class="completion-list-group">
        <li class="completed">Account Creation</li>
        <li class="@if(!empty($user['profile_percentage_step_one'])) {{ 'completed'}} @endif">Personal Information</li>
        <li class="@if(!empty($user['profile_percentage_step_two'])) {{ 'completed'}} @endif">Industry & Skills</li>
        <li class="@if(!empty($user['profile_percentage_step_three'])) {{ 'completed'}} @endif">Curriculum Vitae</li>
        <li class="@if(!empty($user['profile_percentage_step_four'])) {{ 'completed'}} @endif">Availability for hiring</li>
        <li class="@if(!empty($user['profile_percentage_step_five'])) {{ 'completed'}} @endif">Verify Account</li>
    </ul>
</div>