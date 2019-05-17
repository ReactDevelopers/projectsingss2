module.exports = {
    data() {
        return {
            confirmation: {},
            confirmation_items: [],
        }
    },

    methods: {
        /**
         * Create the confirmation using given form/property data
         *
         * @return {Promise}
         */
        createConfirmation () {
            this.form.due_on = this.due_on;
            this.form.client_id = this.client.id;
            this.form.race_id = this.race.id;
            this.form.hotel_id = this.hotel.id;
            this.form.race_hotel_id = this.raceHotelId;
            this.form.currency_id = this.currency.id;
            this.form.notes = this.additional_notes;
            this.form.signed_on = this.signed_on;
            this.form.expires_on = this.due_on;
            this.form.created_by = this.user.id;
            this.form.confirmation_items = this.confirmation_items;
            this.form.payments = this.payments;
            this.form.invoice_type = 'confirmations';

            return Spark.post(`/api/races/${this.race.id}/hotels/${this.hotel.id}/invoices/confirmations`, this.form)
                .then((response) => {
                    let message = 'The Room Confirmation has been saved.';
                    location.href = `/races/${this.race.id}/hotels/${this.hotel.id}/clients/${this.client.id}/confirmations/${response.id}?message=${message}&level=success`;
                }).catch((errors) => {
                    return;
                });
        },

        /**
         * Update the confirmation using given form/property data
         *
         * @return {Promise}
         */
        updateConfirmation () {
            this.form.due_on = this.due_on;
            this.form.client_id = this.client.id;
            this.form.race_id = this.race.id;
            this.form.hotel_id = this.hotel.id;
            this.form.race_hotel_id = this.raceHotelId;
            this.form.currency_id = this.currency.id;
            this.form.notes = this.additional_notes;
            this.form.signed_on = this.signed_on;
            this.form.expires_on = this.due_on;
            this.form.created_by = this.user.id;
            this.form.confirmation_items = this.confirmation_items;
            this.form.payments = this.payments;
            this.form.invoice_type = 'confirmations';
            this.form.confirmation_id = this.confirmationId;

            return Spark.put(`/api/races/${this.race.id}/hotels/${this.hotel.id}/invoices/confirmations/edit`, this.form)
                .then((response) => {
                    let message = 'The Room Confirmation has been saved.';
                    //location.href = `/races/${this.race.id}/hotels/${this.hotel.id}?message=${message}&level=success`;
                    location.href = `/races/${this.race.id}/hotels/${this.hotel.id}/clients/${this.client.id}/confirmations/${this.confirmationId}?message=${message}&level=success`;
                }).catch((errors) => {
                    return;
                });
        },

        /**
         * Sets the tooltip message near the rates.
         *
         * IMPORTANT: Depends on `inventory_currency` and `currency` property on parent.
         * IMPORTANT: Depends on money mixin on parent.
         *
         * @param {object}  confirmation_item Confirmation Item
         * @param {integer} index             Index of Confirmation Item on parent object
         */
        setTooltipMessage (confirmation_item, index) {
            // console.debug('setTooltipMessage() fired.');
            if (typeof this.inventory_currency == undefined || typeof this.currency == undefined) {

                if (! this.setCurrencies()) { // sets `inventory_currency` and `currency` if needed/possible
                    console.error('setTooltipMessage() relies on "inventory_currency" and "currency" properties on parent JS including this mixin.');
                    return;
                }
            }

            let ci = confirmation_item;
            let ic_symbol = this.inventory_currency.symbol;
            let c_symbol = this.currency.symbol;

            let min_night_hotel_rate = this.formattedMoney(ci.room.min_night_hotel_rate, ic_symbol);
            let pre_post_night_hotel_rate = this.formattedMoney(ci.room.pre_post_night_hotel_rate, ic_symbol);
            let min_night_hotel_rate_exchanged = 0;
            let pre_post_night_hotel_rate_exchanged = 0;
            let str = `Min night hotel rate: ${min_night_hotel_rate}`;

            if (typeof ci.room.min_night_hotel_rate_exchanged !== undefined) {
                min_night_hotel_rate_exchanged = this.formattedMoney(ci.room.min_night_hotel_rate_exchanged, c_symbol)
            }

            if (typeof ci.room.pre_post_night_hotel_rate_exchanged !== undefined) {
                pre_post_night_hotel_rate_exchanged = this.formattedMoney(ci.room.pre_post_night_hotel_rate_exchanged, c_symbol);
            }

            if (this.inventory_currency.id !== this.currency.id) {
                str += ` (est ${min_night_hotel_rate_exchanged})`;
            }

            str += `<br />Pre/post hotel rate: ${pre_post_night_hotel_rate}`;

            if (this.inventory_currency.id !== this.currency.id) {
                str += ` (est ${pre_post_night_hotel_rate_exchanged})`;
            }

            ci.tooltip_message = str;

            this.$set(this.confirmation_items, index, ci);
        },

        /**
         * Returns true if the `form` property includes errors for it on check-in/out dates
         * for the given Confirmation Item index
         *
         * @param  {Integer}  index
         *
         * @return {Boolean}
         */
        hasFormErrorsOnDates (index) {
            return (this.form.errors.has(`confirmation_items.${index}.check_in`) || this.form.errors.has(`confirmation_items.${index}.check_out`))
        },

        /**
         * Returns the form errors for check-in/out dates.
         *
         * @param  {Integer} index
         *
         * @return {String}
         */
        formErrorOnDate (index) {
            if (this.form.errors.get(`confirmation_items.${index}.check_in`)) {
                return this.form.errors.get(`confirmation_items.${index}.check_in`);
            }
            return this.form.errors.get(`confirmation_items.${index}.check_out`);
        },

        /**
         * Set the room rates using the current exchange rate
         * IMPORTANT: The money mixin is a dependency for this method to work without error.
         *
         * @param  {Object}  meta Race Hotel object
         *
         * @return {void}
         */
        setExchangedRoomRates (meta) {
            // console.debug('setExchangedRoomRates() fired.', arguments.callee.caller);
            this.fetchExchangeRate().then(() => { // IMPORTANT: Depends on the `money` mixin.
                // console.debug('fetchExchangeRate() fired.');
                let ex = parseFloat(this.exchange_rate);

                // Make sure we set the meta data at the same time so
                // we can match it on the room type select drop-down.
                _.each(meta.room_type_inventories, function (room, index, array) {
                    if (typeof room.races_hotels_inventory !== undefined) {
                        room.min_night_hotel_rate_exchanged       = parseFloat(room.min_night_hotel_rate) * ex;
                        room.pre_post_night_hotel_rate_exchanged  = parseFloat(room.pre_post_night_hotel_rate) * ex;
                        room.min_night_client_rate_exchanged      = parseFloat(room.min_night_client_rate) * ex;
                        room.pre_post_night_client_rate_exchanged = parseFloat(room.pre_post_night_client_rate) * ex;
                        this.$set(meta.room_type_inventories, index, room);
                    }
                }.bind(this));

                return _.each(this.confirmation_items, function (ci, index, array) {
                    if (typeof ci.room !== undefined) {
                        ci.room.min_night_hotel_rate_exchanged       = parseFloat(ci.room.min_night_hotel_rate) * ex;
                        ci.room.pre_post_night_hotel_rate_exchanged  = parseFloat(ci.room.pre_post_night_hotel_rate) * ex;
                        ci.room.min_night_client_rate_exchanged      = parseFloat(ci.room.min_night_client_rate) * ex;
                        ci.room.pre_post_night_client_rate_exchanged = parseFloat(ci.room.pre_post_night_client_rate) * ex;
                        this.$set(this.confirmation_items, index, ci);
                    }

                    this.setTooltipMessage(ci, index);
                    this.setSuggestedRate(ci, index, meta);
                    this.setRateDefaultToSuggestedRate(ci, index, meta);

                }.bind(this));
            });
        },

        /**
         * Returns whether the Conformation Item's check out date is before the check in date
         *
         * @param  {Object}  confirmation_item Confirmation Item
         *
         * @return {Boolean}
         */
        isCheckOutBeforeCheckIn (confirmation_item) {
            if (
                confirmation_item.check_in != ''
                && confirmation_item.check_out != ''
                && confirmation_item.check_in > confirmation_item.check_out
            ) {
                return true;
            }
            return false;
        },

        /**
         * Returns whether the Confirmation Item's dates are outside the range of the min night dates
         *
         * @param  {Object}  confirmation_item Confirmation Item
         * @param  {Object}  meta              Race Hotel object
         *
         * @return {Boolean}
         */
        isOutsideMinNightDates (confirmation_item, meta) {
            if (
                (
                    // Check-in is before Min Check-in
                    // Check-out is equal/before Min Check-in
                    confirmation_item.check_in != ''
                    && confirmation_item.check_out != ''
                    && moment(confirmation_item.check_in) < moment(meta.inventory_min_check_in)
                    && moment(confirmation_item.check_out) <= moment(meta.inventory_min_check_in)
                ) || (
                    // Check-in is equal/after Min Check-out
                    // Check-out is after Min Check-out
                    confirmation_item.check_in != ''
                    && confirmation_item.check_out != ''
                    && moment(confirmation_item.check_in) >= moment(meta.inventory_min_check_out)
                    && moment(confirmation_item.check_out) > moment(meta.inventory_min_check_out)
                )
            ) {
                return true;
            }
            return false;
        },

        /**
         * Returns whether the Confirmation Item's dates are crossing in/out of the min night dates
         *
         * @param  {Object}  confirmation_item Confirmation Item
         * @param  {Object}  meta              Race Hotel object
         *
         * @return {Boolean}
         */
        isCrossingMinNightDates (confirmation_item, meta) {
            if (
                (
                    // Check-in is before Min Check-in
                    // Check-out is after Min Check-in
                    confirmation_item.check_in != ''
                    && confirmation_item.check_out != ''
                    && moment(confirmation_item.check_in) < moment(meta.inventory_min_check_in)
                    && moment(confirmation_item.check_out) > moment(meta.inventory_min_check_in)
                ) || (
                    // Check-out is after Min Check-out
                    // Check-in is before Min Check-out
                    confirmation_item.check_in != ''
                    && confirmation_item.check_out != ''
                    && moment(confirmation_item.check_out) > moment(meta.inventory_min_check_out)
                    && moment(confirmation_item.check_in) < moment(meta.inventory_min_check_out)
                )
            ) {
                return true;
            }
            return false;
        },

        /**
         * Returns whether the Confirmation Item's dates are equal
         *
         * @param  {Object}  confirmation_item Confirmation Item
         *
         * @return {Boolean}
         */
        isSameDates (confirmation_item) {
            if (
                confirmation_item.check_in !== ''
                && confirmation_item.check_out !== ''
                && moment(confirmation_item.check_in) == moment(confirmation_item.check_out)
            ) {
                return true;
            }
            return false;
        },

        /**
         * Returns the total amount owed
         *
         * @return {Float}
         */
        totalConfirmationCharges () {
            let money = _.reduce(this.confirmation_items, function (sum, item) {
                if (typeof this.totalForConfirmationItem(item) == 'undefined') {
                    return sum;
                }
                return sum + parseFloat(this.totalForConfirmationItem(item));
            }.bind(this), 0);

            return money;
        },

        /**
         * Returns the total due for the Confirmation Item object provided
         *
         * @param  {Object} confirmation_item Confirmation Item object
         *
         * @return {Float}
         */
        totalForConfirmationItem (confirmation_item) {
            let rate = confirmation_item.rate;
            let num_room_nights = this.numRoomNights(
                confirmation_item.check_in,
                confirmation_item.check_out,
                confirmation_item.quantity
            );

            if (rate == '' || num_room_nights == '') {
                return;
            }

            return parseFloat(rate) * parseInt(num_room_nights, 10);
        },

        /**
         * Returns the number of room nights given a check in date, check out date, and number of rooms
         *
         * @param  {date}    check_in_date
         * @param  {date}    check_out_date
         * @param  {integer} num_rooms
         *
         * @return {integer}
         */
        numRoomNights (check_in_date, check_out_date, num_rooms) {
            let total = parseInt(this.numNights(check_in_date, check_out_date), 10) * parseInt(num_rooms);
            return (! isFinite(total)) ? '' : total;
        },

        /**
         * Returns the number of nights between a check in and check out date
         *
         * @param  {date}    check_in_date
         * @param  {date}    check_out_date
         *
         * @return {integer}
         */
        numNights (check_in_date, check_out_date) {
            let min_check_in = moment(check_in_date);
            let min_check_out = moment(check_out_date);
            let days = min_check_out.diff(min_check_in, 'days');

            return (! isFinite(days)) ? '' : days;
        },

        /**
         * Returns the number of nights between the minimum check in and check out dates
         *
         * @param  {Object}  meta Race Hotel object
         *
         * @return {integer}
         */
        minNights (meta) {
            return this.numNights(meta.inventory_min_check_in, meta.inventory_min_check_out);
        },

        /**
         * Returns whether the range of check in to check out all falls within the minimum date range
         *
         * @param  {date}    check_in_date
         * @param  {date}    check_out_date
         * @param  {Object}  meta           Race Hotel object
         *
         * @return {Boolean}
         */
        isWithinMinDates (check_in_date, check_out_date, meta) {
            if (
                this.numNights(meta.inventory_min_check_in, check_in_date) >= 0
                && this.numNights(check_out_date, meta.inventory_min_check_out) >= 0
            ) {
                return true;
            }
            return false;
        },

        /**
         * Sets the `suggested_rate` property on the Confirmation Item
         * based on the client rate and exchange rate
         *
         * @param  {Object}  confirmation_item Confirmation Item object
         * @param  {Integer} index             Index of the Confirmation Item object on `this.confirmation_items`
         * @para   {Object}  meta              Race Hotel object
         *
         * @return {Float}
         */
        setSuggestedRate (confirmation_item, index, meta) {
            // console.debug('setSuggestedRate() fired.');
            let rate = 0;

            if (this.isWithinMinDates(confirmation_item.check_in, confirmation_item.check_out, meta)) {
                rate = confirmation_item.room.min_night_client_rate_exchanged;
            } else {
                rate = confirmation_item.room.pre_post_night_client_rate_exchanged;
            }

            confirmation_item.suggested_rate = `${this.formattedMoney(rate, this.currency.symbol)} suggested`;

            this.$set(this.confirmation_items, index, confirmation_item);
        },

        /**
         * Formats the dates on a Confirmation Item
         *
         * @param  {Object} confirmation_item Confirmation Item
         *
         * @return {Object}                   Confirmation Item
         */
        formatConfirmationItemDates (confirmation_item) {
            // console.debug('formatConfirmationItemDates() fired.', JSON.stringify(confirmation_item));
            if (confirmation_item.hasOwnProperty('check_in') && confirmation_item.check_in !== null) {
                confirmation_item.check_in = this.formatDate(confirmation_item.check_in);
            }

            if (confirmation_item.hasOwnProperty('check_out') && confirmation_item.check_out !== null) {
                confirmation_item.check_out = this.formatDate(confirmation_item.check_out);
            }

            return confirmation_item;
        },

        /**
         * Add to the Confirmation Items list/object
         *
         * @return {void}
         */
        addRoomRow () {
            this.confirmation_items.push({
                quantity: '',
                check_in: (typeof this.meta.inventory_min_check_in !== 'undefined') ? moment(this.meta.inventory_min_check_in).format("YYYY-MM-DD") : '',
                check_out: (typeof this.meta.inventory_min_check_out !== 'undefined') ? moment(this.meta.inventory_min_check_out).format("YYYY-MM-DD") : '',
                rate: '',
                room: {},
                date_warning: false,
                max_rooms: 0,
                message: '',
                available_rooms: 0,
                rooms_remaining: 0
            });
        },

        /**
         * Returns the total room nights specified in a Confirmation Item
         *
         * @return {Integer | ''}
         */
        totalRoomNights () {
            let roomNights = _.reduce(this.confirmation_items, function (sum, item) {
                return sum + parseInt(this.numRoomNights(item.check_in, item.check_out, item.quantity), 10);
            }.bind(this), 0);

            return (! isFinite(roomNights)) ? '' : roomNights;
        },

        /**
         * Remove from the Confirmation Items list/object
         *
         * @param  {Integer} index Index of the Confirmation Items array
         *
         * @return {void}
         */
        deleteRoomRow (index) {
            this.confirmation_items.splice(index, 1);
            if (typeof this.confirmation_items[index] != "undefined") {
                this.setRoomsMessage (this.confirmation_items[index], index, this.meta)
            }
            if (typeof this.confirmation_items[index + 1] != "undefined") {
                this.setRoomsMessage (this.confirmation_items[index + 1], (index + 1), this.meta);
            }
            if (typeof this.confirmation_items[index - 1] != "undefined") {
                this.setRoomsMessage (this.confirmation_items[index -1], (index - 1), this.meta);
            }
        },

        /**
         * Fetches the confirmation if `confirmationId` is given
         *
         * @return {Promise | void}
         */
        fetchConfirmation (meta) {
            // console.debug('fetchConfirmation() fired.', this.confirmationId, JSON.stringify(meta));
            if (typeof this.confirmationId == undefined) {
                return;
            }

            return axios.get(`/api/confirmations/${this.confirmationId}`).then(response => {
                this.confirmation = response.data;
                this.confirmation_items = this.confirmation.confirmation_items;
                this.fetchCurrencyById(response.data.currency_id, this.fetchExchangeRate);

                if (typeof this.additional_notes !== undefined) {
                    this.additional_notes = this.confirmation.notes;
                }

                if (typeof this.due_on !== undefined) {
                    this.due_on = this.confirmation.expires_on;
                }

                // console.debug('fetchConfirmation() axios.get() ... pre-return array.');
                return _.each(this.confirmation_items, (ci, index, array) => {
                    // FIXME: For some reason the response for this API call is different than others
                    // where the key `room` is used instead of `races_hotels_inventory`.
                    // So, we'll replicate and use the `room` key instead.
                    this.$set(ci, 'room', ci.races_hotels_inventory);
                    // console.debug('fetchConfirmation() axios.get() _.each() ... pre-formatConfirmationItemDates().', JSON.stringify(ci));
                    this.formatConfirmationItemDates(ci);
                    this.$set(this.confirmation_items, index, ci);
                });
            }).then(response => {
                // console.debug('fetchConfirmation() axios.get().then() ...', JSON.stringify(meta));
                this.updateConfirmationItems(meta);
            });
        },

        /**
         * Get the number of rooms/stays contracted by the room id
         *
         * @param  {Object}  confirmation_item Confirmation Item object
         * @param  {Object}  meta              Race Hotel object
         *
         * @return {Integer}
         */
        getNumContractedRooms (confirmation_item, meta) {

            let inventory = _.find(meta.room_type_inventories, { 'id': confirmation_item.room.id });
            let num_contracted_rooms = inventory.pre_post_nights_contracted;
            let night_type = 'ppn';

            // Get number of these types of rooms available in inventory
            if (this.isWithinMinDates(confirmation_item.check_in, confirmation_item.check_out, meta)) {
                // Switch to using the min night inventory instead
                num_contracted_rooms = inventory.min_stays_contracted;
                night_type = 'mn';
            }

            // FIXME: The API should always respond with a '0' value, not null.
            // FIXME: The database should always save a '0' value, not null.
            // When both of these get fixed, this conditional is unnecessary.
            num_contracted_rooms = (num_contracted_rooms == null) ? 0 : num_contracted_rooms;
            return {num_contracted_rooms: num_contracted_rooms, night_type: night_type};
        },

        /**
         * Returns the number of rooms in other confirmations (same room type)
         *
         * @param  {Integer} room_id       Room Id
         * @param  {Integer} race_hotel_id Race Hotel Id
         *
         * @return {Promise}
         */
        getNumRoomsInOtherConfirmations (room_id, race_hotel_id) {
            let num_rooms_in_other_confirmations = 0;
            let url = `/api/rooms/${room_id}/quantity/${race_hotel_id}`;

            if (this.isEditingConfirmation()) {
                url = `${url}/${this.confirmationId}`;
            }

            return axios.get(url).then(response => {
                return response.data;
                //return parseInt(response.data, 10);
            });
        },

        /**
         * Retrieves the number of rooms in this confirmation by room type
         *
         * @param  {Integer} room_id    Inventory room id
         * @param  {String}  night_type Hotel's night stay type(Min / P&P)
         * @param  {Object}  meta       Race Hotel object
         *
         * @return {Integer}
         */
        getNumRoomsInThisEditingConfirmation (room_id, night_type, meta) {
            return _.reduce(this.confirmation_items, (sum, confirmation_item, value) => {
                if (
                    confirmation_item.room.id == room_id
                    && ! isNaN(confirmation_item.quantity)
                    && confirmation_item.quantity !== ''
                ) {
                    if ('mn' == night_type && this.isWithinMinDates(confirmation_item.check_in, confirmation_item.check_out, meta)) {
                        return parseInt(sum, 10) + parseInt(confirmation_item.quantity, 10);
                    } else if ('ppn' == night_type && ! this.isWithinMinDates(confirmation_item.check_in, confirmation_item.check_out, meta)) {
                        return parseInt(sum, 10) + parseInt(confirmation_item.quantity, 10);
                    }

                }
                return parseInt(sum, 10);
            }, 0);
        },

        /**
         * Show the max rooms available for this inventory room type (id)
         *
         * @param  {Object}  confirmation_item Confirmation Item
         * @param  {Integer} index             Index of Confirmation Item in it's array
         * @param  {Object}  meta              Race Hotel object
         *
         * @return {void}
         */
        setRoomsMessage (confirmation_item, index, meta) {
            // console.debug('setRoomsMessage() fired.');
            // Get number of these types of rooms already in other confirmations.
            this.getNumRoomsInOtherConfirmations(confirmation_item.room.id, confirmation_item.room.race_hotel_id)
                .then(num_in_other_confirmations_data => {
                    // First set the min night quantity by default in other confirmations number
                    // and according to the condition change it to the pre/post night quantity.
                    let num_in_other_confirmations = parseInt(num_in_other_confirmations_data.min_nt_qty, 10);
                    if (! this.isWithinMinDates(confirmation_item.check_in, confirmation_item.check_out, meta)) {
                        num_in_other_confirmations = parseInt(num_in_other_confirmations_data.pp_nt_qty, 10);
                    }

                    // Get number of rooms available in inventory.
                    let num_contracted_data = this.getNumContractedRooms(confirmation_item, meta);
                    let num_contracted = num_contracted_data.num_contracted_rooms;

                    // Get number of these types of rooms already in this confirmation.
                    let num_in_this_confirmation = this.getNumRoomsInThisEditingConfirmation(
                        confirmation_item.room.id,
                        num_contracted_data.night_type,
                        meta
                    );
                    // Subtract the contracted from the actual inventory number
                    let diff = parseInt((num_contracted - num_in_this_confirmation - num_in_other_confirmations), 10);
                    let rooms_remaining = diff;
                    diff = (diff < 0) ? 0 : diff;

                    // Set the max_rooms and message property on the item.
                    this.$set(confirmation_item, 'max_rooms', diff);
                    this.$set(confirmation_item, 'message', `${diff} now available`);
                    this.$set(confirmation_item, 'rooms_remaining', rooms_remaining);
                    this.$set(confirmation_item, 'rooms_available', (num_contracted - num_in_other_confirmations));
            });
        },

        /**
         * Show warnings on confirmation item rows, if necessary
         *
         * @param  {Object}   confirmation_item     Confirmation Item object
         * @param  {Integer}  index                 Index of Confirmation Item on array
         * @param  {Object}   meta                  Race Hotel object
         *
         * @return {void}
         */
        refreshMessages (confirmation_item, index, meta) {
            // console.debug('refreshMessages() fired.', JSON.stringify(confirmation_item));
            this.setDatesMessage(confirmation_item, index, meta);
            this.setRoomsMessage(confirmation_item, index, meta);
            this.setTooltipMessage(confirmation_item, index);
            this.setSuggestedRate(confirmation_item, index, meta);
            this.setRateDefaultToSuggestedRate(confirmation_item, index, meta);
        },

        /**
         * Listener for the Confirmation Item.
         * Updates tooltips and warning messages as needed.
         *
         * @param {Object}  meta Race Hotel object
         *
         * @return {void}
         */
        updateConfirmationItems (meta) {

            // Added this condition to not call the function to generate
            // default payment rows on edit confirmation page.
            if (! this.isEditingConfirmation()) {
                this.generateDefaultPaymentRow();
            }

            // console.debug('updateConfirmationItems() fired.');
            // this.setExchangedRoomRates(meta);

            _.each(this.confirmation_items, (confirmation_item, index) => {
                // console.debug('... _.each()', JSON.stringify(confirmation_item), index, JSON.stringify(meta));
                this.refreshMessages(confirmation_item, index, meta);
            });
        },

        /**
         * Sets a warning message if dates don't pass validation
         * @param {Object}   confirmation_item     Confirmation Item object
         * @param {Integer}  index                 Index of Confirmation Item on array
         * @param {Object}   meta                  Race Hotel object
         */
        setDatesMessage (confirmation_item, index, meta) {
            // console.debug('setDatesMessage() fired.');
            this.$set(confirmation_item, 'date_warning', false);
            this.$set(confirmation_item, 'date_warning_message', '');
            this.$set(confirmation_item, 'date_warning_class', '');

            if (this.isCheckOutBeforeCheckIn(confirmation_item)) {
                this.$set(confirmation_item, 'date_warning', true);
                this.$set(confirmation_item, 'date_warning_message', 'Check-out is before check-in date');
                this.$set(confirmation_item, 'date_warning_class', 'text-danger');
                return;
            }

            if (this.isOutsideMinNightDates(confirmation_item, meta)) {
                this.$set(confirmation_item, 'date_warning', true);
                this.$set(confirmation_item, 'date_warning_message', 'Outside min night range');
                this.$set(confirmation_item, 'date_warning_class', 'text-danger orange-color');
                return;
            }

            if (this.isCrossingMinNightDates(confirmation_item, meta)) {
                this.$set(confirmation_item, 'date_warning', true);
                this.$set(confirmation_item, 'date_warning_message', 'Dates crossing min night range');
                this.$set(confirmation_item, 'date_warning_class', 'text-danger');
                return;
            }

            if (this.isSameDates(confirmation_item)) {
                this.$set(confirmation_item, 'date_warning', true);
                this.$set(confirmation_item, 'date_warning_message', 'Dates are the same');
                this.$set(confirmation_item, 'date_warning_class', 'text-danger');
                return;
            }
        },

        //////////////// Payment specific methods for Confirmations

        /**
         * Returns true if the total payment value = total confirmation value
         *
         * @return {Boolean}
         */
        paymentsEqualConfirmationCharges () {
            return (this.totalPaymentsAmountDue() === this.totalConfirmationCharges());
        },

        /**
         * Returns the total remaining balance (diff between amount owed
         * and payment schedule amount already showing)
         *
         * @return {Float}
         */
        totalConfirmationPaymentRemainingBalance () {
            return this.totalConfirmationCharges() - this.totalPaymentsAmountDue();
        },

        /**
         * Sets the payments property based on Confirmation payments
         *
         * IMPORTANT: Requires the 'payment' mixin
         *
         * @return {void}
         */
        setConfirmationPayments () {
            this.payments = this.confirmation.payments;
        },

        /**
         * Sets the `rate` property default to suggested rate
         * on the Confirmation Item
         *
         * @param  {Object}  confirmation_item Confirmation Item object
         * @param  {Integer} index             Index of the Confirmation Item object on `this.confirmation_items`
         * @param  {Object}  meta              Race Hotel object
         *
         * @return void
         */
        setRateDefaultToSuggestedRate (confirmation_item, index, meta) {
            let rate = 0;

            if (this.isWithinMinDates(confirmation_item.check_in, confirmation_item.check_out, meta)) {
                rate = confirmation_item.room.min_night_client_rate_exchanged;
            } else {
                rate = confirmation_item.room.pre_post_night_client_rate_exchanged;
            }

            // format Money without symbol
            let symbol = '-';

            // For edit confirmation
            this.setPropertiesForEditConfirmation(confirmation_item, rate, index, symbol);

            // For both add and edit confirmation
            this.setPropertiesForAddEditConfirmation(confirmation_item, rate, index, symbol);
        },

        /**
         * Sets the `rate_changed` property on the confirmation items
         *
         * @param  {Object}  confirmation_item Confirmation Item object
         * @param  {Integer} index   Index of the Confirmation Item object on `this.confirmation_items`
         *
         * @return void
         */
        checkRateChanged (confirmation_item, index) {
            if ((! isNaN(confirmation_item.rate) && ! isNaN(confirmation_item.suggested_rate_plain)
                && confirmation_item.rate != confirmation_item.suggested_rate_plain)) {
                confirmation_item.rate_changed = true;
                this.$set(this.confirmation_items, index, confirmation_item);
            }
        },

        /**
         * Sets the `rate_changed` & `suggested_rate_plain` property on the confirmation items
         * for edit confirmation only
         *
         * @param  {Object}  confirmation_item Confirmation Item object
         * @param  {Float} rate   rate of the Confirmation Item object on `this.confirmation_items`
         * @param  {Integer} index Index of the Confirmation Item object on `this.confirmation_items`
         * @param  {String}  symbol  Format Money without symbol i.e symbol = '-'
         *
         * @return void
         */
        setPropertiesForEditConfirmation (confirmation_item, rate, index, symbol) {
            if (confirmation_item.id != undefined && confirmation_item.id != '') {
                confirmation_item.suggested_rate_plain = this.formattedMoney(confirmation_item.rate,symbol);
                confirmation_item.rate_changed = true;
                this.$set(this.confirmation_items, index, confirmation_item);
            }
        },

        /**
         * Sets the `rate` & `rate_changed` & `suggested_rate_plain` property on the confirmation items
         * for edit confirmation only
         *
         * @param  {Object}  confirmation_item Confirmation Item object
         * @param  {Float} rate    rate of the Confirmation Item object on `this.confirmation_items`
         * @param  {Integer} index Index of the Confirmation Item object on `this.confirmation_items`
         * @param  {String}  symbol  Format Money without symbol i.e symbol = '-'
         *
         * @return void
         */
        setPropertiesForAddEditConfirmation (confirmation_item, rate, index, symbol) {
            if ((rate != 0) && (rate != '0.00') && (! isNaN(rate)) && (confirmation_item.rate_changed != true)) {
                confirmation_item.rate = this.formattedMoney(rate,symbol);
                confirmation_item.suggested_rate_plain = this.formattedMoney(rate,symbol);
                confirmation_item.rate_changed = false;
                this.$set(this.confirmation_items, index, confirmation_item);
            } else if ((rate != 0) && (rate != '0.00') && (! isNaN(rate))
                && (confirmation_item.rate_changed == true)
                && (confirmation_item.rate == confirmation_item.suggested_rate_plain)) {
                confirmation_item.rate = this.formattedMoney(rate,symbol);
                confirmation_item.suggested_rate_plain = this.formattedMoney(rate,symbol);
                confirmation_item.rate_changed = false;
                this.$set(this.confirmation_items, index, confirmation_item);
            }
        },
    }
};
