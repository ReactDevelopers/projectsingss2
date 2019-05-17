<div class="form-row mt-5">
    <div class="col-sm-12">
        <h6>
            Min Nights:
            <span v-text="friendlyDate(meta.inventory_min_check_in)"></span> -
            <span v-text="friendlyDate(meta.inventory_min_check_out)"></span>
            (<span v-text="minNights(meta)"></span>)
        </h6>
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th scope="col">Room Type/Name</th>
                    <th scope="col" class="text-right">Rooms</th>
                    <th scope="col" class="text-center">Check-in/out</th>
                    <th scope="col" class="text-center">Days</th>
                    <th scope="col" class="text-center">RmNts</th>
                    <th scope="col" class="text-right">Rate (@{{ currency.name }})</th>
                    <th scope="col" class="text-right">Total (@{{ currency.name }})</th>
                    <th scope="col" class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(confirmation_item, index) in confirmation_items">
                    <td>
                        <select @change="updateConfirmationItems(meta)"
                            class="form-control" name="room_name[]" v-model="confirmation_item.room" dusk="room-name"
                            :class="{'is-invalid': form.errors.has(`confirmation_items.${index}.room.id`)}">
                            <option v-for="inventory in meta.room_type_inventories"
                                v-text="inventory.room_name" :value="inventory" :dusk="inventory.room_name"></option>
                        </select>
                        <span class="invalid-feedback"
                            v-show="form.errors.has(`confirmation_items.${index}.room.id`)">
                            @{{ form.errors.get(`confirmation_items.${index}.room.id`) }}
                        </span>
                    </td>
                    <td class="text-right">
                        <input @change="updateConfirmationItems(meta)"
                            type="text" name="quantity[]" dusk="room-quantity"
                            class="form-control-short form-control float-right text-right"
                            v-model="confirmation_item.quantity"
                            :class="{'is-invalid': form.errors.has(`confirmation_items.${index}.quantity`)}">
                        <span class="invalid-feedback float-right text-right w-100"
                            v-show="form.errors.has(`confirmation_items.${index}.quantity`)">
                            @{{ form.errors.get(`confirmation_items.${index}.quantity`) }}
                        </span>
                        <br class="clearfix">
                        <span :id="`show_max_room_notice_${index}`" class="float-right mt-2 w-100"
                            :class="[ confirmation_item.max_rooms == 0 ? 'text-danger' : '' ]">
                            @{{ confirmation_item.message }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="navbar-item" id="date_picker">
                            <date-pick @input="updateConfirmationItems(meta)"
                                name="confirmation_item.check_in" 
                                v-model="confirmation_item.check_in"
                                :input-attributes="{
                                    dusk: 'confirmation-item-check-in',
                                    name: 'confirmation_item.check_in',
                                    class: 'form-date-picker form-control mb-1 text-center {{ $errors->has('confirmation_items.index.check_in') ? 'is-invalid' : '' }}',
                                    placeholder: 'dd/mm/yyyy',
                                    autocomplete: 'off'
                                }"
                                :display-format="'DD/MM/YYYY'"
                                :start-week-on-sunday="true">
                            </date-pick>
                        </div>
                        <div class="navbar-item" id="date_picker">
                            <date-pick @input="updateConfirmationItems(meta)"
                                name="confirmation_item.check_out" 
                                v-model="confirmation_item.check_out"
                                :input-attributes="{
                                    dusk: 'confirmation-item-check-out',
                                    name: 'confirmation_item.check_out',
                                    class: 'form-date-picker form-control mb-1 text-center {{ $errors->has('confirmation_items.index.check_out') ? 'is-invalid' : '' }}',
                                    placeholder: 'dd/mm/yyyy',
                                    autocomplete: 'off'
                                }"
                                :display-format="'DD/MM/YYYY'"
                                :start-week-on-sunday="true">
                            </date-pick>
                        </div>
                        <span class="invalid-feedback"
                            v-show="hasFormErrorsOnDates(index)">
                            @{{ formErrorOnDate(index) }}
                        </span><br>
                        <span :class="confirmation_item.date_warning_class"
                            v-if="confirmation_item.date_warning_message"
                            v-text="confirmation_item.date_warning_message"></span>
                    </td>
                    <td class="text-center">
                        <div v-if="abbreviatedWeekday(confirmation_item.check_in) && abbreviatedWeekday(confirmation_item.check_out) && numNights(confirmation_item.check_in, confirmation_item.check_out)">
                            <span v-text="abbreviatedWeekday(confirmation_item.check_in)"></span> -
                            <span v-text="abbreviatedWeekday(confirmation_item.check_out)"></span>
                            (<span v-text="numNights(confirmation_item.check_in, confirmation_item.check_out)"></span>)
                        </div>
                    </td>
                    <td class="text-center"
                        v-text="numRoomNights(confirmation_item.check_in, confirmation_item.check_out, confirmation_item.quantity)">
                    </td>
                    <td class="text-right">
                        <input type="text" class="form-control-short form-control mb-1 float-right text-right" dusk="room-rate"
                            v-model="confirmation_item.rate"
                            :class="{'is-invalid': form.errors.has(`confirmation_items.${index}.rate`)}" @input="checkRateChanged(confirmation_item, index)"> <br>
                        <span class="invalid-feedback float-right text-right w-100"
                            v-show="form.errors.has(`confirmation_items.${index}.rate`)">
                            @{{ form.errors.get(`confirmation_items.${index}.rate`) }}
                        </span>
                        <br class="clearfix">
                        <div class="float-right">
                            <div class="info-tooltip" v-if="confirmation_item.room.id">
                                <i class="fa fa-info"></i>
                                <span class="tooltip-text" v-html="confirmation_item.tooltip_message"></span>
                            </div>
                            <em v-if="confirmation_item.room.id">
                                <span v-text="confirmation_item.suggested_rate"></span>
                            </em>
                        </div>
                    </td>
                    <td class="text-right"
                        v-if="typeof currency !== undefined"
                        v-text="formattedMoney(totalForConfirmationItem(confirmation_item), currency.symbol)"></td>
                    <td class="text-right">
                        <a href="#" @click.prevent="deleteRoomRow(index)" class="btn btn-danger btn-sm" data-offline="disabled" dusk="close-room-configuration">
                            <i class="fa fa-close"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-active">
                    <td><strong>Totals</strong></td>
                    <td class="text-right"><strong></strong></td>
                    <td class="text-right"><strong></strong></td>
                    <td class="text-center"><strong></strong></td>
                    <td class="text-center"><strong v-text="totalRoomNights()"></strong></td>
                    <td class="text-right"><strong></strong></td>
                    <td class="text-right">
                        <strong v-if="typeof currency !== undefined"
                            v-text="formattedMoney(totalConfirmationCharges(), currency.symbol)"></strong>
                    </td>
                    <td class="text-right">
                        <a href="#" @click.prevent="addRoomRow" class="btn btn-primary btn-sm ml-2 mr-0" data-offline="disabled" dusk="add-line-room-configuration">
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
        <input type="text" class="form-control" v-model="additional_notes">
    </div>
</div>
