<update-notification-options :user="user" inline-template>
    <div class="card card-default">
        <div class="card-header">{{__('Notification Options')}}</div>

        <div class="card-body">
            <div class="alert alert-success" v-if="form.successful">
                {{__('Your notification option have been updated!')}}
            </div>

            <form role="form">
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">
                        {{__('Hotel Payment Schedule Emails')}}
                    </label>

                    <div class="col-md-1">
                        <input type="checkbox" class="form-control"
                            name="notify_of_hotel_payment_schedule"
                            v-model="form.notify_of_hotel_payment_schedule"
                            :class="{'is-invalid': form.errors.has('notify_of_hotel_payment_schedule')}">

                        <span class="invalid-feedback"
                            v-show="form.errors.has('notify_of_hotel_payment_schedule')">
                            @{{ form.errors.get('notify_of_hotel_payment_schedule') }}
                        </span>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary"
                                @click.prevent="update"
                                :disabled="form.busy">

                            {{__('Update')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</update-notification-options>
