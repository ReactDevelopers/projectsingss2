import DatePick from '../vendor/vue-date-pick/vueDatePick';
import '../vendor/vue-date-pick/vueDatePick.css';

Vue.component('invoices-edit', {
    props: [
        'user',
        'raceId',
        'hotelId',
        'raceHotelId',
        'inventoryCurrencyId',
        'clientId',
        'invoiceType',
        'customInvoiceId',
        'confirmationId',
        'invoiceId',
    ],
    mixins: [
        require('../../mixins/money'),
        require('../../mixins/invoices'),
        require('../../mixins/payments'),
        require('../../mixins/uploads'),
        require('../../mixins/clients/autosuggest'),
        require('../../mixins/invoices/confirmations'),
        require('../../mixins/invoices/custom_invoices'),
    ],
    components: { DatePick },

    data() {
        return {
            raceIdData: this.raceId,
            hotelIdData: this.hotelId,
            raceHotelIdData: this.raceHotelId,
        }
    },

    mounted () {
        this.invoice_type = this.invoiceType; // requires invoices mixin

        this.fetchCurrencies().then(() => {
            this.setCurrencies();
            this.fetchRaceHotel().then(() => { // gets this.meta
                this.fetchInvoice().then(() => {
                    this.setExchangedRates();
                });

                this.fetchRace();
                this.fetchHotel();
                this.fetchClient();

                this.fetchUploads();
            });
        });
    }
});
