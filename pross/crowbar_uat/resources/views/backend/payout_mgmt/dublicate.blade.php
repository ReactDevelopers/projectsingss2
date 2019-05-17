@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/payout/management/duplicate/%s',ADMIN_FOLDER,$country_id)) }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">Select Desired Country</label>
                                <div>
                                    <select class="form-control" style="max-width: 400px;" name="country" placeholder="Country">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name">Selected Country: </label> <span>{{$country_name}}</span>
                            </div>
                        </div>

                        <div class="add-payout-table">
                            <div class="form-group">
                                <label for="name">Selected Country Configuration:-</label>
                            </div>
                            <table style="width:100%;">
                                <tr>
                                    <th>Profession:</th>
                                    <th>Registration Exists?:</th>
                                    <th>Accept Escrow:</th>
                                    <th>Pay Commission(in %):</th> 
                                    <th>Ask for Identification Number:</th>
                                </tr>
                                @foreach($payout_det as $key => $value)
                                <tr>
                                    <td>{{$value['industry_name']}}</td>
                                    <td>
                                        <div class="form-group">
                                            <div>
                                                <span>{{$payout_det[$key]['is_registered_show']}}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div>
                                                <span>Registered:</span>
                                                <span>{{$value['accept_escrow']}}</span>
                                            </div>
                                            <div>
                                                <span>Non Registered:</span>
                                                <span>{{$value['non_reg_accept_escrow']}}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div>
                                                <span>{{$value['pay_commision_percent']}}%</span>
                                            </div>
                                        </div>
                                    </td> 
                                    <td>
                                        <div class="form-group">
                                            <div>
                                                <span>{{$value['identification_number']}}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
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
<script type="text/javascript">
    var old_country_id = "{{$country_id}}";
    setTimeout(function(){
        $('[name="country"]').select2({
            formatLoadMore   : function() {return 'Loading more...'},
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
        }).on('change',function(){
            if($(this).val() == old_country_id){
                alert("You selected same country. Please select some other country.");
            }
        });
    },1000);
</script>
@endpush
@section('inlinecss')
    <style type="text/css">
        .select2-results__option.select2-results__option--load-more{
            display: none;    
        }
        .add-payout-table{
            padding:15px;
        }
    </style>
@endsection