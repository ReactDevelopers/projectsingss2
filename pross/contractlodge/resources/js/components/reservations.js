import DatePick from 'vue-date-pick';
import 'vue-date-pick/dist/vueDatePick.css';

Vue.component('reservations', {
    props: ['user', 'raceId', 'hotelId'],
    mixins: [
        require('../mixins/money'),
        require('../mixins/partials/sorttable')
    ],
    components: { DatePick },

    data() {
        return {
            list_sent_on: '',
            list_confirmed_on: '',
            list_notes: '',
            form: new SparkForm({})
        }
    },

    methods: {
        fetchRoomingList() {
            if (this.raceId !== '0') {
                axios.get(`/api/races/${this.raceId}/hotels/${this.hotelId}/rooming-list-data`)
                    .then((response) => {
                        this.list_sent_on = response.data.meta.rooming_list_sent;
                        this.list_confirmed_on = response.data.meta.rooming_list_confirmed;
                        this.list_notes = response.data.meta.rooming_list_notes;
                        this.inventory_min_check_in = response.data.meta.inventory_min_check_in;
                        this.inventory_min_check_out = response.data.meta.inventory_min_check_out;
                        this.inventory_currency_id = response.data.meta.inventory_currency_id;
                    });
            }
        },

        update() {

            this.form.race_id = this.raceId;
            this.form.hotel_id = this.hotelId;
            this.form.rooming_list_sent = this.list_sent_on;
            this.form.rooming_list_confirmed = this.list_confirmed_on;
            this.form.rooming_list_notes = this.list_notes;
            this.form.inventory_min_check_in = this.inventory_min_check_in;
            this.form.inventory_min_check_out = this.inventory_min_check_out;
            this.form.inventory_currency_id = this.inventory_currency_id;
            this.form.reservation_check = 1;

            Spark.post(`/api/races/${this.raceId}/hotels/${this.hotelId}`, this.form)
                .then((response) => {
                    let message = 'Rooming List have been saved.';
                    location.href = `/races/${this.raceId}/hotels/${this.hotelId}/reservations?message=${message}&level=success`;
            });

        },
    },

    mounted () {
        this.fetchRoomingList();
    }
});
