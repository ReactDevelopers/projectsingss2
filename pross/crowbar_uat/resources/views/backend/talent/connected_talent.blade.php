<div class="panel-body">
    {{-- <label for="name">Industry Affiliations</label><br /> --}}
    <div class="company-name-wrapper">
        @if(!empty($connected_user))
            @foreach($connected_user as $k => $v)
            <div class="info-row row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <label class="company-label">{{$v['user']['name']}}</label>
                    </div>
            </div>
            @endforeach
        @endif
    </div>
</div>
@push('inlinescript')
<script type="text/javascript">

</script>
@endpush
