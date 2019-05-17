Vue.component('hotels-search', {
    props: ['user', 'raceId'],

    data () {
        return {
            query: '',
            results: []
        }
    },

    methods: {
        autoComplete() {
            this.results = [];

            if (this.query.length > 2) {
                axios.get('/api/hotels/search', {
                    params: {
                        query: this.query
                    }
                }).then(response => {
                    this.results = response.data;
                });
            }
        },

        attachHotelToRaceUrl(hotelId) {
            return `/races/${this.raceId}/hotels/${hotelId}/attach`;
        }
    }
});
