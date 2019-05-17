Vue.component('hotels-create', {
    props: ['user', 'action'],
    mixins:[require('../../mixins/contact')],

    data: function () {
        return {
            form: new SparkForm({
                name: null,
                address: null,
                city: null,
                region: null,
                country_id: null,
                postal_code: null,
                website: null,
                code: null,
                notes: null,
                email: null,
                phone: null,
                contacts: []
            }),

        }
    },

    mounted() {
        //
    },

    methods: {

        register: function () {

            Spark.post(this.action, this.form)
            .then(response => {

                location.href = response.url;

            }).catch(error => {

            })
        }
    }
});
