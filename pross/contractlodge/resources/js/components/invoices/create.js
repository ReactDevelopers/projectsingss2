import DatePick from '../vendor/vue-date-pick/vueDatePick';
import '../vendor/vue-date-pick/vueDatePick.css';

Vue.component('invoices-create', {
    props: [
        'user',
        'raceId',
        'hotelId',
        'raceHotelId',
        'inventoryCurrencyId',
        'clientId',
        'invoiceType',
        'raceStartDate'
    ],
    mixins: [
        require('../../mixins/money'),
        require('../../mixins/invoices'),
        require('../../mixins/partials/autosuggest'),
        require('../../mixins/invoices/confirmations'),
        require('../../mixins/invoices/custom_invoices'),
        require('../../mixins/clients/autosuggest'),
        require('../../mixins/payments'),
    ],
    components: { DatePick },

    data() {
        return {
            raceIdData: this.raceId,
            hotelIdData: this.hotelId,
            raceHotelIdData: this.raceHotelId,
        }
    },

    methods: {
        /**
         * Call the appropriate invoice creation method based on the invoice_type given
         *
         * @return {Promise | void}
         */
        createInvoice () {
            if (typeof this.invoice_type == undefined || this.invoice_type == '') {
                console.error('createInvoice() requires invoice_type to work properly.');
                return;
            }

            if (this.invoice_type == 'confirmations') {
                return this.createConfirmation();
            }

            if (this.invoice_type == 'extras') {
                return this.createCustomInvoice();
            }
        },

        /**
         * To Generate the payment row if there is any change in confirmation amount.
         * @return {void}
         */
        generateDefaultPaymentRow () {
            this.paymentDefaultRows();
        },

        /**
         * Add a preliminary row to the invoice
         *
         * @return {void}
         */
        addRow () {
            if (typeof this.invoice_type == undefined || this.invoice_type == '') {
                console.error('addRow() requires invoice_type to work properly.');
                return;
            }

            if (this.invoice_type == 'confirmations') {
                this.addRoomRow();
            }

            if (this.invoice_type == 'extras') {
                this.addInvoiceRow();
            }
        },

        setClient (value) {
            this.client = value;
        },
    },

    mounted () {
        this.invoice_type = this.invoiceType; // requires invoices mixin

        this.fetchCurrencies().then(() => {
            this.setCurrencies();
            this.fetchRaceHotel().then(() => {
                this.setExchangedRates();
                this.fetchHotel();
                this.fetchClient();
                this.addRow();
                this.addPaymentRow();
            });

            if (typeof this.raceId != 'undefined') {
                this.fetchRace();
            }
        });
    }
});
