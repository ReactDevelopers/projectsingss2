import DatePick from '../vendor/vue-date-pick/vueDatePick';
import '../vendor/vue-date-pick/vueDatePick.css';

Vue.component('clients-edit', {
    props: ['user', 'clientId'],
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
            client: {},
        }
    },

    methods: {

        update () {
            this.form.client_id = this.clientId;
            this.form.name = this.name;
            this.form.address = this.address;
            this.form.city = this.city;
            this.form.region = this.region;
            this.form.postal_code = this.postal_code;
            this.form.country_id = this.country_id;
            this.form.phone = this.phone;
            this.form.email = this.email;
            this.form.website = this.website;
            this.form.created_by = this.user.id;
            this.form.code = this.code;

            Spark.put(`/api/clients/edit`, this.form)
            .then((response) => {
                let message = 'The client has been saved.';
                location.href = `/clients?message=${message}&level=success`;
            }).catch((errors) => {
                return;
            });
        },

        fetchClient (clientId) {
            return axios.get(`/api/clients/${this.clientId}`).then((response) => {
                this.client = response.data;
                this.name = this.client.name;
                this.address = this.client.address;
                this.city = this.client.city;
                this.region = this.client.region;
                this.postal_code = this.client.postal_code;
                this.country_id = this.client.country_id;
                this.phone = this.client.phone;
                this.email = this.client.email;
                this.website = this.client.website;
                this.code = this.client.code;
                this.form.contacts = this.client.contacts;
            });
        },

    },

    created () {
        this.fetchClient();
    }
});
