module.exports = {
    data() {
        return {
            currencies: [],
            currency: {},
            inventory_currency: {},
            exchange_rate: 0,
        }
    },

    methods: {
        /**
         * Ref: https://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-dollars-currency-string-in-javascript
         *
         * @param  {Number} amount       Amount to format
         * @param  {String} symbol       Money symbol
         * @param  {Number} decimalCount Number of digits after the decimal
         * @param  {String} decimal      Decimal separator
         * @param  {String} thousands    Thousands separator
         *
         * @return {string}
         */
        formattedMoney(amount, symbol = '$', decimalCount = 2, decimal = ".", thousands = ",") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

                const negativeSign = amount < 0 ? "-" : "";

                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;

                // format Money without symbol by passing argument symbol = '-'
                if (symbol == '-') {
                    symbol = '';
                }

                return negativeSign + symbol + (j ? i.substr(0, j) + thousands : '')
                    + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands)
                    + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {
                console.log(e);
            }
        },

        /**
         * Fetches currencies from the API and sets the `currencies` property
         *
         * @return {Promise}
         */
        fetchCurrencies () {
            // console.error('fetchCurrencies() fired.', JSON.stringify(this.currencies));
            if (this.currencies.length > 0) {
                return new Promise((resolve, reject) => {
                    resolve(this.currencies);
                });
            }

            return axios.get(`/api/currencies`).then(response => {
                // console.debug('axios.get().then() ...', JSON.stringify(response.data));
                return this.currencies = response.data;
            });
        },

        /**
         * Return the currency, if available, from the array of currencies already retrieved.
         *
         * @param  {integer} id                  Currency ID
         * @param  {Boolean} isInventoryCurrency
         *
         * @return {Object | false}
         */
        setCurrencyById (id, isInventoryCurrency = false) {
            // console.debug('setCurrencyById() fired.', JSON.stringify(this.currencies), id, isInventoryCurrency);

            if (typeof this.currencies == undefined || this.currencies == undefined) {
                console.error('setCurrencyById() fired, but `currencies` property is needed to work properly.');
                return false;
            }

            let currency = _.find(this.currencies, { 'id': id });

            if (typeof currency === undefined || currency == undefined) {
                console.error('setCurrencyById() fired, but `currency` was not found inside `currencies` array.', currency, JSON.stringify(this.currencies));
                return false;
            }

            if (isInventoryCurrency) {
                this.inventory_currency = currency;
                return currency;
            }

            this.currency = currency;
            return currency;
        },

        /**
         * Sets the `inventory_currency` property and/or returns it.
         * Returns false if it's not available and fails to set it.
         *
         * @return {Object | false}
         */
        getInventoryCurrency () {
            // console.error('getInventoryCurrency() fired.', JSON.stringify(this.inventory_currency), this.inventoryCurrencyId);
            if (! _.isEmpty(this.inventory_currency)) {
                // console.debug('this.inventory_currency is not empty, so returning it.');
                return this.inventory_currency;
            }

            if (this.inventoryCurrencyId) {
                // console.debug('this.inventoryCurrencyId is not empty, so use it.', this.inventoryCurrencyId);
                let cur = this.setCurrencyById(this.inventoryCurrencyId, true);
                // console.debug('getInventoryCurrency() fired. cur = ', cur);
                return this.inventory_currency;
            }

            // console.error('getInventoryCurrency() fired.', false);
            return false;
        },

        /**
         * Sets the `currency` property and/or returns it.
         * Returns false if it's not available and fails to set it.
         *
         * @return {Object | false}
         */
        getCurrency () {
            // console.error('getCurrency() fired.', this.currency, this.currencyId, this.currency_id);
            if (! _.isEmpty(this.currency)) {
                return this.currency;
            }

            if (this.currencyId) {
                return this.setCurrencyById(this.currencyId);
            }

            if (this.currency_id) {
                return this.setCurrencyById(this.currency_id);
            }

            // console.error('getCurrency() fired.', false);
            return false;
        },

        /**
         * Sets `inventory_currency` and `currency` objects based on what's available.
         * Sets `currency` = `inventory_currency` if we're creating a new invoice.
         *
         * @return {Boolean}
         */
        setCurrencies () {

            // Set the default currency if we're creating a new invoice
            if (! this.isEditingInvoice() && ! _.isEmpty(this.inventory_currency.length)) {
                this.currency = this.inventory_currency;
            }

            // console.debug('setCurrencies() fired.', arguments.callee.caller);
            if (! this.getInventoryCurrency() || ! this.getCurrency()) {
                // console.error('setCurrencies() fired.', false);
                return false;
            }

            // console.error('setCurrencies() fired.', true);
            return true;
        },

        /**
         * Fetches a currency from the API, sets the `currency` and `inventory_currency`, and runs the callback function.
         *
         * @param  {integer}   id         Currency ID to lookup
         * @param  {Function}  callback   Callback function to run after done setting properties.
         * @param  {boolean}   isInventoryCurrency
         *
         * @return {Promise | false}
         */
        fetchCurrencyById (id, callback, isInventoryCurrency = false) {
            // console.debug('fetchCurrencyById() fired.', id, arguments.callee.caller);
            if (id === undefined) {
                return false;
            }

            this.fetchCurrencies().then(() => {
                this.setCurrencyById(id, isInventoryCurrency);
            }).then(() => {
                callback();
            });
        },

        /**
         * Helper function for specifying the ID given is intended to set the `inventory_currency` property.
         *
         * @param  {[type]}   id       [description]
         * @param  {Function} callback [description]
         *
         * @return {[type]}            [description]
         */
        fetchInventoryCurrencyById(id, callback) {
            return this.fetchCurrencyById(id, callback, true);
        },

        /**
         * Sets `exchange_rate` using `inventory_currency.name` -> `currency.name`
         *
         * @return {Promise | false}
         */
        fetchExchangeRate () {

            if (_.isEmpty(this.inventory_currency) || _.isEmpty(this.currency)) {
                if (! this.setCurrencies() && this.isEditingInvoice()) {
                    console.error(
                        'fetchExchangeRate() fired, but `inventory_currency` and `currency` properties required to work properly.',
                        this.inventory_currency.id,
                        this.currency.id,
                        arguments.callee.caller
                    );
                    return new Promise((resolve, reject) => {
                        resolve(false);
                    });
                }
            }
            // console.debug('fetchExchangeRate() fired successfully.', this.inventory_currency.id, this.currency.id, arguments.callee.caller);

            if (this.inventory_currency.name === this.currency.name) {
                return new Promise((resolve, reject) => {
                    this.exchange_rate = 1;
                    resolve(1);
                });
            }

            return axios.get(`/api/currencies/exchange/${this.inventory_currency.name}/${this.currency.name}`).then(response => {
                this.exchange_rate = (response.data !== '') ? response.data : 0;
            });
        },
    }
};
