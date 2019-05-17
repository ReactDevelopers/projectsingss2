@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/coupon/add',ADMIN_FOLDER)) }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">Coupon Code Prefix</label>
                                <input type="text" class="form-control" name="code_prefix" placeholder="Coupon Code Prefix" value="{{ old('code_prefix') }}">
                            </div>

                            <div class="form-group">
                                <label for="name">Discount(in %)</label>
                                <input type="text" class="form-control" name="discount" placeholder="Discount" value="{{ old('discount') }}">
                            </div>

                            <div class="form-group">
                                <div class="datebox-no startdate">
                                    <label class="control-label">Start Date</label>  
                                    <div class='input-group datepicker'>
                                        <input type='text' id="from" name="startdate" class="form-control" placeholder="" maxlength="10" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="datebox-no startdate">
                                    <label class="control-label">Expiration Date</label>  
                                    <div class='input-group datepicker'>
                                        <input type='text' id="to" name="enddate" class="form-control" placeholder="" maxlength="10" />
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="panel-footer">
                            <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('inlinescript')
<link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
<script src="{{ asset('js/jquery-ui.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    function getDate(element) {
        var date;
        try {
            date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
            date = null;
        }

        return date;
    }

    var dateFormat = "dd/mm/yy";

    var from = $("#from").datepicker({
        changeMonth: true,
        changeYear: true,
        minDate: new Date(),
        numberOfMonths: 1,
        dateFormat: dateFormat
    }).on("change", function() {        
        var maxDate = new Date(getDate( this ));
        maxDate.setDate(maxDate.getDate() + 1);            
        to.datepicker( "option", "minDate", maxDate );
    });

    var to = $("#to").datepicker({
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        dateFormat: dateFormat
    }).on("change", function(e){
        from.datepicker( "option", "maxDate", getDate( this ) );
    });
</script>
@endpush
