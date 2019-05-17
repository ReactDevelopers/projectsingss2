module.exports = {
    data() {
        return {
            invoice_type: '', // 'confirmations' or 'extras'
            hotel: {},
            race: {},
            meta: {},
            due_on: '',
            additional_notes: '',
            form: new SparkForm({}),
        }
    },

    methods: {
        /**
         * Retrieves the Confirmation or Custom Invoice object for the page
         *
         * @return {Promise}
         */
        fetchInvoice () {
            // console.debug('fetchInvoice() fired.');
            if (this.isEditingConfirmation()) {
                // console.debug('... isEditingConfirmation', this.isEditingConfirmation());
                return this.fetchConfirmation(this.meta).then(() => {
                    // console.debug('fetchConfirmation().then() fired.');
                    this.setConfirmationPayments();
                });
            }

            if (this.isEditingCustomInvoice()) {
                return this.fetchCustomInvoice();
            }

            // Not editing. We're creating an invoice
            return new Promise((resolve, reject) => {
                resolve(false);
            });
        },

        /**
         * Wrapper to figure out if we're on an edit or create page.
         * Returns true if editing.
         *
         * @return {Boolean}
         */
        isEditingInvoice () {
            if (this.invoice_type == 'confirmations') {
                return this.isEditingConfirmation();
            }
            return this.isEditingCustomInvoice();
        },

        /**
         * Returns true if we have a confirmationId (thus are editing one)
         *
         * @return {Boolean}
         */
        isEditingConfirmation () {
            return (typeof this.confirmationId !== 'undefined' && this.confirmationId !== 0);
        },

        /**
         * Returns true if we have a customInvoiceId (thus are editing one)
         *
         * @return {Boolean}
         */
        isEditingCustomInvoice () {
            return (typeof this.customInvoiceId !== 'undefined' && this.customInvoiceId !== 0);
        },

        /**
         * Sets the exchange rates for the invoice
         *
         * @return {Promise}
         */
        setExchangedRates () {
            // console.debug('setExchangedRates() fired.');
            if (this.invoice_type == 'confirmations') {
                return this.setExchangedRoomRates(this.meta);
            }
            return this.setExchangedCustomInvoiceRates();
        },

        /**
         * Retrieves the Race Hotel (`meta`) object
         *
         * @return {Promise | void}
         */
        fetchRaceHotel () {
            if (typeof this.raceHotelId == 'undefined' && (typeof this.raceId == 'undefined' || typeof this.hotelId == 'undefined')) {
                return;
            }
            return axios.get(`/api/races/${this.raceIdData}/hotels/${this.hotelIdData}`).then((response) => {
                this.meta = response.data.meta;
                if ((typeof this.raceHotelIdData != "undefined") && (typeof response.data.meta != "undefined") && (response.data.meta !== null)) {
                    this.raceHotelIdData = response.data.meta.id;
                }
            });
        },

        /**
         * Retrieves the race data
         *
         * @return {Promise | void}
         */
        fetchRace () {
            if (typeof this.raceId == 'undefined') {
                return;
            }

            return axios.get(`/api/races/${this.raceId}`).then((response) => {
                this.race = response.data;
            });
        },

        /**
         * Retrieves the Hotel data
         *
         * @return {Promise | void}
         */
        fetchHotel () {
            if (typeof this.hotelId == 'undefined') {
                return;
            }

            return axios.get(`/api/hotels/${this.hotelId}`).then((response) => {
                this.hotel = response.data;
            });
        },

        /**
         * Format the date using moment()
         *
         * @param  {mixed}  d
         * @param  {String} format What to format the date to look like
         *
         * @return {Moment}
         */
        formatDate (d, format = 'YYYY-MM-DD') {
            if (typeof d == undefined || d == null) {
                return;
            }
            return moment(d).format(format)
        },

        /**
         * Format date to our "friendly" version
         *
         * @param  {mixed}  date
         *
         * @return {moment}
         */
        friendlyDate (date) {
            return this.formatDate(date, 'ddd, MMM DD, YYYY');
        },

        /**
         * Format date as an abbreviated weekday
         *
         * @param  {mixed} date
         *
         * @return {moment | ''}
         */
        abbreviatedWeekday (date) {
            let formatted = this.formatDate(date, 'ddd');

            if (formatted == 'Invalid date') {
                return '';
            }

            return formatted;
        },
    }
};
