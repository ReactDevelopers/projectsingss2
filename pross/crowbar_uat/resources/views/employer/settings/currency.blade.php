<div class="col-md-8 col-sm-8 col-xs-12 no-padding-xs">
    <div class="login-inner-wrapper setting-wrapper">
        <p class="p-b-15">{{trans('website.W0675')}}</p>
        <table class="table currency-rate" border="1" cellpadding="2" cellspacing="0" style="border: 1px solid #dddddd;">
			<thead>
				<tr>
					<th>Currency</th>
					<th>Rate</th>
				</tr>
			</thead>
			<tbody>
				@php 
					array_walk($currency, function($item){
						echo sprintf("<tr><td>{$item['iso_code']}</td><td>".str_replace('$', $item['sign'].' ', ___formatdoller($item['conversion_rate'],true,true))."</td></tr>");
					});
				@endphp
			</tbody>
		</table>
		{{trans('website.W0746')}} {{___ago($currency[0]['updated'])}}
	</div>
</div>