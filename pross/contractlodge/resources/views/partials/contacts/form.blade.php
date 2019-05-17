<div class="row mt-3">
    <div class="col-sm-12">
        <label><strong>Full Name</strong></label>
        @if ($errors->has('contact_name'))
            <input type="text" class="form-control is-invalid" placeholder="Full Name"
                name="contact_name" value="{{ old('contact_name', @$contact->name) }}">
            <span class="invalid-feedback">
                {{ $errors->first('contact_name') }}
            </span>
        @else
            <input type="text" class="form-control" placeholder="Full Name" name="contact_name"
                value="{{ old('contact_name', @$contact->name) }}">
        @endif
        <small class="form-text text-muted">
            Ex: "George Clooney"
        </small>
    </div>
</div>
<div class="row mt-3">
    <div class="col-sm-6">
        <label><strong>Email</strong></label>
        @if ($errors->has('contact_email'))
            <input type="text" class="form-control is-invalid" placeholder="Email"
                name="contact_email" value="{{ old('contact_email', @$contact->email) }}">
            <span class="invalid-feedback">
                {{ $errors->first('contact_email') }}
            </span>
        @else
            <input type="text" class="form-control" placeholder="Email" name="contact_email"
                value="{{ old('contact_email', @$contact->email) }}">
        @endif
        <small class="form-text text-muted">
            Ex: "gclooney@somewhere.com"
        </small>
    </div>
    <div class="col-sm-6">
        <label><strong>Phone</strong></label>
        @if ($errors->has('contact_phone'))
            <input type="text" class="form-control is-invalid" placeholder="Phone"
                name="contact_phone" value="{{ old('contact_phone', @$contact->phone) }}">
            <span class="invalid-feedback">
                {{ $errors->first('contact_phone') }}
            </span>
        @else
            <input type="text" class="form-control" placeholder="Phone" name="contact_phone"
                value="{{ old('contact_phone', @$contact->phone) }}">
        @endif
        <small class="form-text text-muted">
            Ex: "+1 (800) 555-1212"
        </small>
    </div>
</div>
<div class="row mt-3">
    <div class="col-sm-4">
        <label><strong>Role</strong></label>
        @if ($errors->has('contact_role'))
            <input type="text" class="form-control is-invalid" placeholder="Role"
                name="contact_role" value="{{ old('contact_role', @$contact->role) }}">
            <span class="invalid-feedback">
                {{ $errors->first('contact_role') }}
            </span>
        @else
            <input type="text" class="form-control" placeholder="Role" name="contact_role"
                value="{{ old('contact_role', @$contact->role) }}">
        @endif
        <small class="form-text text-muted">
            Ex: "Accountant"
        </small>
    </div>
</div>
