<div class="form-row mt-5">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th scope="col" class="text-center">Date</th>
                    <th scope="col">Description</th>
                    <th scope="col" class="text-right">Quantity</th>
                    <th scope="col" class="text-right">Rate ({{ currency.name }})</th>
                    <th scope="col" class="text-right">Total ({{ currency.name }})</th>
                    <th scope="col" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(invoice_item, index) in invoice_items">
                    <td class="text-center">
                        <div class="navbar-item" id="date_picker">
                            <date-pick name="invoice_item.date" dusk="invoice-date"
                                v-model="invoice_item.date"
                                :input-attributes="{
                                    name: 'invoice_item.date',
                                    class: {'form-date-picker form-control mb-1 text-center ': !form.errors.has(`invoice_items.${index}.date`) , 'form-date-picker form-control mb-1 text-center is-invalid': form.errors.has(`invoice_items.${index}.date`)},
                                    placeholder: 'dd/mm/yyyy',
                                    autocomplete: 'off',
                                    dusk: 'room-date'
                                }"
                                :display-format="'DD/MM/YYYY'"
                                :start-week-on-sunday="true">
                            </date-pick>
                        </div>
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`invoice_items.${index}.date`)">
                            {{ form.errors.get(`invoice_items.${index}.date`) }}
                        </span>
                    </td>
                    <td>
                        <input type="text" name="description[]" dusk="invoice-description"
                            class="form-control-full form-control"
                            v-model="invoice_item.description"
                            :class="{'is-invalid': form.errors.has(`invoice_items.${index}.description`)}">
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`invoice_items.${index}.description`)">
                            {{ form.errors.get(`invoice_items.${index}.description`) }}
                        </span>
                    </td>
                    <td class="text-right">
                        <input type="text" name="quantity[]"
                            class="form-control-short form-control text-right float-right"
                            v-model="invoice_item.quantity" dusk="invoice-qty" @input="setExchangedCustomInvoiceRates"
                            :class="{'is-invalid': form.errors.has(`invoice_items.${index}.quantity`)}">
                        <span class="invalid-feedback float-right text-right w-100"
                            v-show="form.errors.has(`invoice_items.${index}.quantity`)">
                            {{ form.errors.get(`invoice_items.${index}.quantity`) }}
                        </span>
                    </td>
                    <td class="text-right">
                        <input type="text" name="rate[]"
                            class="form-control-short form-control text-right float-right"
                            v-model="invoice_item.rate" dusk="invoice-rate" @input="setExchangedCustomInvoiceRates"
                            :class="{'is-invalid': form.errors.has(`invoice_items.${index}.rate`)}">
                        <br class="clearfix">
                        <small v-if="invoice_item.rate_exchanged && (inventory_currency.name !== currency.name)"
                            class="float-right text-muted" style="clear:both;">
                                (est {{ formattedMoney(invoice_item.rate_exchanged, inventory_currency.symbol) }})
                        </small>
                        <span class="invalid-feedback float-right text-right w-100"
                            v-show="form.errors.has(`invoice_items.${index}.rate`)">
                            {{ form.errors.get(`invoice_items.${index}.rate`) }}
                        </span>
                    </td>
                    <td class="text-right"
                        v-if="typeof currency !== undefined">
                        <span class="qty_rate_total" v-text="formattedMoney(totalForCustomInvoiceItem(invoice_item), currency.symbol)"></span>
                        <br class="clearfix">
                        <small v-if="totalExchangedForCustomInvoiceItem(invoice_item) && (inventory_currency.name !== currency.name)"
                            class="float-right text-muted" style="clear:both;">
                                (est {{ formattedMoney(totalExchangedForCustomInvoiceItem(invoice_item), inventory_currency.symbol) }})
                        </small>
                    </td>
                    <td class="text-right">
                        <a href="#" @click.prevent="deleteInvoiceRow(index)" class="btn btn-danger btn-sm" data-offline="disabled" dusk="delete-invoice-rate">
                            <i class="fa fa-close"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-secondary">
                    <td><strong>Totals</strong></td>
                    <td> </td>
                    <td></td>
                    <td></td>
                    <td class="text-right">
                        <strong v-if="typeof currency !== undefined"
                            v-text="formattedMoney(totalCustomInvoiceCharges(), currency.symbol)"></strong> <br>
                        <small class="float-right text-muted" style="clear:both;"
                            v-if="totalExchangedCustomInvoiceCharges() && (inventory_currency.name !== currency.name)">
                                (est {{ formattedMoney(totalExchangedCustomInvoiceCharges(), inventory_currency.symbol) }})
                        </small>
                    </td>
                    <td class="text-right">
                        <a href="#" @click.prevent="addInvoiceRow"
                            class="btn btn-primary btn-sm ml-2 mr-0" data-offline="disabled" dusk="invoice-add-line">
                            <i class="fa fa-plus mr-2"></i> Add Line
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="form-row my-4">
    <div class="col-sm-12">
        <label><strong>Additional Notes</strong></label>
        <input type="text" class="form-control"
            v-model="additional_notes" dusk="additional-notes">
    </div>
</div>
