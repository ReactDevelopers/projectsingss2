<div class="row mb-3">
    <div class="col-md-3">
        <label><strong>Client Company Name</strong></label>
        <input type="text" placeholder="Client Company name"
            class="form-control"
            v-model="name"
            :class="{'is-invalid': form.errors.has(`name`)}"
            dusk="client-name">
        <span class="invalid-feedback"
            v-show="form.errors.has(`name`)">
            @{{ form.errors.get(`name`) }}
        </span>
         <small class="form-text text-muted">
            Ex: "Mercedes"
        </small>
    </div>
    <div class="col-md-3">
        <label><strong>Address</strong></label>
        <input type="text" placeholder="Address"
            class="form-control"
            v-model="address"
            :class="{'is-invalid': form.errors.has(`address`)}"
            dusk="client-address">
        <span class="invalid-feedback"
            v-show="form.errors.has(`address`)">
            @{{ form.errors.get(`address`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "Operations Centre"
        </small>
    </div>
    <div class="col-md-3">
        <label><strong>City</strong></label>
        <input type="text" placeholder="City"
            class="form-control"
            v-model="city"
            :class="{'is-invalid': form.errors.has(`city`)}"
            dusk="client-city">
        <span class="invalid-feedback"
            v-show="form.errors.has(`city`)">
            @{{ form.errors.get(`city`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "Brackley"
        </small>
    </div>
    <div class="col-md-3">
        <label><strong>State/Region</strong></label>
        <input type="text" placeholder="State/Region"
            class="form-control"
            v-model="region"
            :class="{'is-invalid': form.errors.has(`region`)}"
            dusk="client-region">
        <span class="invalid-feedback"
            v-show="form.errors.has(`region`)">
            @{{ form.errors.get(`region`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "Northants"
        </small>
    </div>
    <div class="col-md-3  mt-3">
        <label><strong>Country</strong></label>
        <select class="form-control" name="country_id" v-model="country_id" dusk="client-country">
            @foreach ($countries as $country)
                @if(isset($client)  && !empty($client))
                    <option value="{{ $country->id }}" {{ $country->id === $client->country_id ? 'selected' : '' }}>{{ $country->name }}</option>
                @else
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endif
            @endforeach
        </select>
        <span class="invalid-feedback"
            v-show="form.errors.has(`country_id`)">
            @{{ form.errors.get(`country_id`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "Great Britain"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>Postal Code</strong></label>
        <input type="text" placeholder="Postal Code"
            class="form-control"
            v-model="postal_code"
            :class="{'is-invalid': form.errors.has(`postal_code`)}"
            dusk="client-postal-code">
        <span class="invalid-feedback"
            v-show="form.errors.has(`postal_code`)">
            @{{ form.errors.get(`postal_code`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "NN13 7BD"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>Email</strong></label>
        <input type="text" placeholder="Client Company Email"
            class="form-control"
            v-model="email"
            :class="{'is-invalid': form.errors.has(`email`)}"
            dusk="client-email">
        <span class="invalid-feedback"
            v-show="form.errors.has(`email`)">
            @{{ form.errors.get(`email`) }}
        </span>
        <small class="form-text text-muted">
            Ex: "someone@mercedesamgf1.com"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>Phone</strong></label>
        <input type="text" placeholder="Client Company Phone"
            class="form-control"
            v-model="phone"
            :class="{'is-invalid': form.errors.has(`phone`)}"
            dusk="client-phone">
        <small class="form-text text-muted">
            Ex: "+44 1280 844000"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>Website</strong></label>
        <input type="text" placeholder="Website"
            class="form-control"
            v-model="website"
            :class="{'is-invalid': form.errors.has(`website`)}"
            dusk="client-website">
        <small class="form-text text-muted">
            Ex: "https://www.mercedesamgf1.com"
        </small>
    </div>
    <div class="col-md-3 mt-3">
        <label><strong>{{__('Code')}}</strong></label>
        <input type="text" placeholder="{{__('Code')}}"
            class="form-control"
            v-model="code"
            :class="{'is-invalid': form.errors.has(`code`), 'form-control': true}" dusk="client-code">
        <span class="invalid-feedback" v-if="form.errors.has(`code`)">
            @{{ form.errors.get(`code`) }}
        </span>
    </div>
</div>
