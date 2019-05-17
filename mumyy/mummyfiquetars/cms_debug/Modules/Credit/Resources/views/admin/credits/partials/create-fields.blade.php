<div class="box-body">
    <div class="form-group">
      {!! Form::label("Vendor", trans('Vendor')) !!} <span class="text-danger">*</span>
        <select class="js-example-basic-single form-control vendor" id="vendor" name="vendor_id">
        @foreach($vendors as $vendor)
        	<?php  $vendorName = '';
        		   if(isset($vendor->vendorProfile->business_name) && !empty($vendor->vendorProfile->business_name))
        		   {
        		   		$vendorName = $vendor->vendorProfile->business_name;
        		   }
        	 ?>
		  <option value="{{ $vendor->id }}">{{ $vendorName }}</option>
		 @endforeach
		</select>
    </div>
    <div class='form-group{{ $errors->has("amount") ? ' has-error' : '' }}'>
	    {!! Form::label("amount", trans('Amount')) !!} <span class="text-danger">*</span>
	    {!! Form::text("amount", Input::old('amount'), ['class' => "form-control amount",'id' => "amount",'onkeypress' => "return isNumberKey(event)", 'placeholder' => trans('Amount')] ) !!}
	    {!! $errors->first("amount", '<span class="help-block">:message</span>') !!}
	</div>
     <div class='form-group{{ $errors->has("point") ? ' has-error' : '' }}'>
	    {!! Form::label("point", trans('Point')) !!} <span class="text-danger">*</span>
	    {!! Form::text("point", Input::old('point'), ['class' => "form-control point",'id' => "point",'readonly' => "readonly", 'placeholder' => trans('Point')] ) !!}
	    {!! $errors->first("point", '<span class="help-block">:message</span>') !!}
	</div>
</div>
