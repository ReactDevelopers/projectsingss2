@extends('layouts.backend.dashboard')
@section('content')
<div class="mailbox-read-message">
    <div class="form-group">
        <b>Coupon Code</b><br>
        <span>{{$coupon_code->code}}</span> 
    </div>
</div>
<div class="mailbox-read-message">
    <div class="form-group">
        <b>Discount(in %)</b><br>
        @if(!empty($coupon_code->discount))
            <span>{{number_format($coupon_code->discount,2)}}</span>
        @else
            <span>{{number_format(0,2)}}</span>
        @endif 
    </div>
</div>
<div class="mailbox-read-message">
    <div class="form-group">
        <b>Start Date</b><br>
        <span>{{___d($coupon_code->start_date)}}</span> 
    </div>
</div>
<div class="mailbox-read-message">
    <div class="form-group">
        <b>Expiration</b><br>
        <span>{{___d($coupon_code->expiration_date)}}</span> 
    </div>
</div>
<div class="mailbox-read-message">
    <div class="form-group">
        <b>Status</b><br>
        <span>{{ucfirst($coupon_code->status)}}</span> 
    </div>
</div>
<div class="mailbox-read-message">
    <div class="form-group">
        <b>Created Date</b><br>
        <span>{{___d($coupon_code->created)}}</span> 
    </div>
</div>
<div class="mailbox-read-message">
	<div class="form-group">
		<span>
			<a href="{{url(sprintf('%s/coupon/list',ADMIN_FOLDER))}}">Back</a>
		</span>
	</div>
</div>
@endsection