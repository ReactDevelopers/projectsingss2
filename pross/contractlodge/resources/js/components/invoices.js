Vue.component('invoices', {
    props: ['user'],

    data() {
        return {
            openContracts: []
        }
    },

    methods: {
        toggleContracts (contractId) {
            let index = _.indexOf(this.openContracts, contractId);

            if (index > -1) {
                this.openContracts.splice(index, 1);
                return;
            }

            this.openContracts.push(contractId);
        },

        isOpen (contractId) {
            return (_.indexOf(this.openContracts, contractId) > -1) ? true : false;
        }
    }
});
