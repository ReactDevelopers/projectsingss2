import DatePick from '../vendor/vue-date-pick/vueDatePick';
import '../vendor/vue-date-pick/vueDatePick.css';

// FIXME: Needs doc blocks on methods and computed functions

Vue.component('hotels-edit', {
    components: { DatePick },
    props: ['user', 'raceHotelId', 'raceId', 'hotelId', 'inventoryCheckIn' , 'inventoryCheckOut', 'hotel', 'contacts', 'raceStartDate' , 'raceEndDate'],
    mixins: [require('../../mixins/money'), require('../../mixins/contact'), require('../../mixins/uploads')],

    data() {
        return {
            inventory_currency_id: '',
            inventory_min_check_in: this.inventoryCheckIn,
            inventory_min_check_out: this.inventoryCheckOut,
            inventory_notes: '',
            room_name: '',
            min_night_hotel_rate: '',
            min_night_client_rate: '',
            min_stays_contracted: '',
            pre_post_night_hotel_rate: '',
            pre_post_night_client_rate: '',
            pre_post_nights_contracted: '',
            inventory_rows: [],
            show_success_message: false,
            form: new SparkForm({
                ...this.hotel,
                contacts: this.contacts
            }),
            race_hotel_id: '',
        }
    },

    mounted() {
        this.fetchCurrencies();
        this.fetchInventory();
        this.fetchUploads();
    },

    methods: {

        fetchInventory() {
            if (this.raceId !== '0') {
                axios.get(`/api/races/${this.raceId}/hotels/${this.hotelId}`)
                    .then((response) => {
                        this.inventory_currency_id = response.data.meta.inventory_currency_id;
                        this.inventory_min_check_in = this.setFetchInventoryMin(response,'check_in');
                        this.inventory_min_check_out = this.setFetchInventoryMin(response,'check_out');
                        this.inventory_notes = response.data.meta.inventory_notes;
                        this.inventory_rows = response.data.meta.room_type_inventories;
                    });
            }
        },

        addRow() {
            this.inventory_rows.push({
                room_name: '',
                min_night_hotel_rate: '',
                min_night_client_rate: '',
                min_stays_contracted: '',
                pre_post_night_hotel_rate: '',
                pre_post_night_client_rate: '',
                pre_post_nights_contracted: ''
            });
        },

        deleteRow(index) {
            this.inventory_rows.splice(index,1);
        },

        update() {
            if (this.raceId) {
                this.form.race_id = this.raceId;
            }

            this.form.race_hotel_id = this.raceHotelId;
            this.form.inventory_currency_id = this.inventory_currency_id;
            this.form.hotel_id = this.hotelId;
            this.form.inventory_min_check_in = this.inventory_min_check_in;
            this.form.inventory_min_check_out = this.inventory_min_check_out;
            this.form.inventory_notes = this.inventory_notes;
            this.form.inventory_rows = this.inventory_rows;

            if (this.raceId) {

                Spark.post(`/api/races/${this.raceId}/hotels/${this.hotelId}`, this.form)
                    .then((response) => {
                        let message = 'Room Types and Rates have been saved.';
                        location.href = `/races/${this.raceId}/hotels/${this.hotelId}?message=${message}&level=success`;
                });
            } else {
                Spark.put(`/hotels/${this.hotelId}`, this.form)
                    .then((response) => {
                        let message = 'The hotel has been saved.';
                        location.href = response.url;
                });
            }
        },

        setFetchInventoryMin (response,field) {
            let inventory_min_check_in = response.data.meta.inventory_min_check_in ? moment(response.data.meta.inventory_min_check_in).format("YYYY-MM-DD") : null;
            let inventory_min_check_out = response.data.meta.inventory_min_check_out ? moment(response.data.meta.inventory_min_check_out).format("YYYY-MM-DD") : null;
            let race_start_date = this.raceStartDate ? moment(this.raceStartDate).format("YYYY-MM-DD"): null;
            let race_end_date = this.raceEndDate ? moment(this.raceEndDate).format("YYYY-MM-DD"): null;

            if (field == 'check_in') {
                return inventory_min_check_in ? inventory_min_check_in : race_start_date;
            } else {
                return inventory_min_check_out ? inventory_min_check_out : race_end_date;
            }
        },
    },

    computed: {
        chosen_currency_symbol () {
            let currency = _.find(this.currencies, {'id': parseInt(this.inventory_currency_id, 10)});

            if (_.isUndefined(currency)) {
                return '';
            }

            return currency.symbol;
        },

        inventory_min_check_in_formatted () {
            return moment(this.inventory_min_check_in).format("ddd, MMM D, YYYY");
        },

        inventory_min_check_out_formatted () {
            return moment(this.inventory_min_check_out).format("ddd, MMM D, YYYY");
        },

        inventory_number_of_nights () {
            let min_check_in = moment(this.inventory_min_check_in);
            let min_check_out = moment(this.inventory_min_check_out);
            return min_check_out.diff(min_check_in, 'days');
        },

        total_min_per_night_hotel () {
            return _.sumBy(this.inventory_rows, function (row) {
                if (isFinite(row.min_night_hotel_rate) && ! _.isEmpty(row.min_night_hotel_rate)) {
                    return parseFloat(parseFloat(row.min_night_hotel_rate) * parseFloat(row.min_stays_contracted) * parseFloat(this.inventory_number_of_nights));
                }
                return 0;
            }.bind(this));
        },

        total_min_per_night_client () {
            return _.sumBy(this.inventory_rows, function (row) {
                if (isFinite(row.min_night_client_rate) && ! _.isEmpty(row.min_night_client_rate)) {
                    return parseFloat(parseFloat(row.min_night_client_rate) * parseFloat(row.min_stays_contracted) * parseFloat(this.inventory_number_of_nights));
                }
                return 0;
            }.bind(this));
        },

        total_min_stays_contracted () {
            return _.sumBy(this.inventory_rows, function (row) {
                if (_.trim(row.min_stays_contracted) === '') {
                    return 0;
                }
                if (isNaN(row.min_stays_contracted)) {
                    return 0;
                }
                return parseInt(row.min_stays_contracted, 10);
            });
        },

        total_pre_post_per_night_hotel () {
            return _.sumBy(this.inventory_rows, function (row) {
                if (isFinite(row.pre_post_night_hotel_rate) && ! _.isEmpty(row.pre_post_night_hotel_rate)) {
                    return parseFloat(parseFloat(row.pre_post_night_hotel_rate) * parseFloat(row.pre_post_nights_contracted));
                }
                return 0;
            });
        },

        total_pre_post_per_night_client () {
            return _.sumBy(this.inventory_rows, function (row) {
                if (isFinite(row.pre_post_night_client_rate) && ! _.isEmpty(row.pre_post_night_client_rate)) {
                    return parseFloat(parseFloat(row.pre_post_night_client_rate) * parseFloat(row.pre_post_nights_contracted));
                }
                return 0;
            });
        },

        total_pre_post_nights_contracted () {
            return _.sumBy(this.inventory_rows, function (row) {
                if (_.trim(row.pre_post_nights_contracted) === '') {
                    return 0;
                }
                if (isNaN(row.pre_post_nights_contracted)) {
                    return 0;
                }
                return parseInt(row.pre_post_nights_contracted, 10);
            });
        },
    }
});
