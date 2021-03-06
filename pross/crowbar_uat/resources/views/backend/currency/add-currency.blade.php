@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/currency/add',ADMIN_FOLDER)) }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">Currency ISO</label>
                                <input type="text" class="form-control" name="iso_code" placeholder="Currency ISO" value="{{ old('iso_code') }}">
                            </div>
                            <div class="form-group">
                                <label for="name">Currency Symbol</label>
                                <input type="text" class="form-control" name="sign" placeholder="Currency Symbol" value="{{ old('sign') }}">
                            </div>
                            <div class="form-group">
                                <label for="name">Country</label>
                                <select class="form-control" name="id_country">
                                    <option value="">Select Country</option>
                                    @foreach($country as $c)
                                    <option value="{{$c['id_country']}}">{{$c['country_name']}}</option>
                                    @endforeach
                                </select>
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
<script type="text/javascript">

</script>
@endpush
