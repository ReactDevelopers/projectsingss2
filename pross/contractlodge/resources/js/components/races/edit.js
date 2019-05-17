import DatePick from '../vendor/vue-date-pick/vueDatePick';
import '../vendor/vue-date-pick/vueDatePick.css';

Vue.component('races-edit', {
    components: { DatePick },
    props: ['user', 'startDate', 'endDate'],

    data() {
        return {
            start_on: this.startDate,
            end_on: this.endDate,
        };
    }

});
