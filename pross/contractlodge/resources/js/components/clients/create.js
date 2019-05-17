import DatePick from '../vendor/vue-date-pick/vueDatePick';
import '../vendor/vue-date-pick/vueDatePick.css';

Vue.component('clients-create', {
    props: ['user'],
    mixins: [

        require('../../mixins/money'),
        require('../../mixins/contact')

    ],
    components: { DatePick },

    data() {
        return {
            name: '',
            address: '',
            city: '',
            region: '',
            postal_code: '',
            country_id: '',
            phone: '',
            email: '',
            website: '',
            code: '',
            form: new SparkForm({
                contacts: []
            }),
        }
    },

    methods: {
        create () {
            this.form.name = this.name;
            this.form.address = this.address;
            this.form.city = this.city;
            this.form.region = this.region;
            this.form.postal_code = this.postal_code;
            this.form.country_id = this.country_id;
            this.form.phone = this.phone;
            this.form.email = this.email;
            this.form.website = this.website;
            this.form.code = this.code;
            this.form.created_by = this.user.id;
            Spark.post(`/api/clients/create`, this.form)
                .then((response) => {
                    let message = 'The client has been saved.';
                    location.href = `/clients?message=${message}&level=success`;
                }).catch((errors) => {
                    return;
                });
        },

    },

    mounted () {
    }
});
