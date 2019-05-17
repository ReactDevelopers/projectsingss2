<div class="form-row mb-3">
    <div class="col-sm-6">
        <h5 class="mb-3 mt-3">Payment Schedule</h5>
    </div>
</div>
<div class="form-row mb-5">
    <div class="col-sm-12" dusk="payment-schedule-container">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th scope="col">Payment</th>
                    <th scope="col" class="text-right">Amount due (@{{ currency.name }})</th>
                    <th scope="col" class="text-center">Due on</th>
                    <th scope="col" class="text-right">Amount paid (@{{ currency.name }})</th>
                    <th scope="col" class="text-center">Paid on</th>
                    <th scope="col" class="text-center">@{{ __('To Accounts') }}</th>
                    <th scope="col" class="text-center">@{{ __('Invoice Nº') }}</th>
                    <th scope="col" class="text-center">@{{ __('Invoice Date') }}</th>
                    <th scope="col" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(payment, index) in payments">
                    <td>
                        <input type="text" name="payment_name[]"
                            class="form-control-full form-control" dusk="payment-name"
                            v-model="payment.payment_name"
                            placeholder="Payment description"
                            :class="{'is-invalid': form.errors.has(`payments.${index}.payment_name`)}">
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`payments.${index}.payment_name`)">
                            @{{ form.errors.get(`payments.${index}.payment_name`) }}
                        </span>
                    </td>
                    <td class="text-right">
                        <input type="text" class="form-control-short form-control mb-1 float-right text-right" dusk="amount-due-invoice"
                            v-model="payment.amount_due"
                            :class="{'is-invalid': form.errors.has(`payments.${index}.amount_due`)}">
                        <span class="invalid-feedback float-right text-right w-100"
                            v-show="form.errors.has(`payments.${index}.amount_due`)">
                            @{{ form.errors.get(`payments.${index}.amount_due`) }}
                        </span>
                    </td>
                    <td class="text-center" dusk="due-on-container">
                        <div class="navbar-item" id="date_picker">
                            <date-pick name="payment.due_on" id="due-on"
                                v-model="payment.due_on"
                                :input-attributes="{
                                    dusk: 'due-on',
                                    name: 'payment.due_on',
                                    class: {'form-date-picker form-control text-center': !form.errors.has(`payments.${index}.due_on`) , 'form-date-picker form-control text-center is-invalid': form.errors.has(`payments.${index}.due_on`)},
                                    placeholder: 'dd/mm/yyyy',
                                    autocomplete: 'off',
                                    dusk: 'due-on'
                                }"
                                :display-format="'DD/MM/YYYY'"
                                :start-week-on-sunday="true">
                            </date-pick>
                        </div>
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`payments.${index}.due_on`)">
                            @{{ form.errors.get(`payments.${index}.due_on`) }}
                        </span>
                    </td>
                    <td class="text-right">
                        <input type="text" class="form-control-short form-control mb-1 float-right text-right"
                            v-model="payment.amount_paid" dusk="amt-paid"
                            :class="{'is-invalid': form.errors.has(`payments.${index}.amount_paid`)}">
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`payments.${index}.amount_paid`)">
                            @{{ form.errors.get(`payments.${index}.amount_paid`) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="navbar-item" id="date_picker">
                            <date-pick name="payment.paid_on"
                                v-model="payment.paid_on"
                                :input-attributes="{
                                    dusk: 'paid-on',
                                    name: 'payment.paid_on',
                                    class: 'form-date-picker form-control mb-1 text-center {{ $errors->has(`payments.index.paid_on`) ? 'is-invalid' : '' }}',
                                    placeholder: 'dd/mm/yyyy',
                                    autocomplete: 'off',
                                    dusk: 'paid-on'
                                }"
                                :display-format="'DD/MM/YYYY'"
                                :start-week-on-sunday="true">
                            </date-pick>
                        </div>
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`payments.${index}.paid_on`)">
                            @{{ form.errors.get(`payments.${index}.paid_on`) }}
                        </span>
                    </td>
                    <td class="text-center" dusk="to-accounts-on-container">
                        <div class="navbar-item" id="date_picker">
                            <date-pick name="payment.to_accounts_on"
                                v-model="payment.to_accounts_on"
                                :input-attributes="{
                                    dusk: 'to_accounts_on',
                                    name: 'payment.to_accounts_on',
                                    class: {'form-date-picker form-control text-center': !form.errors.has(`payments.${index}.to_accounts_on`) , 'form-date-picker form-control text-center is-invalid': form.errors.has(`payments.${index}.to_accounts_on`)},
                                    placeholder: 'dd/mm/yyyy',
                                    autocomplete: 'off',
                                    dusk: 'to-accounts-on'
                                }"
                                :display-format="'DD/MM/YYYY'"
                                :start-week-on-sunday="true">
                            </date-pick>
                        </div>
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`payments.${index}.to_accounts_on`)">
                            @{{ form.errors.get(`payments.${index}.to_accounts_on`) }}
                        </span>
                    </td>
                    <td class="text-right">
                        <input type="text" class="form-control-short form-control mb-1 float-right"
                            v-model="payment.invoice_number"
                            :class="{'is-invalid': form.errors.has(`payments.${index}.invoice_number`)}" dusk="invoice-no">
                        <span class="invalid-feedback float-right text-right w-100"
                            v-show="form.errors.has(`payments.${index}.invoice_number`)">
                            @{{ form.errors.get(`payments.${index}.invoice_number`) }}
                        </span>
                    </td>
                    <td class="text-center" dusk="payment-invoice-date-container">
                        <div class="navbar-item" id="date_picker">
                            <date-pick name="payment.invoice_date"
                                v-model="payment.invoice_date"
                                :input-attributes="{
                                    dusk: 'invoice_date',
                                    name: 'payment.invoice_date',
                                    class: {'form-date-picker form-control text-center': !form.errors.has(`payments.${index}.invoice_date`) , 'form-date-picker form-control text-center is-invalid': form.errors.has(`payments.${index}.invoice_date`)},
                                    placeholder: 'dd/mm/yyyy',
                                    autocomplete: 'off',
                                    dusk: 'payment-invoice-date'
                                }"
                                :display-format="'DD/MM/YYYY'"
                                :start-week-on-sunday="true">
                            </date-pick>
                        </div>
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`payments.${index}.invoice_date`)">
                            @{{ form.errors.get(`payments.${index}.invoice_date`) }}
                        </span>
                    </td>
                    <td class="text-right">
                        <a href="#" @click.prevent="deletePaymentRow(index)" class="btn btn-danger btn-sm" data-offline="disabled" dusk="delete-payment">
                            <i class="fa fa-close"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td>
                        <strong>Totals</strong>
                        <em :class="{'text-danger': ! isPaymentsEqualToCharges()}">
                            Conf. amount must = amount due
                        </em>
                    </td>
                    <td class="text-right">
                        <strong>Total Payments: </strong>
                        <strong v-text="formattedMoney(totalPaymentsAmountDue(), currency.symbol)"
                            v-if="typeof currency !== undefined"
                            :class="{'text-success': isPaymentsEqualToCharges()}"></strong>
                        <br/>
                        <span class="text-danger" v-if="! isPaymentsEqualToCharges()">
                            Remaining:
                            <span v-text="formattedMoney(totalPaymentRemainingBalance(), currency.symbol)"
                                v-if="typeof currency !== undefined"></span>
                        </span>
                    </td>
                    <td></td>
                    <td class="text-right">
                        <span v-show="isAmountDueEqualToAmountPaid()">
                            <i class="fa fa-check-circle text-success mr-2"></i>
                        </span>
                        <strong v-text="formattedMoney(totalPaymentsAmountPaid(), currency.symbol)"
                            v-if="typeof currency !== undefined"
                            :class="{'text-success': isAmountDueEqualToAmountPaid()}"></strong>
                    </td>
                    <td colspan="4"></td>
                    <td class="text-right">
                        <a href="#" @click.prevent="addPaymentRow"
                            class="btn btn-primary btn-sm ml-2 mr-0" data-offline="disabled" dusk=add-line-payment>
                            <i class="fa fa-plus mr-2"></i> Add Line
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
