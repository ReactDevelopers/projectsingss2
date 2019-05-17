<?php if(!empty($banner['how-it-works']->count())): ?>
    <div class="how-it-works" style="background-image: url('<?php echo e(asset('uploads/banner/'.$banner['how-it-works'][0]->banner_image)); ?>')">
        <div class="container-fluid">
            <div class="works-as">
                <h5>Find out how it works</h5>
                <ul class="">
                    <li>
                        <a href="<?php echo e(url('/page/how-it-works?section=get-hired')); ?>"><span>get hired</span></a>
                    </li>
                    <li>
                        <a href="<?php echo e(url('/page/how-it-works?section=hire-talent')); ?>"><span>hire talent</span></a>
                    </li>
                    <li>
                        <a href="<?php echo e(url('/page/faq')); ?>"><span>FAQs</span></a>
                    </li>
                </ul>            
            </div>
        </div>
    </div>
<?php endif; ?>