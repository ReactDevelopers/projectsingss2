<!-- The Modal Start -->
<div class="modal" :id="`clientModal`">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
            <h4 class="modal-title">Add Client</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="row mt-2">
                    @php $countries = App\Country::all(); @endphp
                    @include('partials.invoices.common.create_client_overlay', ['countries' => $countries])
                </div>
            </div>
        </div>
    </div>
</div>
<!-- The Modal End -->