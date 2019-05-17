module.exports = {
    data() {
        return {
            query: '',
            results: [],
            client: {},
        }
    },

    methods: {
        /**
         * Retrieves the client data
         *
         * @return {Promise | void}
         */
        fetchClient () {
            if (typeof this.clientId == 'undefined') {
                console.error('fetchClient() requires a `clientId` property to work.');
                return;
            }

            return axios.get(`/api/clients/${this.clientId}`).then((response) => {
                this.client = response.data;
            });
        },

        /**
         * Resets the `client` property to empty object
         *
         * @return {void}
         */
        destroyClient () {
            this.client = {};
        },

        /**
         * Sets the `client` property to the given client and resets
         * the search query and results.
         *
         * @param {Object} client
         *
         * @return {void}
         */
        setClient (client) {
            this.client = client;
            this.resetSearch();
        },

        /**
         * Resets the search `query` and `results` properties
         *
         * @return {void}
         */
        resetSearch () {
            this.query = '';
            this.results = [];
        },

        /**
         * Sets the `results` proprty using API response using given `query` property
         *
         * @return {void}
         */
        autoComplete () {
            this.results = [];

            if (this.query.length > 2) {
                axios.get('/api/clients/search', {
                    params: {
                        query: this.query
                    }
                }).then(response => {
                    this.results = response.data;
                });
            }
        },
    }
};
