module.exports = {
    data() {
        return {
            payments: [],
            blank_payment: {
                payment_name: '',
                amount_due: '',
                due_on: '',
                amount_paid: '',
                paid_on: '',
                to_accounts_on: '',
                invoice_number: '',
                invoice_date: ''
            },
        }
    },

    methods: {
        /**
         * Format a payment's dates
         *
         * @param  {Object} payment Payment
         *
         * @return {Object}         Payment
         */
        formatPaymentDates (payment) {
            if (payment.hasOwnProperty('due_on') && payment.due_on !== null) {
                payment.due_on = this.formatDate(payment.due_on);
            }

            if (payment.hasOwnProperty('paid_on') && payment.paid_on !== null) {
                payment.paid_on = this.formatDate(payment.paid_on);
            }

            return payment;
        },

        /**
         * Adds a new payment object to the array of `payments`
         *
         * @return {void}
         */
        addPaymentRow () {
            this.payments.push(JSON.parse(JSON.stringify(this.blank_payment)));
        },

        /**
         * To add the payment default row base on the Race start date.
         * @return {void}
         */
        paymentDefaultRows() {

            let race_start_date = moment(this.race.start_on);
            let current_date = moment();
            let num_days = race_start_date.diff(current_date, 'days');
            let total_confirmation_charge = this.totalConfirmationCharges();

            if (total_confirmation_charge <= 0) {
                return;
            }

            if (num_days > 120) {
                let due_amount = total_confirmation_charge / 2;
                this.payments = [
                    {
                        payment_name: 'Deposit 1',
                        amount_due: due_amount,
                        due_on: moment().format('YYYY-MM-DD'),
                        amount_paid: '',
                        paid_on: '',
                    },
                    {
                        payment_name: 'Deposit 2',
                        amount_due: due_amount,
                        due_on: moment(this.race.start_on).add(4, 'months').format('YYYY-MM-DD'),
                        amount_paid: '',
                        paid_on: '',
                    }
                ]
                return;
            }

            this.payments = [
                {
                    payment_name: 'Full',
                    amount_due: total_confirmation_charge,
                    due_on: moment().format('YYYY-MM-DD'),
                    amount_paid: '',
                    paid_on: '',
                }
            ];
        },

        /**
         * Removes a payment object from the array of `payments` via specified index
         *
         * @param  {Integer} index Index of Payment object in array
         *
         * @return {void}
         */
        deletePaymentRow (index) {
            this.payments.splice(index, 1);
        },

        /**
         * Returns true if the total due value = total paid value
         *
         * @return {Boolean}
         */
        isAmountDueEqualToAmountPaid () {
            return (this.totalPaymentsAmountDue() === this.totalPaymentsAmountPaid());
        },

        /**
         * Returns the total amount included in payments
         *
         * @return {Float}
         */
        totalPaymentsAmountDue () {
            return this.getTotalPaymentsAmount('due');
        },

        /**
         * Returns total value of payment amounts paid
         *
         * @return {Float}
         */
        totalPaymentsAmountPaid () {
            return this.getTotalPaymentsAmount('paid');
        },

        /**
         * Gets the total 'due' or 'paid' payments amount depending on parameter
         *
         * @param  {String} duePaid 'due' or 'paid'
         *
         * @return {Float}
         */
        getTotalPaymentsAmount (duePaid = 'due') {

            return _.reduce(this.payments, function (sum, item) {
                let field = item.amount_paid;

                if (duePaid == 'due') {
                    field = item.amount_due;
                }

                if (isFinite(field) && ! _.isEmpty(field)) {
                   field  = parseFloat(field);
                }

                return parseFloat(sum + field);
            }, 0);
        },

        /**
         * Returns the difference between the payments and the invoice charges
         *
         * @return {Float}
         */
        totalPaymentRemainingBalance () {
            if (this.invoice_type == 'confirmations') {
                return this.totalConfirmationPaymentRemainingBalance();
            }
            return this.totalCustomInvoicePaymentRemainingBalance();
        },

        /**
         * Returns true if the payments equal the charges (depending on `invoice_type`)
         *
         * IMPORTANT: This relies on the `invoices` mixin
         *
         * @return {Boolean}
         */
        isPaymentsEqualToCharges () {
            if (this.invoice_type == 'confirmations') {
                return this.paymentsEqualConfirmationCharges();
            }
            return this.paymentsEqualCustomInvoiceCharges();
        },

        /**
         * Call the appropriate invoice update method based on the invoice_type given.
         *
         * @return {Promise | void}
         */
        updateConfirmationInvoicePayment () {
            if (typeof this.invoice_type == undefined || this.invoice_type == '') {
                console.error('updateConfirmationInvoicePayment() requires invoice_type to work properly.');
                return;
            }

            if (this.invoice_type == 'confirmations') {
                return this.updateConfirmation();
            }

            if (this.invoice_type == 'extras') {
                return this.updateCustomInvoice();
            }
        }
    }
};
