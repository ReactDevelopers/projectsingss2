<div class="col-md-4 col-sm-4 left-sidebar clearfix">
    <?php if(count($talent['talentCompany']) > 0 && $talent['company_profile'] == 'company' ): ?>
        <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
            <div class="profile-left">
                <div class="user-profile-image">
                    <div class="user-display-details">
                        <div class="user-display-image" style="background: url('<?php echo e(@$talent['talentCompany']->company_logo); ?>') no-repeat center center;background-size:100% 100%"></div>
                    </div>
                </div>
            </div>
            <div class="profile-right no-padding">
                <div class="user-profile-details">
                    <div class="item-list">
                        <div class="rating-review">
                            <span class="rating-block">
                                <?php echo ___ratingstar($talent['rating']); ?>

                            </span>
                            <a href="<?php echo e(url(sprintf('%s/find-talents/reviews?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($talent['id_user'])))); ?>" class="reviews-block underline"><?php echo e($talent['review']); ?> <?php echo e(trans('website.W0213')); ?></a>
                        </div>
                    </div>
                    <div class="item-list">
                        <span class="item-heading"><?php echo e(trans('website.W0992')); ?></span>
                        <span class="item-description">
                            <?php echo e(!empty($talent['countries']) ? $talent['countries'] : ''); ?>

                        </span>
                    </div>
                    <div class="item-list">
                        <span class="item-heading"><?php echo e(trans('website.W0993')); ?></span>
                        <span class="item-description"><?php echo e($talent['connectedTalent']); ?></span>
                    </div>
                </div>        
            </div>
            <div class="view-profile-name">
                <div class="user-name-info">
                    <p><?php echo e($talent['company_name']); ?></p>
                </div>
                
            </div>
        </div>
    <?php endif; ?>
    <?php if($talent['show_profile']=='yes' || $talent['company_profile'] == 'individual'): ?>
        <div class="user-info-wrapper user-info-greyBox viewProfileBox clearfix">
            <div class="profile-left">
                <div class="user-profile-image">
                    <div class="user-display-details">
                        <div class="user-display-image" style="background: url('<?php echo e($talent['picture']); ?>') no-repeat center center;background-size:100% 100%"></div>
                    </div>
                </div>
            </div>
            <div class="profile-right no-padding">
                <div class="user-profile-details">
                    <div class="item-list">
                        <div class="rating-review">
                            <span class="rating-block">
                                <?php echo ___ratingstar($talent['rating']); ?>

                            </span>
                            <a href="<?php echo e(url(sprintf('%s/find-talents/reviews?talent_id=%s',EMPLOYER_ROLE_TYPE,___encrypt($talent['id_user'])))); ?>" class="reviews-block underline"><?php echo e($talent['review']); ?> <?php echo e(trans('website.W0213')); ?></a>
                        </div>
                    </div>
                    <?php if(!empty($talent['remuneration'])): ?>
                        <div class="item-list">
                            <span class="item-heading"><?php echo e(trans('website.W0660')); ?></span>
                            <span class="item-description">
                                <?php $__currentLoopData = $talent['remuneration']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <?php if($item['interest'] != 'fixed'): ?>
                                        <?php echo e(sprintf('%s/%s',___currency($item['converted_price'],true,true),substr($item['interest'],0,1))); ?>

                                    <?php else: ?>
                                        <span class="label-green color-grey"><?php echo e($item['interest']); ?></span>
                                    <?php endif; ?>
                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!empty($talent['country_name'])): ?>
                        <div class="item-list">
                            <span class="item-heading"><?php echo e(trans('website.W0201')); ?></span>
                            <span class="item-description"><?php echo e($talent['country_name']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>        
            </div>
            <div class="profile-completion-block profile-completion-list">
                <div class="clearfix"></div>
            </div>
            <div class="view-profile-name">
                <div class="user-name-info">
                    <p><?php echo e(sprintf("%s %s",$talent['first_name'],$talent['last_name'])); ?></p>
                </div>
                <div class="profile-expertise-column">
                    <?php if(!empty($talent['is_saved'])): ?>
                        <a href="javascript:void(0);" class="save-icon active" data-request="favorite-save" data-url="<?php echo e(url('employer/save?talent_id='.$talent['id_user'])); ?>"></a>
                    <?php else: ?>
                        <a href="javascript:void(0);" class="save-icon" data-request="favorite-save" data-url="<?php echo e(url('employer/save?talent_id='.$talent['id_user'])); ?>"></a>
                    <?php endif; ?>
                    <?php if(!empty($talent['expertise'])): ?>
                        <span class="label-green color-grey"><?php echo e(expertise_levels($talent['expertise'])); ?></span>
                    <?php endif; ?>
                    <?php if(!empty($talent['expertise'])): ?>
                        <span class="experience"><?php echo e(!empty($talent['experience']) ? sprintf("%s %s",$talent['experience'],trans('website.W0669')) : ''); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php 
                $shareUrl = '';
                if(!empty($talent['first_name']) && !empty($talent['id_user'])){
                    $shareUrl = url('/showprofile/'.strtolower($talent['first_name']).'-'.strtolower($talent['last_name']).'/'.$talent['id_user']);
                } 
             ?>
            <?php if(!empty($shareUrl)): ?>
                <br/>
                <div class="talent-share-profile clearfix">
                    <span class="item-description talent-share-links"><?php echo e(trans('website.W0947')); ?></span>
                    <ul>
                        <li>
                            <a href="javascript:void(0);" class="linkdin_icon_talent">
                                <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
                                <script type="IN/Share" data-url="<?php echo e($shareUrl); ?>"></script>
                                <img src="<?php echo e(asset('images/linkedin.png')); ?>">
                            </a>
                        </li>
                        <li>
                            <a class="fb_icon" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e($shareUrl); ?>" target="_blank"><img src="<?php echo e(asset('images/facebook.png')); ?>">
                            </a>
                        </li>
                        <li>
                            <a href="https://web.whatsapp.com/send<?php echo e($shareUrl); ?>" target="_blank" id="whatsapp_link" data-action="share/whatsapp/share" ><img src="<?php echo e(asset('images/whatsapp-logo.png')); ?>"></a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if(!empty($completed_jobs)): ?>
        <div class="talent-profile-section completed-jobs-list" style="display: none;">
            <h2 class="form-heading small-heading"><?php echo e(trans('website.W0671')); ?></h2>
            <div class="view-information" id="personal-infomation">
                <div class="no-table datatable-listing shift-up-5px">
                    <?php echo $html->table();; ?>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php 
        $shareUrl = '';
        if(!empty($talent['first_name']) && !empty($talent['id_user'])){
            $shareUrl = url('/showprofile/'.strtolower($talent['first_name']).'-'.strtolower($talent['last_name']).'/'.$talent['id_user']);
        } 
     ?>
            
</div>
<?php $__env->startPush('inlinescript'); ?>
<script type="text/javascript">
    $(document).ready(function(){
        // console.log("page ready");
        // console.log("link is- "+'<?php echo e($shareUrl); ?>');
        //Change Whatsapp link according to Web or Mobile
        var isMobile1 = window.orientation > -1;
        isMobile1 = isMobile1 ? 'Mobile' : 'Not mobile';
        if(isMobile1 == 'Mobile'){
            //Whatsapp Mobile link share
            $('#whatsapp_link').attr('href','whatsapp://send?text=<?php echo e($shareUrl); ?>');
        }else{
            //Whatsapp Web link share
            $('#whatsapp_link').attr('href','https://web.whatsapp.com/send?text=<?php echo e($shareUrl); ?>');
        }
    });
</script>
<?php $__env->stopPush(); ?>