<div class="form-row mt-5">

    <div class="col-sm-6">
    <h5 class="mb-3 mt-3">
        @isset($title)
            {{ $title }}
        @else
            Client Contacts
        @endif
    </h5>
    </div>

    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(input, index) in form.contacts">
                    <td class="text-center">
                        <input type="text" placeholder="Full Name" dusk="client-contact-name"
                            class="form-control-full form-control"
                            v-model="input.name"
                            :class="{'is-invalid': form.errors.has(`contacts.${index}.name`)}"
                            dusk="client-contact-name">
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`contacts.${index}.name`)">
                            @{{ form.errors.get(`contacts.${index}.name`) }}
                        </span>
                        <small class="form-text text-left text-muted">
                            Ex: "George Clooney"
                        </small>
                    </td>
                    <td class="text-center">
                        <input type="text" placeholder="Email" dusk="contact-email"
                            class="form-control-full form-control"
                            v-model="input.email"
                            :class="{'is-invalid': form.errors.has(`contacts.${index}.email`)}"
                            dusk="contact-email">
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`contacts.${index}.email`)">
                            @{{ form.errors.get(`contacts.${index}.email`) }}
                        </span>
                        <small class="form-text text-left text-muted">
                            Ex: "gclooney@somewhere.com"
                        </small>
                    </td>
                    <td class="text-center">
                        <input type="text" placeholder="Phone" dusk="contact-phone"
                            class="form-control-full form-control"
                            v-model="input.phone"
                            dusk="contact-phone">
                        <small class="form-text text-left text-muted">
                            Ex: "+1 (800) 555-1212"
                        </small>
                    </td>
                    <td class="text-center">
                        <input type="text" placeholder="Role"
                            class="form-control-full form-control" dusk="hotel-contact-role"
                            v-model="input.role"
                            :class="{'is-invalid': form.errors.has(`contacts.${index}.role`)}" dusk="hotel-contact-role">
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`contacts.${index}.role`)">
                            @{{ form.errors.get(`contacts.${index}.role`) }}
                        </span>
                        <small class="form-text text-left text-muted">
                            Ex: "Accountant"
                        </small>
                    </td>
                    <td class="text-center">
                        <a href="#" @click.prevent="deleteContactRow(index)"
                            class="btn btn-danger btn-sm" data-offline="disabled">
                            <i class="fa fa-close"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-secondary">
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center">
                        <a href="#" @click.prevent="addContactRow"
                            class="btn btn-primary btn-sm ml-2 mr-0" data-offline="disabled"
                            dusk="add-line-hotel-contact">
                            <i class="fa fa-plus mr-2"></i> Add Line
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
