module.exports = {
    data() {
        return {
            invoice: {},
            invoice_items: [],
            blank_invoice_item: {
                date: '',
                description: '',
                quantity: '',
                rate: '',
                date_warning: false,
                message: '',
            },
        }
    },

    methods: {
        /**
         * Create a Custom Invoice using given form/property data
         *
         * @return {Promise}
         */
        createCustomInvoice () {
            this.form.due_on = this.due_on;
            this.form.client_id = this.client.id;
            this.form.race_id = this.race.id;
            this.form.hotel_id = this.hotel.id;
            this.form.race_hotel_id = this.raceHotelIdData;
            this.form.currency_id = this.currency.id;
            this.form.notes = this.additional_notes;
            this.form.created_by = this.user.id;
            this.form.invoice_items = this.invoice_items;
            this.form.payments = this.payments;
            this.form.invoice_type = 'extras';

            return Spark.post(`/api/races/${this.race.id}/hotels/${this.hotel.id}/invoices/extras`, this.form)
                .then((response) => {
                    let message = 'The Extra Invoice has been saved.';
                    location.href = `/races/${this.race.id}/hotels/${this.hotel.id}/clients/${this.client.id}/extras/${response.id}?message=${message}&level=success`;
                }).catch((errors) => {
                    return;
                });
        },

        /**
         * Update a Custom Invoice using given form/property data
         *
         * @return {Promise}
         */
        updateCustomInvoice () {
            this.form.due_on = this.due_on;
            this.form.client_id = this.client.id;
            this.form.race_id = this.race.id;
            this.form.hotel_id = this.hotel.id;
            this.form.race_hotel_id = this.raceHotelId;
            this.form.currency_id = this.currency.id;
            this.form.notes = this.additional_notes;
            this.form.created_by = this.user.id;
            this.form.invoice_items = this.invoice_items;
            this.form.payments = this.payments;
            this.form.invoice_type = 'extras';
            this.form.invoiceId = this.invoiceId;

            return Spark.put(`/api/races/${this.race.id}/hotels/${this.hotel.id}/invoices/extras/edit`, this.form)
                .then((response) => {
                    let message = 'The Extra Invoice has been saved.';
                    location.href = `/races/${this.race.id}/hotels/${this.hotel.id}?message=${message}&level=success`;
                }).catch((errors) => {
                    return;
                });
        },

        /**
         * Set the custom invoice rates using current exchange rate
         * IMPORTANT: The money mixin is a dependency for this method to work without error.
         *
         * @return {void}
         */
        setExchangedCustomInvoiceRates () {
            this.fetchExchangeRate().then(() => { // IMPORTANT: Depends on the `money` mixin.
                let ex = parseFloat(this.exchange_rate);

                return _.each(this.invoice_items, function (ii, index, array) {
                    // console.debug(JSON.stringify(ii));
                    ii.rate_exchanged = parseFloat(ii.rate) * ex;

                    this.$set(this.invoice_items, index, ii);
                }.bind(this));
            });
        },

        /**
         * Returns total value of invoice amounts owed
         *
         * @return {Float}
         */
        totalCustomInvoiceCharges () {
            let money = _.reduce(this.invoice_items, function (sum, invoice_item) {
                if (typeof this.totalForCustomInvoiceItem(invoice_item) == 'undefined') {
                    return sum;
                }
                return sum + parseFloat(this.totalForCustomInvoiceItem(invoice_item));
            }.bind(this), 0);

            return money;
        },

        /**
         * Returns total value of invoice amounts owed (exchanged)
         *
         * @return {Float}
         */
        totalExchangedCustomInvoiceCharges () {
            return parseFloat(this.totalCustomInvoiceCharges()) * this.exchange_rate;
        },

        /**
         * Returns row total for an invoice item
         * FIXME: Should move this to the mixins/invoices.js
         *
         * @param  {Object} invoice_item Invoice Item
         *
         * @return {Float | void}
         */
        totalForCustomInvoiceItem (invoice_item) {
            if (invoice_item.rate == '' || invoice_item.quantity == '') {
                return;
            }

            return parseFloat(invoice_item.rate) * invoice_item.quantity;
        },

        /**
         * Returns row total (x exchange rate) for an invoice item
         * FIXME: Should move this to the mixins/invoices.js
         *
         * @param  {Object} invoice_item Invoice Item
         *
         * @return {Float | void}
         */
        totalExchangedForCustomInvoiceItem (invoice_item) {
            if (invoice_item.rate == '' || invoice_item.quantity == '') {
                return;
            }

            return parseFloat(invoice_item.rate * this.exchange_rate) * invoice_item.quantity;
        },

        /**
         * Retrieves the custom invoice
         *
         * @return {Promise}
         */
        fetchCustomInvoice () {
            if (typeof this.customInvoiceId == undefined) {
                console.error('fetchCustomInvoice() requires a customInvoiceId to work properly.');
                return;
            }

            return axios.get(`/api/custom_invoices/${this.customInvoiceId}`).then(response => {
                // console.debug('fetchCustomInvoice() axios.get().then() ...', JSON.stringify(response.data.invoice_items));
                this.invoice = response.data;
                this.due_on = this.invoice.due_on;
                this.additional_notes = this.invoice.notes;
                this.invoice_items = response.data.invoice_items;
                this.payments = response.data.payments;
                this.currency = this.fetchCurrencyById(response.data.currency_id, this.fetchExchangeRate);
            });
        },

        /**
         * Adds a blank Invoice Item to the array of Invoice Items
         *
         * @return {void}
         */
        addInvoiceRow () {
            this.invoice_items.push(JSON.parse(JSON.stringify(this.blank_invoice_item)));
        },

        /**
         * Removes an Invoice item from it's array at the given index
         *
         * @param  {Integer} index Invoice Items index
         *
         * @return {void}
         */
        deleteInvoiceRow (index) {
            this.invoice_items.splice(index, 1);
        },

        //////////////// Payment specific methods for Custom Invoices

        /**
         * Returns true if the total payments due = total invoice value
         *
         * @return {Boolean}
         */
        paymentsEqualCustomInvoiceCharges () {
            return (this.totalPaymentsAmountDue() === this.totalCustomInvoiceCharges());
        },

        /**
         * Returns the total remaining balance (diff between amount owed
         * and payment schedule amount already showing)
         *
         * @return {Float}
         */
        totalCustomInvoicePaymentRemainingBalance () {
            return this.totalCustomInvoiceCharges() - this.totalPaymentsAmountDue();
        },
    }
};
