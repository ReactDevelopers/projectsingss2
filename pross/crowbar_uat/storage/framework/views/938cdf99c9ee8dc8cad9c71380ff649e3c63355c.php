<form method="post" role="find-jobs" accept-charset="utf-8" class="form-horizontal" autocomplete="off">
    <div class="contentWrapper job-listing-section">
        <div class="container">
            <div class="row mainContentWrapper">
                <a href="javascript:void(0);" class="sidebar-menu"><span></span> Filter</a>
                <div class="col-md-3 left-sidebar bottom-margin-10px" id="left-sidebar">
                    <div class="sidebar-content">
                        <h3>Filters <span data-request="clear-filter" data-url="<?php echo e(url('/search-job')); ?>" id="clearAll">Clear All</span></h3>
                        <div class="filter-options">
                            <h4><?php echo e(trans('website.W0342')); ?></h4>
                            <div class="filter-list-group">
                                <input type="text" value="<?php echo e(\Request::get('_search')); ?>" name="search" placeholder="Search" class="form-control" data-request="search"/>
                            </div>
                            <h4>Employment Type</h4>
                            <ul class="filter-list-group">
                                <?php $__currentLoopData = employment_types('web_post_job'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li>
                                        <div class="checkbox ">                
                                            <input type="checkbox" id="employement-<?php echo e($value['type']); ?>" name="employment_type_filter[]" value="<?php echo e($value['type']); ?>"  data-action="filter" <?php if($value['type'] && 0): ?> checked="checked" <?php endif; ?>>
                                            <label for="employement-<?php echo e($value['type']); ?>"><span class="check"></span> <?php echo e($value['type_name']); ?></label>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                            <h4><?php echo e(trans('website.W0661')); ?></h4>
                            <div class="price-range-slider">
                                <div class="price-range">
                                    <div class="leftLabel form-control">
                                        <input type="text" name="price_min_filter" data-action="filter">
                                    </div>
                                    <span>-</span>
                                    <div class="rightLabel form-control">
                                        <input type="text" name="price_max_filter" data-action="filter">
                                    </div>
                                </div>
                            </div>
                            <h4><?php echo e(trans('website.W0198')); ?></h4>
                            <div class="custom-dropdown industry-filter">
                                <select name="industry_filter[]" class="form-control" data-request="tags" multiple="true"  data-placeholder="<?php echo e(trans('website.W0644')); ?>">
                                    <?php echo ___dropdown_options(___cache('industries_name'),trans('website.W0198'),request()->get('industry'),false); ?>

                                </select>
                                <div class="js-example-tags-container"></div>
                            </div>
                            <h4><?php echo e(trans('general.M0510')); ?></h4>
                            <div class="timeline-filter">
                                <div class="datebox startdate">
                                    <div class='input-group datepicker'>
                                        <input type='text' id='from' name="startdate_filter" class="form-control" placeholder="<?php echo e(trans('website.W0657')); ?>" data-action="filter" />
                                    </div>
                                </div>
                                <span>-</span>
                                <div class="datebox enddate">
                                    <div class='input-group datepicker'>
                                        <input type='text' id='to' name="enddate_filter" class="form-control" placeholder="<?php echo e(trans('website.W0657')); ?>" data-action="filter"/>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <h4 class="m-t-10px"><?php echo e(trans('website.W0206')); ?></h4>
                            <div class="skills-filter">
                                <div class="custom-dropdown">
                                    <select id="skills" name="skills_filter[]" class="filter form-control" data-request="tags" multiple="true" data-placeholder="<?php echo e(trans('website.W0798')); ?>">
                                        <?php echo ___dropdown_options(___cache('skills_filter'),'','',false); ?>

                                    </select>
                                    <div class="js-example-tags-container"></div>
                                </div>
                            </div>
                            <h4><?php echo e(trans('website.W0280')); ?></h4>
                            <ul class="filter-list-group">
                                <?php $__currentLoopData = expertise_levels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $value): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                                    <li>
                                        <div class="checkbox ">                
                                            <input type="checkbox" id="expertise-<?php echo e($value['level']); ?>" name="expertise_filter[]" value="<?php echo e($value['level']); ?>" data-action="filter">
                                            <label for="expertise-<?php echo e($value['level']); ?>"><span class="check"></span><?php echo e($value['level_name']); ?></label>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                            </ul>
                            <?php if(0): ?>
                                <h4>Location</h4>
                                <div class="location-filter">
                                    <input type="text" name="location_filter" placeholder="<?php echo e(trans('general.M0195')); ?>" class="form-control" data-request="city-filter" data-url="<?php echo e(url('ajax/city-list')); ?>" data-target=".city-filter-group" data-action="filter">
                                    <ul class="filter-list-group city-filter-group" data-request="custom-scrollbar"></ul>
                                </div>                                
                            <?php endif; ?>
                        </div>
                    </div> 
                </div>
                <div class="col-md-9 right-sidebar">
                    <h2 class="form-heading">
                        <span class="filter-result-title p-b-15 hide">&nbsp;</span>
                        <div class="heading-filter small-select">
                            
                            <div class="custom-dropdown sortby-filter">
                                <select name="sortby_filter" class="form-control">
                                    <?php echo ___dropdown_options(
                                            \App\Lib\Dash::combine(
                                                ___filter('job_sorting_filter','all'),
                                                '{n}.filter_key',
                                                '{n}.filter_name'
                                            ),
                                            trans('general.M0193')
                                        ); ?>

                                </select>
                            </div>
                        </div>
                    </h2>
                    <div id="job_listing" class="timeline timeline-inverse"></div>
                    <div class="pager text-center"><img src="<?php echo e(asset('images/loader.gif')); ?>"></div>
                    <div>
                       <div id="loadmore">
                           <button type="button" class="btn btn-default btn-block btn-lg hide" data-request="filter-paginate" data-url="<?php echo e(url('_search-job')); ?>" data-target="#job_listing" data-showing="#paginate_showing" data-loadmore="#loadmore" data-form="[role='find-jobs']"><?php echo e(trans('website.W0254')); ?></button>
                       </div>
                   </div>
                   <input type="hidden" name="page" value="1">
                </div>                
            </div>            
        </div>
    </div>
</form>

<?php $__env->startPush('inlinescript'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/jquery.mCustomScrollbar.min.css')); ?>" media="all" type="text/css" />
    <script src="<?php echo e(asset('js/jquery.mCustomScrollbar.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('script/filter.js')); ?>" type="text/javascript"></script>

    <script>
        $( function() {
            var dateFormat = "dd/mm/yy";
            var from = $("#from").datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: new Date(),
                numberOfMonths: 1,
                dateFormat: dateFormat
            }).on("change", function() {
                to.datepicker( "option", "minDate", getDate( this ) );
            });

            var to = $( "#to" ).datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                dateFormat: dateFormat
            }).on("change", function(e){
                from.datepicker( "option", "maxDate", getDate( this ) );
            });

            function getDate( element ) {
                var date;
                try {
                    date = $.datepicker.parseDate( dateFormat, element.value );
                } catch( error ) {
                    date = null;
                }

                return date;
            }
        });
    </script>
    <style type="text/css">
        .price-range .form-control{
            padding-left: 28px;
        }
        .price-range .form-control::before{
            content: "<?php echo e(___cache('currencies')[\Session::get('site_currency')]); ?>";
        }
    </style>
<?php $__env->stopPush(); ?>
