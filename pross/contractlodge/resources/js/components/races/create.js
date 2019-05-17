import DatePick from '../vendor/vue-date-pick/vueDatePick';
import '../vendor/vue-date-pick/vueDatePick.css';

Vue.component('races-create', {
    components: { DatePick },
    props: ['user', 'startDate', 'endDate'],

    data() {
        return {
            start_on: this.startDate,
            end_on: this.endDate
        };
    },

    methods: {
        setDefaultEndDate () {
            this.end_on = moment(this.start_on).add(1, 'days').format("YYYY-MM-DD");
        },
    },
});
