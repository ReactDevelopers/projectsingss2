<div class="panel-body">
    <div class="form-group">
        <label for="name">Industry Affiliations</label><br />
        <?php if(!empty(array_column($user['industry'],'name'))): ?>
            <?php echo ___tags(array_column($user['industry'],'name'),'<span class="small-tags">%s</span>',''); ?>

        <?php else: ?>
            <?php echo e(N_A); ?>

        <?php endif; ?>
    </div>
    
    <div class="form-group">
        <label for="name">Specialization</label><br />
        <?php if(!empty(array_column($user['subindustry'],'name'))): ?>
            <?php echo ___tags(array_column($user['subindustry'],'name'),'<span class="small-tags">%s</span>',''); ?>

        <?php else: ?>
            <?php echo e(N_A); ?>

        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="name">Skill</label><br>
        <?php if(!empty($user['skills'])): ?>
            <?php echo ___tags(array_column($user['skills'], 'skill_name'),'<span class="small-tags">%s</span>',''); ?>

        <?php else: ?>
            <?php echo e(N_A); ?>

        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="name">Expertise Level: </label>
        <br /><?php echo e(!empty($user['expertise']) ? ucfirst($user['expertise']) : N_A); ?>

    </div>
    <div class="form-group">
        <label for="name">No. of Years(in Years): </label>
        <br /><?php echo e(!empty($user['experience']) ? $user['experience'] : N_A); ?>

    </div>

    <div class="form-group">
        <label for="name">Comments: </label>
        <br /><?php echo e(!empty($user['workrate_information']) ? $user['workrate_information'] : N_A); ?>

    </div>

    <div class="form-group">
        <label for="name">Certificates: </label><br />
        <?php 
            if(!empty($user['certificate_attachments'])){
                foreach ($user['certificate_attachments'] as $item) {
                    echo sprintf(RESUME_TEMPLATE,
                        $item['id_file'],
                        url(sprintf('/download/file?file_id=%s',___encrypt($item['id_file']))),
                        asset('/'),
                        substr($item['filename'],0,3),
                        $item['size'],
                        '',
                        $item['id_file'],
                        asset('/')
                    );  
                }
            }else{
                echo N_A;
            }
         ?>
    </div>

    <!---Education section-->
    <div class="form-group">
        <label for="name">Education</label>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>School / College</th>
                        <th>Year of Graduation</th>
                        <th>Degree</th>
                        <th>Country</th>
                        <th>Area of Study</th>
                        <th width="10">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $counter = 1;
                     ?>
                    <?php if(!empty($education_list)): ?>
                        <?php $__currentLoopData = $education_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <tr id="list-<?php echo e($e['id_education']); ?>">
                                <td width="3%"><?php echo e($counter++); ?></td>
                                <td><?php echo e($e['college']); ?></td>
                                <td><?php echo e($e['passing_year']); ?></td>
                                <td><?php echo e($e['degree_name']); ?></td>
                                <td><?php echo e($e['degree_country_name']); ?></td>
                                <td><?php echo e($e['area_of_study']); ?></td>
                                <td width="10">
                                    <a href="javascript:;" data-id-edu="<?php echo e($e['id_education']); ?>" data-url="<?php echo e(url('administrator/talent/delete-education/'.$e['id_education'].'/'.$id_user)); ?>" class="badge bg-red delete-edu" >Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                    <?php else: ?>
                        <tr>
                            <th colspan="6">No Records</th>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!---Work section-->
    <div class="form-group">
        <label for="name">Work</label>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Job Title</th>
                        <th>Company Name</th>
                        <th>Start Date</th>
                        <th>Currently Working?</th>
                        <th>Type of Job</th>
                        <th>End Date</th>
                        <th>Country</th>
                        <th>State/ Province</th>
                        <th width="10">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                     ?>
                    <?php if(!empty($user['work_experiences'])): ?>
                        <?php $__currentLoopData = $user['work_experiences']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <tr id="list-<?php echo e($e['id_experience']); ?>">
                                <td width="3%"><?php echo e($counter++); ?></td>
                                <td><?php echo e($e['jobtitle']); ?></td>
                                <td><?php echo e($e['company_name']); ?></td>
                                <td><?php echo e($e['joining']); ?></td>

                                <td><?php echo e($e['is_currently_working'] == 'yes' ? 'Yes' : 'No'); ?></td>
                                <td><?php echo e($e['job_type'] == 'fulltime' ? 'Fulltime' : 'Temporary'); ?></td>
                                <td><?php echo e($e['joining']); ?></td>

                                <td><?php echo e($e['country_name']); ?></td>
                                <td><?php echo e($e['state_name']); ?></td>
                                <td width="10">
                                    <a href="javascript:;" data-url="<?php echo e(url('administrator/talent/delete-experience/'.$e['id_experience'].'/'.$id_user)); ?>" data-id-user="<?php echo e($id_user); ?>" data-id-experience="<?php echo e($e['id_experience']); ?>" class="delete-exp badge bg-red" >Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                    <?php else: ?>
                    <tr>
                        <th colspan="6">No Records</th>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->startPush('inlinescript'); ?>
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

    $('.delete-edu').click(function(){
        var id_edu = $(this).data('id-edu');
        var url = $(this).data('url');
        var res = confirm('Do you really want to continue with this action?');

        if(res){
            $.ajax({
            method: "GET",
            url: url
            })
            .done(function(data) {
                $('#list-'+id_edu).remove();
            });
        }
    });

    $('.delete-exp').click(function(){
        var id_experience = $(this).data('id-experience');
        var url = $(this).data('url');
        var res = confirm('Do you really want to continue with this action?');

        if(res){
            $.ajax({
            method: "GET",
            url: url
            })
            .done(function(data) {
                $('#list-'+id_experience).remove();
            });
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
