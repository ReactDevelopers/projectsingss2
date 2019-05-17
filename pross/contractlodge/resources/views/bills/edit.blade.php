@extends('spark::layouts.app')

@section('content')
<bills-edit :user="user" :race-id="{{ isset($race->id) ? $race->id : '0' }}"
    :hotel-id="{{ isset($hotel->id) ? $hotel->id : '0' }}" :race-hotel-id="{{ isset($meta->id) ? $meta->id: '0' }}"
    :inventory-currency-id="{{ isset($meta->inventory_currency_id) ? $meta->inventory_currency_id: '0' }}"
    :bill-id="{{ isset($bill->id) ? $bill->id : '0' }}"
    inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">
                        <a href="{{ route('races.index') }}" title="{{__('Races')}}">{{__('Races')}}</a> /
                        <a href="{{ route('races.show', ['race' => $race->id]) }}" title="{{ $race->full_name }}">{{ $race->full_name }}</a> /
                        <a href="{{ route('races.hotels.show', ['race' => $race->id, 'hotel' => $hotel->id]) }}">{{ $hotel->name }}</a> /
                        {{__('Payments')}}
                    </div>

                    <div class="card-body">
                        <div class="form-row mb-4">
                            <div class="col-sm-12">
                                <h5>
                                    {{ $hotel->name }} Payment Schedule
                                    for {{ $race->full_name }}
                                </h5>
                            </div>
                        </div>

                        <div class="form-row mb-4">
                            <div class="col-sm-2">
                                <label><strong>Contract Signed</strong></label> <br>
                                <date-pick name="contract_signed_on"
                                    v-model="contract_signed_on"
                                    :input-attributes="{
                                        name: 'contract_signed_on',
                                        class: 'form-date-picker form-control mb-1 text-center {{ $errors->has(`contract_signed_on`) ? 'is-invalid' : '' }}',
                                        placeholder: 'dd/mm/yyyy',
                                        autocomplete: 'off',
                                        dusk: 'contract-signed-date',
                                    }"
                                    :display-format="'DD/MM/YYYY'"
                                    :start-week-on-sunday="true">
                                </date-pick>
                                <span class="invalid-feedback" v-show="form.errors.has('contract_signed_on')">
                                    @{{ form.errors.get('contract_signed_on') }}
                                </span>
                                <small class="form-text text-muted">Ex: "31/12/2020"</small>
                            </div>
                            <div class="col-sm-2">
                                <label><strong>Bill Currency</strong></label> <br>
                                <template v-if="typeof currency !== undefined">
                                    <select @change="fetchExchangeRate()" class="form-exchange-rate form-control"
                                        v-model="currency" name="currency" dusk="bill-currency">
                                        <option v-for="c in currencies" :dusk="c.id" :value="c">@{{ c.name }}</option>
                                    </select>
                                </template>
                                <small class="form-text text-muted">Ex: "USD"</small>
                            </div>
                            <div class="col-sm-2">
                                <label><strong>Exchange</strong></label> <br>
                                <template v-if="typeof exchange_currency !== undefined">
                                    <select @change="fetchExchangeRate()" class="form-exchange-rate form-control"
                                        v-model="exchange_currency" name="exchange_currency" dusk="exchange">
                                        <option v-for="c in currencies" :value="c" :dusk="c.id">@{{ c.name }}</option>
                                    </select>
                                </template>
                                <small class="form-text text-muted">Ex: "EUR"</small>
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-sm-6">
                                <h5 class="mb-3 mt-3">Payment Schedule</h5>
                            </div>
                        </div>

                        <div class="form-row mb-5">
                            <div class="col-sm-12">
                                <table class="table table-sm table-striped override-table">
                                    <thead>
                                        <tr>
                                            <th>Payment</th>
                                            <th class="text-right">
                                                Amount due (<span v-text="currency.name"></span>)
                                            </th>
                                            <th class="text-center">Due on</th>
                                            <th class="text-right">
                                                Amount paid (<span v-text="currency.name"></span>)
                                            </th>
                                            <th class="text-center">Paid on</th>
                                            <th class="text-center">@{{ __('To Accounts') }}</th>
                                            <th class="text-center">@{{ __('Invoice Nº') }}</th>
                                            <th class="text-center">@{{ __('Invoice Date') }}</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(payment, index) in payments">
                                            <td>
                                                <input type="text" class="form-control-full form-control" name="payment_name[]"
                                                    v-model="payment.payment_name"
                                                    placeholder="Payment description"
                                                    :class="{'is-invalid': form.errors.has(`payments.${index}.payment_name`)}" dusk="bill-payment-name">
                                                <span class="invalid-feedback"
                                                    v-show="form.errors.has(`payments.${index}.payment_name`)">
                                                    @{{ form.errors.get(`payments.${index}.payment_name`) }}
                                                </span>
                                                <small class="form-text text-muted">Ex: "Deposit"</small>
                                            </td>
                                            <td class="text-right clearfix">
                                                <input type="text" class="form-control-short form-control mb-1 pull-right text-right"
                                                    v-model="payment.amount_due"
                                                    :class="{'is-invalid': form.errors.has(`payments.${index}.amount_due`)}" dusk="bill-amount-due">
                                                <span class="form-text text-muted" style="clear:both;" v-if="getExchangeAmountDue(payment.amount_due)">
                                                    (est @{{ getExchangeAmountDue(payment.amount_due) }})
                                                </span>
                                                <span class="invalid-feedback"
                                                    v-show="form.errors.has(`payments.${index}.amount_due`)">
                                                    @{{ form.errors.get(`payments.${index}.amount_due`) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="navbar-item" id="date_picker">
                                                    <date-pick name="payment.due_on"
                                                        v-model="payment.due_on"
                                                        :input-attributes="{
                                                            name: 'payment.due_on',
                                                            class: 'form-date-picker form-control mb-1 text-center {{ $errors->has(`payments.index.due_on`) ? 'is-invalid' : '' }}',
                                                            placeholder: 'dd/mm/yyyy',
                                                            autocomplete: 'off',
                                                            dusk: 'bill-due',
                                                        }"
                                                        :display-format="'DD/MM/YYYY'"
                                                        :start-week-on-sunday="true">
                                                    </date-pick>
                                                </div>
                                                <span class="invalid-feedback"
                                                    v-show="form.errors.has(`payments.${index}.due_on`)">
                                                    @{{ form.errors.get(`payments.${index}.due_on`) }}
                                                </span>
                                                <small class="form-text text-muted" style="clear:both;">
                                                    Ex: "31/12/2019"
                                                </small>
                                            </td>
                                            <td class="text-right">
                                                <input type="text" class="form-control-short form-control mb-1 float-right text-right"
                                                    v-model="payment.amount_paid"
                                                    :class="{'is-invalid': form.errors.has(`payments.${index}.amount_paid`)}" dusk="amount-paid">
                                                <small class="form-text text-muted" style="clear:both;" v-if="getExchangeAmountDue(payment.amount_paid)">
                                                    (est @{{ getExchangeAmountDue(payment.amount_paid) }})
                                                </small>
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
                                                            name: 'payment.paid_on',
                                                            class: 'form-date-picker form-control mb-1 text-center {{ $errors->has(`payments.index.paid_on`) ? 'is-invalid' : '' }}',
                                                            placeholder: 'dd/mm/yyyy',
                                                            autocomplete: 'off',
                                                            dusk: 'amount-paid-on',
                                                        }"
                                                        :display-format="'DD/MM/YYYY'"
                                                        :start-week-on-sunday="true">
                                                    </date-pick>
                                                </div>
                                                <span class="invalid-feedback"
                                                    v-show="form.errors.has(`payments.${index}.paid_on`)">
                                                    @{{ form.errors.get(`payments.${index}.paid_on`) }}
                                                </span>
                                                <small class="form-text text-muted" style="clear:both;">
                                                    Ex: "31/12/2019"
                                                </small>
                                            </td>
                                            <td class="text-center" dusk="payment-to-accounts-on-container">
                                                <div class="navbar-item" id="date_picker">
                                                    <date-pick name="payment.to_accounts_on"
                                                        v-model="payment.to_accounts_on"
                                                        :input-attributes="{
                                                            name: 'payment.to_accounts_on',
                                                            class: {'form-date-picker form-control text-center': !form.errors.has(`payments.${index}.to_accounts_on`) , 'form-date-picker form-control text-center is-invalid': form.errors.has(`payments.${index}.to_accounts_on`)},
                                                            placeholder: 'dd/mm/yyyy',
                                                            autocomplete: 'off',
                                                            dusk: 'payment-to-accounts-on',
                                                        }"
                                                        :display-format="'DD/MM/YYYY'"
                                                        :start-week-on-sunday="true">
                                                    </date-pick>
                                                </div>
                                                <span class="invalid-feedback"
                                                    v-show="form.errors.has(`payments.${index}.to_accounts_on`)">
                                                    @{{ form.errors.get(`payments.${index}.to_accounts_on`) }}
                                                </span>
                                                <small class="form-text text-muted" style="clear:both;">
                                                    Ex: "31/12/2019"
                                                </small>
                                            </td>
                                            <td class="text-right">
                                                <input type="text" class="form-control-short form-control mb-1 float-right"
                                                    v-model="payment.invoice_number"
                                                    :class="{'is-invalid': form.errors.has(`payments.${index}.invoice_number`)}" dusk="invoice-number">
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
                                                <small class="form-text text-muted" style="clear:both;">
                                                    Ex: "31/12/2019"
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <a href="#" @click.prevent="deletePaymentRow(index)" class="btn btn-danger btn-sm" data-offline="disabled" dusk="clear-payment-schedule">
                                                    <i class="fa fa-close"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <td><strong>Totals</strong></td>
                                            <td class="text-right">
                                                <strong v-if="currency" v-text="formattedMoney(totalPaymentDueMoney(), currency.symbol)">
                                                </strong>
                                                <small class="text-muted form-text" v-text="getExchangeAmountDue(totalPaymentDueMoney())"></small>
                                            </td>
                                            <td></td>
                                            <td class="text-right">
                                                <span v-show="paidEqualsDue()">
                                                    <i class="fa fa-check-circle text-success mr-2"></i>
                                                </span>
                                                <strong v-if="currency" v-text="formattedMoney(totalPaymentPaidMoney(), currency.symbol)"
                                                    :class="{'text-success': paidEqualsDue()}">
                                                </strong>
                                                <small class="text-muted form-text" style="clear:both;">
                                                    (est @{{getExchangeAmountDue(totalPaymentPaidMoney())}})
                                                </small>
                                            </td>
                                            <td colspan="4"></td>
                                            <td class="text-center">
                                                <a href="#" @click.prevent="addPaymentRow"
                                                    class="btn btn-primary btn-sm ml-2 mr-0" data-offline="disabled" dusk="add-line">
                                                    <i class="fa fa-plus mr-2"></i> Add Line
                                                </a>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-6 text-right">
                                <a href="{{ route('races.hotels.show', ['race' => $race->id, 'hotel' => $hotel->id]) }}"
                                    class="btn btn-default" dusk="cancel-bill">Cancel</a>
                                <button type="submit" class="btn btn-primary mx-3" @click.prevent="update" dusk="add-bill">
                                    <i class="fa fa-save mr-2"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</bills-edit>
@endsection
