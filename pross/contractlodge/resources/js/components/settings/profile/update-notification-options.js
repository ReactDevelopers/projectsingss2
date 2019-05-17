Vue.component('update-notification-options', {
    props: ['user'],

    data() {
        return {
            form: $.extend(true, new SparkForm({
                notify_of_hotel_payment_schedule: false
            }), Spark.forms.updateNotificationOptions)
        }
    },

    methods: {
        /**
         * Update the user's notification options.
         */
        update() {
            Spark.put('/settings/notification', this.form)
                .then(() => {
                    // Bus.$emit('updateNotificationOptions');
                });
        }
    },

    mounted() {
        this.form.notify_of_hotel_payment_schedule = this.user.notify_of_hotel_payment_schedule;
    }
});
