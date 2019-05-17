<form method="post" role="find-talents" accept-charset="utf-8" class="form-horizontal m-t-5px" autocomplete="off">
    <div class="contentWrapper job-listing-section job-listing-section-sub">
        <div class="container">
            <div class="row mainContentWrapper">
                <a href="javascript:void(0);" class="sidebar-menu"><span></span>Filter</a>
                <div class="col-md-3 left-sidebar bottom-margin-10px" id="left-sidebar">
                    <div class="sidebar-content">
                        <h3>Filters<span data-request="clear-filter" data-url="<?php echo e(url(sprintf('%s/find-talents',EMPLOYER_ROLE_TYPE))); ?>" id="clearAll">Clear All</span></h3>
                        <div class="filter-options">
                            <h4><?php echo e(trans('website.W0342')); ?></h4>
                            <div class="filter-list-group">
                                <input type="text" name="search" value="<?php echo e(\Request::get('_search')); ?>" placeholder="Search" id="search_talent" class="form-control" data-request="search"/>
                            </div>
                            <h4><?php echo e(trans('website.W1000')); ?></h4>
                            <ul class="filter-list-group">
                                <li>
                                    <div class="checkbox">                
                                        <input type="checkbox" id="individual-profile" name="company_profile_filter" value="individual" data-action="filter">
                                        <label for="individual-profile"><span class="check"></span><?php echo e(trans('website.W0545')); ?></label>
                                    </div>
                                </li>
                                <li>
                                    <div class="checkbox">                
                                        <input type="checkbox" id="company-profile" name="company_profile_filter" value="company" data-action="filter">
                                        <label for="company-profile"><span class="check"></span><?php echo e(trans('website.W0943')); ?></label>
                                    </div>
                                </li>
                            </ul>
                            <h4><?php echo e(trans('website.W0333')); ?></h4>
                            <ul class="filter-list-group">
                                <li>
                                    <div class="checkbox">                
                                        <input type="checkbox" id="saved-talent" name="saved_talent_filter" data-action="filter">
                                        <label for="saved-talent"><span class="check"></span><?php echo e(trans('website.W0334')); ?></label>
                                    </div>
                                </li>
                            </ul>
                            <h4>Employment Type</h4>
                            <ul class="filter-list-group">
                                <?php $__currentLoopData = employment_types('web_talent_personal_information'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li>
                                        <div class="checkbox">                
                                            <input type="checkbox" id="employement-<?php echo e($value['type']); ?>" name="employment_type_filter[]" value="<?php echo e($value['type']); ?>" data-action="filter">
                                            <label for="employement-<?php echo e($value['type']); ?>"><span class="check"></span> <?php echo e($value['type_name']); ?></label>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                            <h4><?php echo e(trans('website.W0661')); ?> (<?php echo e(trans('website.W0547')); ?>)</h4>
                            <div class="price-range-slider">
                                <div class="price-range">
                                    <div class="leftLabel form-control">
                                        <input type="text" name="hourly_min_filter" data-action="filter">
                                    </div>
                                    <span>-</span>
                                    <div class="rightLabel form-control">
                                        <input type="text" name="hourly_max_filter" data-action="filter">
                                    </div>
                                </div>
                            </div>
                            <h4><?php echo e(trans('website.W0661')); ?> (<?php echo e(trans('website.W0673')); ?>)</h4>
                            <div class="price-range-slider">
                                <div class="price-range">
                                    <div class="leftLabel form-control">
                                        <input type="text" name="monthly_min_filter" data-action="filter">
                                    </div>
                                    <span>-</span>
                                    <div class="rightLabel form-control">
                                        <input type="text" name="monthly_max_filter" data-action="filter">
                                    </div>
                                </div>
                            </div>
                            <h4><?php echo e(trans('website.W0661')); ?> (<?php echo e(trans('website.W0674')); ?>)</h4>
                            <div class="price-range-slider">
                                <div class="price-range">
                                    <div class="leftLabel form-control">
                                        <input type="text" name="fixed_min_filter" data-action="filter">
                                    </div>
                                    <span>-</span>
                                    <div class="rightLabel form-control">
                                        <input type="text" name="fixed_max_filter" data-action="filter">
                                    </div>
                                </div>
                            </div>
                            <h4><?php echo e(trans('website.W0198')); ?></h4>
                            <div class="custom-dropdown industry-filter">
                                <select name="industry_filter[]" class="form-control" data-request="tags" multiple="true"  data-placeholder="<?php echo e(trans('website.W0644')); ?>">
                                    <?php echo ___dropdown_options(___cache('industries_name'),trans('website.W0198'),'',false); ?>

                                </select>
                                <div class="js-example-tags-container"></div>
                            </div>
                            <div class="clearfix"></div>
                            <h4 class="m-t-10px"><?php echo e(trans('website.W0206')); ?></h4>
                            <div class="skills-filter">
                                <div class="custom-dropdown">
                                    <select id="skills" name="skills_filter[]" class="filter form-control" data-request="tags" multiple="true" data-placeholder="<?php echo e(trans('website.W0798')); ?>">
                                        <?php echo ___dropdown_options(___cache('skills'),'','',false); ?>

                                    </select>
                                    <div class="js-example-tags-container"></div>
                                </div>
                            </div>
                            <h4><?php echo e(trans('website.W0280')); ?></h4>
                            <ul class="filter-list-group">
                                <?php $__currentLoopData = expertise_levels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li>
                                        <div class="checkbox">                
                                            <input type="checkbox" id="expertise-<?php echo e($value['level']); ?>" name="expertise_filter[]" value="<?php echo e($value['level']); ?>" data-action="filter">
                                            <label for="expertise-<?php echo e($value['level']); ?>"><span class="check"></span><?php echo e($value['level_name']); ?> <?php echo e($value['level_exp']); ?></label>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                            <h4><?php echo e(trans('website.W0201')); ?></h4>
                            <div class="filter-list-group">
                                <div class="custom-dropdown">
                                    <select class="form-control" multiple="true" name="city_filter[]" data-placeholder="<?php echo e(trans('general.M0195')); ?>"></select>
                                    <div class="js-example-tags-container"></div>
                                </div>
                            </div>
                            <?php if(0): ?>
                                <div class="location-filter">
                                    <input type="text" name="location_filter" placeholder="<?php echo e(trans('general.M0195')); ?>" class="form-control" data-request="city-filter" data-page="1" data-url="<?php echo e(url('ajax/city-list')); ?>" data-target=".city-filter-group" data-action="filter">
                                    <ul class="filter-list-group city-filter-group" data-request="custom-scrollbar"></ul>
                                </div>
                            <?php endif; ?>
                            <h4 class="m-t-10px"><?php echo e(trans('website.W0972')); ?></h4>
                            <div class="skills-filter">
                                <div class="custom-dropdown">
                                    <select id="jurisdiction" name="jurisdiction_filter[]" style="max-width: 400px;" class="filter form-control" data-request="tagssearch" multiple="true" data-placeholder="<?php echo e(trans('website.W0973')); ?>">
                                        <?php echo ___dropdown_options(___cache('countries'),'','',false); ?>

                                    </select>
                                    <div class="js-example-tags-container"></div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="col-md-9 right-sidebar">
                    <h2 class="form-heading">
                        <span class="filter-result-title p-b-15 hide"></span>
                        <div class="heading-filter small-select">
                            <div class="custom-dropdown sortby-filter">
                                <select name="sortby_filter" class="form-control">
                                    <?php echo ___dropdown_options(
                                            \App\Lib\Dash::combine(
                                                ___filter('talent_sorting_filter','all'),
                                                '{n}.filter_key',
                                                '{n}.filter_name'
                                            ),
                                            trans('general.M0193')
                                        ); ?>

                                </select>
                            </div>
                        </div>
                    </h2>
                    <div id="talent_listing" class="timeline timeline-inverse"></div>
                    <div class="pager text-center"><img src="<?php echo e(asset('images/loader.gif')); ?>"></div>
                    <div>
                       <div id="loadmore">
                           <button type="button" class="btn btn-default btn-block btn-lg" data-request="filter-paginate" data-url="<?php echo e(url(sprintf('%s/_find-talents',EMPLOYER_ROLE_TYPE))); ?>" data-target="#talent_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role='find-talents']"><?php echo e(trans('website.W0254')); ?></button>
                       </div>
                   </div>
                   <input type="hidden" name="page" value="1">
                </div>
            </div>
        </div>
    </div>
</form>

<?php $__env->startPush('inlinecss'); ?>
    <style type="text/css"> .job-profile-image {max-width: 60px; border-radius: 100%;} .find-job-left .contentbox-header-title{width:calc(100% - 70px); }.find-job-left{border:none;} </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('inlinescript'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/jquery.mCustomScrollbar.min.css')); ?>" media="all" type="text/css" />
    <script src="<?php echo e(asset('js/jquery.mCustomScrollbar.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('script/filter.js')); ?>" type="text/javascript"></script>
    <style type="text/css">
        .content-box .js-example-tags-container .tag-selected{
            padding: 4px 10px 4px 10px;
        }
        .price-range .form-control{
            padding-left: 28px;
        }
        .price-range .form-control::before{
            content: "<?php echo e(\Cache::get('currencies')[\Session::get('site_currency')]); ?>";
        }
    </style>
    <script type="text/javascript">
        /*$('.city-filter-group').mCustomScrollbar({
               callbacks:{
                   onTotalScroll:function(){
                       $('[data-request="city-filter"]').trigger('keyup');
                   }
               }
        }); */

        $(document).on('click','[name="company_profile_filter"]',function(){
            if($(this).val() == 'individual'){
                $('#company-profile').prop('checked',false);
            }else if($(this).val() == 'company'){
                $('#individual-profile').prop('checked',false);
            }
        });

        $("#search_talent").bind('keypress', function(e) {    
            var k = e.which;
            var ok = k >= 65 && k <= 90 || // A-Z
                k >= 97 && k <= 122 || // a-z
                k >= 48 && k <= 57 || //0-9 
                k == 46 //. 
            if (!ok){
                e.preventDefault();
            }
        });

        $("[name='hourly_min_filter'],[name='hourly_max_filter'],[name='monthly_min_filter'],[name='monthly_max_filter'],[name='fixed_min_filter'],[name='fixed_max_filter'] ").bind('keypress', function(e) {    
            var k = e.which;
            var ok = k >= 48 && k <= 57 || //0-9 
                     k == 46 //. 
            if (!ok){
                e.preventDefault();
            }
        });

        setTimeout(function(){
            $('[name="city_filter[]"]').select2({
                multiple: true,
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
                placeholder: function(){
                    $(this).find('option[value!=""]:first').html();
                }
            }).on('change', function() {
                var $selected = $(this).find('option:selected');
                var $container = $(this).siblings('.js-example-tags-container');

                var $list = $('<ul>');
                $selected.each(function(k, v) {
                    var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
                    $li.children('a.destroy-tag-selected')
                    .off('click.select2-copy')
                    .on('click.select2-copy', function(e) {
                        var $opt = $(this).data('select2-opt');
                        $opt.attr('selected', false);
                        $opt.parents('select').trigger('change');
                    }).data('select2-opt', $(v));
                    $list.append($li);
                });
                $container.html('').append($list);
            }).trigger('change');
        },2000);
    </script>
<?php $__env->stopPush(); ?>