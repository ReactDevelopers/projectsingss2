<div class="row mb-3">
    <div class="col-md-3 mt-3">
        <label><strong>{{__('Hotel Name')}}</strong></label>
        <input type="text" placeholder="{{__('Hotel Name')}}" dusk="name"
            class="form-control"
            v-model="form.name"
            :class="{'is-invalid': form.errors.has(`name`), 'form-control': true}" >
        <span class="invalid-feedback"
            v-if="form.errors.has(`name`)">
            @{{ form.errors.get(`name`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "Four Seasons"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>{{__('Address')}}</strong></label>
        <input type="text" placeholder="{{__('Address')}}"
            class="form-control" dusk="address"
            v-model="form.address"
            :class="{'is-invalid': form.errors.has(`address`), 'form-control': true}">
        <span class="invalid-feedback" v-if="form.errors.has(`address`)">
            @{{ form.errors.get(`address`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "98 San Jacinto Blvd"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>{{__('City')}}</strong></label>
        <input type="text" placeholder="{{__('City')}}"
            class="form-control" dusk="city"
            v-model="form.city"
            :class="{'is-invalid': form.errors.has(`city`), 'form-control': true}">
        <span class="invalid-feedback" v-if="form.errors.has(`city`)">
            @{{ form.errors.get(`city`) }}
        </span>
            <small class="form-text text-muted">
                Ex: "Austin"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>{{__('State/Region')}}</strong></label>
        <input type="text" placeholder="{{__('State/Region')}}"
            class="form-control" dusk="region"
            v-model="form.region"
            :class="{'is-invalid': form.errors.has(`region`), 'form-control': true}">
        <span class="invalid-feedback" v-if="form.errors.has(`region`)">
            @{{ form.errors.get(`region`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "TX"
        </small>
    </div>
    <div class="col-md-3 mt-3">

        <label><strong>{{__('Country')}}</strong></label>

        <select class="form-control" name="country_id" dusk="country_id" v-model="form.country_id">
            @foreach ($countries as $country)
                <option value="{{ $country->id }}">{{ $country->name }}</option>
            @endforeach
        </select>
        <span class="invalid-feedback" v-if="form.errors.has(`country_id`)">
            @{{ form.errors.get(`country_id`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "United States"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>{{__('Postal Code')}}</strong></label>
        <input type="text" placeholder="{{__('Postal Code')}}"
            class="form-control" dusk="postal_code"
            v-model="form.postal_code"
            :class="{'is-invalid': form.errors.has(`postal_code`), 'form-control': true}">
        <span class="invalid-feedback" v-if="form.errors.has(`postal_code`)">
            @{{ form.errors.get(`postal_code`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "78704"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>Email</strong></label>

        <input type="text" placeholder="{{__('Property Email')}}"
        class="form-control"
        v-model="form.email" dusk="email"
        :class="{'is-invalid': form.errors.has(`email`), 'form-control': true}">

        <span class="invalid-feedback">
            {{ $errors->first('email') }}
        </span>
        <span class="invalid-feedback" v-if="form.errors.has(`email`)">
            @{{ form.errors.get(`email`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "someone@fourseasons.com"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>Phone</strong></label>

        <input type="text" placeholder="{{__('Property Phone')}}"
            class="form-control"
            v-model="form.phone" dusk="phone"
            :class="{'is-invalid': form.errors.has(`phone`), 'form-control': true}"
        >
        <span class="invalid-feedback" v-if="form.errors.has(`phone`)">
            @{{ form.errors.get(`phone`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "+1 (800) 555-1212"
        </small>
    </div>

    <div class="col-md-3 mt-3">
        <label><strong>{{__('Website')}}</strong></label>
        <input type="text" placeholder="{{__('Website')}}"
            class="form-control" dusk="website"
            v-model="form.website"
            :class="{'is-invalid': form.errors.has(`website`), 'form-control': true}">
        <span class="invalid-feedback" v-if="form.errors.has(`website`)">
            @{{ form.errors.get(`website`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "https://www.fourseasons.com"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>{{__('Supplier Code')}}</strong></label>
        <input type="text" placeholder="{{__('Supplier Code')}}"
            class="form-control"
            v-model="form.code"
            :class="{'is-invalid': form.errors.has(`code`), 'form-control': true}">
        <span class="invalid-feedback" v-if="form.errors.has(`code`)">
            @{{ form.errors.get(`code`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "RIVA51"
            {{--
            NOTES From Simon re: Supplier Code:
            * We use the first 4 characters of the supplier name
                but ignore words like “The” or “Hotel” otherwise
                we’d have hundreds of suppliers codes like HOTE**. 
            * We don’t have 50 other codes beginning RIVA but instead
                have 2 sequences for supplier codes to distinguish
                between FOWT suppliers (starting at 51) and the
                wider commercial rights (starting at 01). 
            * They’re therefore always 6 characters in length and
                normally 4 letters followed by 2 numbers.  There
                can be exceptions to that rule, hence a hotel
                called “K2 Hotel” would have to be K2HO51 so
                I wouldn’t make that a rule.
             --}}
        </small>
    </div>
</div>
<hr class="my-5">

<div class="form-row">
    <div class="col-sm-6">
        <h5>Notes on Hotel</h5>
    </div>
</div>
<div class="row mt-3">
    <div class="col-sm-12">

        <textarea
            rows="5"
            placeholder="Add hotel notes here"
            v-model="form.notes"
            :class="{'is-invalid': form.errors.has(`notes`), 'form-control': true}"
            name="notes" dusk="note"></textarea>
        <span class="invalid-feedback" v-if="form.errors.has(`notes`)">
            @{{ form.errors.get(`notes`) }}
        </span>
    </div>
</div>
