<div class="chat-loader">
	<div class="col-md-3">
        <div class="media-block show-loading-animation my-text-block" style="display: flex;background: #fff;padding-top: 0;">
            <div class="btn-block">
                <div style="padding-bottom: 20px;border-bottom: 1px solid #e1e1e1;">
                    <div class="text-block" style="width: 100%;">
                        <div class="text-row" style="height: 38px;border-radius: 3px;background-color: #e8e9ea;margin: 0px auto;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="media-block show-loading-animation my-text-block" style="display: flex;background: #eeeeee;padding-top: 10px;padding-bottom: 10px;">
            <div class="btn-block">
                <div class="col-xs-3">
                    <div class="round-shape" style="background-color: rgb(205, 205, 205);width: 55px;height: 55px;min-height: 55px;min-width: 55px;margin-right: 10px;border-radius: 100%;"></div>
                </div>
                <div class="col-xs-9" style="padding-top: 10px;padding-bottom: 15px;">
                    <div class="text-block" style="width: 100%;">
                        <div class="text-row" style="max-height: 5.88235%; width: 97%; height: 1em; background-color: rgb(205, 205, 205); margin-top: 0px;"></div>
                        <div class="text-row" style="max-height: 5.88235%; width: 100%; height: 1em; background-color: rgb(205, 205, 205); margin-top: 0.7em;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="media-block show-loading-animation my-text-block" style="display: flex;background: #eeeeee;padding-top: 10px;padding-bottom: 10px;">
            <div class="btn-block">
                <div class="col-xs-3">
                    <div class="round-shape" style="background-color: rgb(205, 205, 205);width: 55px;height: 55px;min-height: 55px;min-width: 55px;margin-right: 10px;border-radius: 100%;"></div>
                </div>
                <div class="col-xs-9" style="padding-top: 10px;padding-bottom: 15px;">
                    <div class="text-block" style="width: 100%;">
                        <div class="text-row" style="max-height: 5.88235%; width: 50%; height: 1em; background-color: rgb(205, 205, 205); margin-top: 0px;"></div>
                        <div class="text-row" style="max-height: 5.88235%; width: 75%; height: 1em; background-color: rgb(205, 205, 205); margin-top: 0.7em;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php if ($__env->exists('chat.chatbox')) echo $__env->make('chat.chatbox', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>