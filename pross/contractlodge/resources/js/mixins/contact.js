module.exports = {

    methods: {

        /**
         * To add new contact row
         */
        addContactRow () {
            this.form.contacts.push({
                name: '',
                email: '',
                phone: '',
                role: '',
            });
        },

        /**
         * To remove the given index from the contact Array
         * @param {Integer} index - Row index
         */
        deleteContactRow (index) {
            this.form.contacts.splice(index, 1);
        }
    }
}
