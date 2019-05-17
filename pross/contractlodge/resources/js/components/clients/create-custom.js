import DatePick from '../vendor/vue-date-pick/vueDatePick';
import '../vendor/vue-date-pick/vueDatePick.css';

Vue.component('clients-create-custom', {
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
            client: {},
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
                    this.$el.querySelector('.alert-success').style.visibility = "visible";
                    this.$refs['triggerCancel'].click();
                    this.$emit('client', response);
                }).catch((errors) => {
                    this.$el.querySelector('.alert-success').style.visibility = "hidden";
                    return;
                });
        },
    },

    mounted () {
        this.$el.querySelector('.alert-success').style.visibility = "hidden";
    }
});
