<form class="form-horizontal" role="employer_step_two" action="<?php echo e(url(sprintf('%s/hire/talent/process/four',EMPLOYER_ROLE_TYPE))); ?>" method="post" accept-charset="utf-8">
	<div class="login-inner-wrapper">
		<?php echo e(csrf_field()); ?>

		<div class="row">
			<div class="messages"></div>
		</div>
		<h4 class="form-sub-heading"><?php echo e(sprintf(trans('website.W0656'),'')); ?></h4>
		
		<div class="row">	
			<div class="col-md-4">
				<div class="col-md-12">						
					<div class="form-group">
						<div class="datebox-no startdate">
							<label class="control-label"><?php echo e(trans('website.W0277')); ?></label>  
							<div class='input-group datepicker'>
								<input type='text' id="from" name="startdate" class="form-control" placeholder="<?php echo e(trans('website.W0657')); ?>" value="<?php echo e(___convert_date($project['startdate'],'JS','d/m/Y')); ?>" maxlength="10" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="col-md-12">
					<div class="form-group">
						<div class="datebox-no enddate">
							<label class="control-label"><?php echo e(trans('website.W0278')); ?></label>  
							<div class='input-group datepicker'>
								<input type='text' <?php if($project['employment'] !== 'monthly'): ?> id="to" <?php endif; ?> name="enddate" class="form-control <?php if($project['employment'] == 'monthly'): ?> hasDatepicker focus-change <?php endif; ?>" placeholder="<?php echo e(trans('website.W0657')); ?>" value="<?php echo e(___convert_date($project['enddate'],'JS','d/m/Y')); ?>" maxlength="10" <?php if($project['employment'] == 'monthly'): ?> readonly <?php endif; ?>/>
							</div>
						</div>
					</div>
				</div>						
			</div>
			<?php if($project['employment'] == 'hourly'): ?>
				<div class="col-md-4">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label"><?php echo trans('website.W0793'); ?></label> 
							<input type="text" name="expected_hour" value="<?php echo e(substr($project['expected_hour'], 0, -3)); ?>" autocomplete="off" class="form-control hasTimepicker" placeholder="<?php echo e(trans('website.W0701')); ?>" maxlength="5"/>
						</div>							
					</div>
				</div>
			<?php elseif($project['employment'] == 'monthly'): ?>
				<div class="col-md-4">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label"><?php echo trans('website.W0841'); ?></label> 
							<input type="number" autocomplete="off" class="form-control total_months" placeholder="<?php echo e(trans('website.W0840')); ?>" max="12" min="1"/>
						</div>							
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="login-inner-wrapper">
		<h4 class="form-sub-heading"><?php echo e(trans('website.W0794')); ?></h4>
		<div class="form-group">
			<div class="row">	
				<div class="col-md-10 m-b-10px">	
					<div class="col-md-3"><?php echo e(trans('website.W0795')); ?></div>	
					<div class="col-md-9 amount-tag"><span id="sub_total"><?php echo e(\Cache::get('currencies')[$user['currency']]); ?>0.00</span>&nbsp;<?php if($project['employment'] == 'hourly'): ?><span id="total_hour">00:00 <?php echo e(trans('website.W0759')); ?></span><?php endif; ?><span> @ </span> <?php echo e(\Cache::get('currencies')[$user['currency']]); ?><?php echo e(___format($project['price'],false,false)); ?>&nbsp;<?php echo e(trans('website.rate_'.$project['employment'])); ?></div>
				</div>
				<div class="col-md-10 m-b-10px">	
					<div class="col-md-3"><?php echo e(trans('website.W0796')); ?></div>	
					<div class="col-md-9 amount-tag"><span id="transaction_fee"><?php echo e(\Cache::get('currencies')[$user['currency']]); ?>0.00</span></div>
				</div>
				<div class="col-md-10 m-b-10px">	
					<div class="col-md-3"><?php echo e(trans('website.W0797')); ?></div>	
					<div class="col-md-9 amount-tag"><span id="total_amount"><?php echo e(\Cache::get('currencies')[$user['currency']]); ?>0.00</span></div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group button-group">
		<input type="hidden" name="id_project" value="<?php echo e($project['id_project']); ?>">
		<input type="hidden" name="employment" value="<?php echo e($project['employment']); ?>">
		<input type="text" class="hide" name="talent_id" value="<?php echo e($talent_id); ?>">
		<input type="text" class="hide" name="action" value="<?php echo e($action); ?>">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="row form-btn-set">
				<div class="col-md-7 col-sm-7 col-xs-6">
					<?php if(in_array('two',$steps)): ?>
                        <a href="<?php echo e(url(sprintf("%s/hire/talent/{$action_url}%s{$project_id_postfix}",EMPLOYER_ROLE_TYPE,$steps[count($steps)-2]))); ?>" class="greybutton-line"><?php echo e(trans('website.W0196')); ?></a>
                    <?php endif; ?>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-6">
					<button type="button" class="button" data-request="ajax-submit" data-target='[role="employer_step_two"]'>
						<?php echo e(trans('website.W0659')); ?>

					</button>
				</div>
			</div>
		</div>
	</div>
</form>

<?php $__env->startPush('inlinescript'); ?>
	<script type="text/javascript" src="<?php echo e(asset('js/bootstrap-timepicker.min.js')); ?>"></script>
    <script type="text/javascript">
        $(function () {
        	function getdate($date,$days) {
        		$given = $date.split('/');
			    var date = new Date(new Date($given[1]+'/'+$given[0]+'/'+$given[2]));
			    date.setDate(date.getDate() + (parseInt($days)));

			    var dd = (date.getDate() < 10)?'0'+(date.getDate()):(date.getDate());
			    var mm = ((date.getMonth() + 1) < 10)?'0'+((date.getMonth() + 1)):((date.getMonth() + 1));
			    var yy = (date.getFullYear() < 10)?'0'+(date.getFullYear()):(date.getFullYear());

			    return  dd +'/' +  mm + '/' + yy;
			}

        	$(document).on('click','.focus-change',function(){
        		$('.total_months').trigger('focus');
        	});
        	
        	$(document).on('change keyup','.total_months',function(){
        		if(!parseInt($(this).val()) && $(this).val().length > 0){
        			$(this).val(1);
        		}else if(parseInt($(this).val()) > 12){
        			$(this).val(12);
        		}else if(parseInt($(this).val()) < 1){
        			$(this).val(1);
        		}

        		$(".has-error").removeClass('has-error');$('.help-block').remove();
				if(!$("#from").val()){
					$("#from").closest('.form-group').addClass('has-error');
					$("#from").parent().after('<div class="help-block"><?php echo e(trans('general.M0146')); ?></div>');
				}else if(!$(this).val()){
					$(this).closest('.form-group').addClass('has-error');
					$(this).after('<div class="help-block"><?php echo e(trans('general.M0594')); ?></div>');
				}else{
					$('[name="enddate"]').val(getdate($("#from").val(),(parseInt($(this).val())*<?php echo e(MONTH_DAYS); ?>)-1));
					$("#from").trigger('change');
				}
			});

        	$(document).on('change','#from',function(){
				if(parseInt($('.total_months').val())){
					$('[name="enddate"]').val(getdate($("#from").val(),(parseInt($('.total_months').val())*<?php echo e(MONTH_DAYS); ?>)-1));
				}	

        		$(".has-error").removeClass('has-error');$('.help-block').remove();
				if(!$(this).val() && $('.total_months').val()){
					$(this).closest('.form-group').addClass('has-error');
					$(this).parent().after('<div class="help-block"><?php echo e(trans('general.M0146')); ?></div>');
				}else if(!$('[name="enddate"]').val() && $(this).val()){
					$('.total_months').trigger('keyup');
				}

				$(this).trigger('keyup');
				$(this).trigger('blur');
			});

            $("[name=\"expected_hour\"]").timepicker({
                template: false,
                showMeridian: false,
                defaultTime: "00:00"
            }).on("change keyup",function(){
            	var $to 					= $('[name="enddate"]').val();
				var $from 					= $('[name="startdate"]').val();
				var $price 					= "<?php echo e($project['price']); ?>";
				var $employment 			= "<?php echo e($project['employment']); ?>";
				var $expected_hours 		= time_to_decimal($("[name=\"expected_hour\"]").val());
				var $number_of_days 		= date_difference($from,$to);
				var $paypal_commission      = "<?php echo e(\Cache::get('configuration')['paypal_commission']); ?>";
				var $paypal_commission_flat = "<?php echo e(\Cache::get('configuration')['paypal_commission_flat']); ?>";

				var $amount 				= calculate_price($to,$from,$price,$employment,$expected_hours,$number_of_days,$paypal_commission,$paypal_commission_flat);

				$('#sub_total').html('<?php echo e(\Cache::get('currencies')[$user['currency']]); ?>'+$amount.sub_total);
				$('#transaction_fee').html('<?php echo e(\Cache::get('currencies')[$user['currency']]); ?>'+$amount.transaction_fee);
				$('#total_amount').html('<?php echo e(\Cache::get('currencies')[$user['currency']]); ?>'+$amount.total_amount);
				$('#total_hour').html($('[name="expected_hour"]').val()+' <?php echo e(trans('website.W0759')); ?>');
			});

		    $("#from").on("change keyup",function(){
            	var $to 					= $('[name="enddate"]').val();
				var $from 					= $('[name="startdate"]').val();
				var $price 					= "<?php echo e($project['price']); ?>";
				var $employment 			= "<?php echo e($project['employment']); ?>";
				var $expected_hours 		= time_to_decimal($("[name=\"expected_hour\"]").val());
				var $number_of_days 		= date_difference($from,$to);
				var $paypal_commission      = "<?php echo e(\Cache::get('configuration')['paypal_commission']); ?>";
				var $paypal_commission_flat = "<?php echo e(\Cache::get('configuration')['paypal_commission_flat']); ?>";

				
				var $amount 				= calculate_price($to,$from,$price,$employment,$expected_hours,$number_of_days,$paypal_commission,$paypal_commission_flat);

				$('#sub_total').html('<?php echo e(\Cache::get('currencies')[$user['currency']]); ?>'+$amount.sub_total);
				$('#transaction_fee').html('<?php echo e(\Cache::get('currencies')[$user['currency']]); ?>'+$amount.transaction_fee);
				$('#total_amount').html('<?php echo e(\Cache::get('currencies')[$user['currency']]); ?>'+$amount.total_amount);
				$('#total_hour').html($('[name="expected_hour"]').val()+' <?php echo e(trans('website.W0759')); ?>');
		    });

		    $("#to").on("change keyup",function(){
            	var $to 					= $('[name="enddate"]').val();
				var $from 					= $('[name="startdate"]').val();
				var $price 					= "<?php echo e($project['price']); ?>";
				var $employment 			= "<?php echo e($project['employment']); ?>";
				var $expected_hours 		= time_to_decimal($("[name=\"expected_hour\"]").val());
				var $number_of_days 		= date_difference($from,$to);
				var $paypal_commission      = "<?php echo e(\Cache::get('configuration')['paypal_commission']); ?>";
				var $paypal_commission_flat = "<?php echo e(\Cache::get('configuration')['paypal_commission_flat']); ?>";

				
				var $amount 				= calculate_price($to,$from,$price,$employment,$expected_hours,$number_of_days,$paypal_commission,$paypal_commission_flat);

				$('#sub_total').html('<?php echo e(\Cache::get('currencies')[$user['currency']]); ?>'+$amount.sub_total);
				$('#transaction_fee').html('<?php echo e(\Cache::get('currencies')[$user['currency']]); ?>'+$amount.transaction_fee);
				$('#total_amount').html('<?php echo e(\Cache::get('currencies')[$user['currency']]); ?>'+$amount.total_amount);
				$('#total_hour').html($('[name="expected_hour"]').val()+' <?php echo e(trans('website.W0759')); ?>');
		    });
        });

        $( function() {
        	$("[name=\"startdate\"]").trigger('change');
			var dateFormat = "dd/mm/yy";

			<?php if($project['employment'] == 'monthly'): ?>
				var from = $("#from").datepicker({
					changeMonth: true,
					changeYear: true,
					minDate: new Date(),
					numberOfMonths: 1,
					dateFormat: dateFormat
				}).on("change", function() {
					to.datepicker( "option", "minDate", firstOfNextMonth( this ));
				});
			<?php else: ?>
				var from = $("#from").datepicker({
					changeMonth: true,
					changeYear: true,
					minDate: new Date(),
					numberOfMonths: 1,
					dateFormat: dateFormat
				}).on("change", function() {
					to.datepicker( "option", "minDate", getDate( this ) );
				});
			<?php endif; ?>
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

			function firstOfNextMonth() {
	            var d = new Date();
	            d.setMonth(d.getMonth()+1, 1);
	            return d;
	        }
		});

		function date_difference($startdate, $enddate){

			if($startdate && $enddate){
				var $date = $startdate.split("/");
				$startdate = $date[1]+'/'+$date[0]+'/'+$date[2];

				var $date = $enddate.split("/");
				$enddate = $date[1]+'/'+$date[0]+'/'+$date[2];

				var $startdate 	= new Date($startdate);
				var $enddate 	= new Date($enddate);
				var $timeDiff 	= Math.abs($enddate.getTime() - $startdate.getTime());
				var $diffDays 	= (Math.ceil($timeDiff / (1000 * 3600 * 24)))+1; 
			}else{
				var $diffDays 	= 0;				
			}

			return $diffDays;
		}

		function calculate_price($to,$from,$price,$employment,$expected_hours,$number_of_days,$paypal_commission,$paypal_commission_flat){
			if($employment == 'hourly'){
				$sub_total  = $price*$expected_hours*1/*parseInt($number_of_days)*/;
			}else if($employment == 'monthly'){
				$sub_total  = ($price/parseInt(<?php echo e(MONTH_DAYS); ?>))*(parseInt($number_of_days));
			}else if($employment == 'fixed'){
				$sub_total  = $price;
			}else{
				$sub_total  = 0;
			}

			$commission                 = 0;
			if($sub_total){
	        	$transaction_fee       		= ((parseFloat(($sub_total*$paypal_commission)/100)+parseFloat($paypal_commission_flat)));

            	return {
            		'sub_total'			: parseFloat($sub_total).toFixed(2),
            		'transaction_fee'	: parseFloat($transaction_fee).toFixed(2),
            		'total_amount'		: (parseFloat($sub_total)+parseFloat($transaction_fee)).toFixed(2)
        		}
			}else{
				return {
            		'sub_total'			: parseFloat(0).toFixed(2),
            		'transaction_fee'	: parseFloat(0).toFixed(2),
            		'total_amount'		: parseFloat(0).toFixed(2)
        		}
			}
		}

		function time_to_decimal($time) {
			if($time){
			  	var $hoursMinutes = $time.split(/[.:]/);
			  	var $hours = parseInt($hoursMinutes[0], 10);
			  	var $minutes = $hoursMinutes[1] ? parseInt($hoursMinutes[1], 10) : 0;
			  	
			  	return $hours + $minutes / 60;
			}else{
				return 0;
			}
		}
    </script>
    <style type="text/css">
        .price-range .form-control{
            padding-left: 28px;
        }
        .price-range .form-control::before{
            content: "<?php echo e(\Cache::get('currencies')[\Session::get('site_currency')]); ?>";
        }
    </style>
<?php $__env->stopPush(); ?>
