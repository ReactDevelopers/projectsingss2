module.exports = {
    data() {
        return {
            hotel: {},
            hotel_query: '',
            hotels: [],
            race: {},
            race_query: '',
            races: []
        }
    },

    methods: {
        /**
         *  Autocomplete search for hotels
         */
        autoCompleteHotel() {
            this.hotels = [];

            if (this.hotel_query.length > 2) {
                axios.get('/api/hotels/search', {
                    params: {
                        query: this.hotel_query
                    }
                }).then(response => {
                    this.hotels = response.data;
                });
            }
        },

        /**
         * Sets the `hotel` property to the given hotel and resets
         * the search query and results.
         *
         * @param {Object} hotel
         *
         * @return {void}
         */
        setHotel (hotel) {
            this.hotel = hotel;
            this.resetHotelSearch();
            this.hotelIdData = hotel.id;
            this.fetchRaceHotel();
        },

        /**
         * Resets the hotel search `hotel_query` and `hotels` properties
         *
         * @return {void}
         */
        resetHotelSearch () {
            this.hotel_query = '';
            this.hotels = [];
        },

        /**
         * Resets the `hotel` property to empty object
         *
         * @return {void}
         */
        destroyHotel () {
            this.hotel = {};
        },

        /**
         *  Autocomplete search for races
         */
        autoCompleteRace() {
            this.races = [];

            if (this.race_query.length > 2) {
                axios.get('/api/races/search', {
                    params: {
                        query: this.race_query
                    }
                }).then(response => {
                    this.races = response.data;
                });
            }
        },

        /**
         * Sets the `race` property to the given race and resets
         * the search query and results.
         *
         * @param {Object} race
         *
         * @return {void}
         */
        setRace (race) {
            this.race = race;
            this.resetRaceSearch();
            this.raceIdData = race.id;
            this.fetchRaceHotel();
        },

        /**
         * Resets the race search `race_query` and `races` properties
         *
         * @return {void}
         */
        resetRaceSearch () {
            this.race_query = '';
            this.races = [];
        },

        /**
         * Resets the `race` property to empty object
         *
         * @return {void}
         */
        destroyRace () {
            this.race = {};
        },

    }
};
