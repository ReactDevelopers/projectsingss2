import DatePick from 'vue-date-pick';
import 'vue-date-pick/dist/vueDatePick.css';

Vue.component('bills-edit', {
    components: { DatePick },
    props: ['user', 'raceId', 'hotelId', 'raceHotelId', 'inventoryCurrencyId', 'billId'],
    mixins: [require('../../mixins/money')],

    data() {
        return {
            bill: {},
            currencies: [],
            currency: {},
            exchange_currency: {},
            payments: [],
            form: new SparkForm({}),
            contract_signed_on: '',
            exchange_rate_amount: '',
        }
    },

    mounted() {
        this.fetchCurrencies(this.currencies);
        this.addPaymentRow();

        if (this.billId) {
            this.fetchBill().then(() => {
                this.fetchExchangeRate();
            });
        }
    },

    methods: {
        addPaymentRow () {
            this.payments.push({
                payment_name: '',
                amount_due: '',
                due_on: '',
                amount_paid: '',
                paid_on: '',
                to_accounts_on: '',
                invoice_number: '',
                invoice_date: ''
            });
        },

        deletePaymentRow (index) {
            this.payments.splice(index, 1);
        },

        totalPaymentDueMoney () {
            let money = _.reduce(this.payments, function (sum, item) {
                if (isFinite(item.amount_due) && ! _.isEmpty(item.amount_due)) {
                    return sum + parseFloat(item.amount_due);
                }
                return sum;
            }, 0);
            return money;
        },

        totalPaymentPaidMoney () {
            let money = _.reduce(this.payments, function (sum, item) {
                if (isFinite(item.amount_paid) && ! _.isEmpty(item.amount_paid)) {
                    return sum + parseFloat(item.amount_paid);
                }
                return sum;
            }, 0);
            return money;
        },

        paidEqualsDue () {
            return (this.totalPaymentDueMoney() === this.totalPaymentPaidMoney());
        },

        update () {
            this.form.race_hotel_id = this.raceHotelId;
            this.form.contract_signed_on = this.contract_signed_on;
            this.form.currency_id = this.currency.id;
            this.form.exchange_currency_id = this.exchange_currency.id;
            this.form.created_by = this.user.id;
            this.form.payments = this.payments;

            Spark.post(`/api/races/${this.raceId}/hotels/${this.hotelId}/bills`, this.form)
                .then((response) => {
                    let message = 'Payment Schedules have been saved.';
                    location.href = `/races/${this.raceId}/hotels/${this.hotelId}?message=${message}&level=success`;
            });
        },

        fetchBill () {
            return axios.get(`/api/bills/${this.billId}`).then((response) => {
                this.bill = response.data;
                this.race_hotel_id = this.bill.race_hotel_id;
                this.contract_signed_on = this.bill.contract_signed_on;
                this.payments = response.data.payments;
                this.currency = _.find(this.currencies, { 'id': this.bill.currency_id });
                this.exchange_currency = _.find(this.currencies, { 'id': this.bill.exchange_currency_id });
            });
        },

       fetchExchangeRate () {
            if (typeof this.currency === 'undefined' || typeof this.exchange_currency === 'undefined') {
                this.exchange_rate_amount = 1;
                return;
            }

            return axios.get(`/api/currencies/exchange/${this.currency.name}/${this.exchange_currency.name}`).then((response) => {
                this.exchange_rate_amount = response.data;
            });
        },

        getExchangeAmountDue (amount) {
            if (
                this.exchange_currency === undefined
                || this.exchange_currency === 0
                || this.exchange_currency === ''
                || this.exchange_rate_amount === undefined
                || this.exchange_rate_amount === 0
                || this.exchange_rate_amount === ''
            ) {
                return '';
            }

            let exchange_amount = parseFloat(amount) * parseFloat(this.exchange_rate_amount);
            let ex = parseFloat(exchange_amount.toFixed(2));

            if (isNaN(ex)) {
                return '';
            }

            return this.formattedMoney(ex, this.exchange_currency.symbol);
        },
    }
});
