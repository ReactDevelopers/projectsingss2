<clients-create-custom  v-on:client="setClient" :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">

            <div class="col-md-12">
                <form enctype="multipart/form-data" method="POST" role="form" action="{{ route('clients.store') }}">

                {{ csrf_field() }}

                <div class="col-md-12">
                    <div role="alert" class="alert alert-success" >
                        The client has been saved.
                    </div>
                </div>

                <div class="card card-default">
                    <div class="card-body">

                        <div class="form-row">
                            @include('partials.clients.form')
                        </div>

                        @include('partials.contacts')

                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-6 text-right">
                                <!-- Modal footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success" dusk="client-submit" @click.prevent="create()" :disabled="form.busy" >Save</button>
                                    <button type="button" class="btn" data-dismiss="modal" ref="triggerCancel" >Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</clients-create-custom>
