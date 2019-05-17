module.exports = {
    data() {
        return {
            upload_file: '',
            uploaded_rows: [],
        }
    },

    methods: {
        /**
         * Gather the form data for the file upload.
         *
         * IMPORTANT: This requires the 'invoice' mixin
         *
         * @return {FormData}
         */
        gatherFormData () {
            let data = new FormData();

            data.append('upload_file', (typeof this.$refs.upload_file.files[0] === 'undefined') ? '' : this.$refs.upload_file.files[0]);
            if ((typeof this.invoiceId !== 'undefined')) {
                data.append('common_invoice_id', this.invoiceId);
            }
            if ((typeof this.invoice_type !== 'undefined')) {
               data.append('invoice_type', this.invoice_type); // requires the invoice mixin
            }
            if ((typeof this.raceHotelId !== 'undefined')) {
               data.append('race_hotel_id', this.raceHotelId);
            }

            return data;
        },

        /**
         * Preview uploaded file in the browser
         *
         * @param {Event} event The event click
         *
         * @return {void}
         */
        previewFile (event) {
            // Reference to the DOM input element
            let input = event.target;

            // Ensure that you have a file before attempting to read it
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                // Define a callback function to run, when FileReader finishes its job
                reader.onload = (e) => {
                    this.upload_file = e.target.result;
                }

                // Start the reader job - read file as a data url (base64 format)
                reader.readAsDataURL(input.files[0]);
                this.upload();
            }
        },

        /**
         * Upload the file via the API and form data
         *
         * @return {void}
         */
        upload (e) {
            let self = this;
            let swal = this;

            this.form.startProcessing();

            axios.post('/api/uploads', this.gatherFormData())
                .then((response) => {
                        this.uploaded_rows.push({
                            id: response.data.id,
                            orig_filename: response.data.orig_filename,
                            filepath: response.data.filepath
                        });
                    },
                    (error) => {
                        self.form.setErrors(error.response.data.errors);
                    }
                );
        },

        /**
         * Retrieve the Uploads for the invoice from the API
         *
         * @return {Promise | void}
         */
        fetchUploads () {

            if ((typeof this.raceHotelId !== 'undefined') && (typeof this.invoice_type === 'undefined')) {
                return axios.get(`/api/uploads_race_hotel/${this.raceHotelId}`).then((response) => {
                    this.uploaded_rows = response.data.uploads;
                });
            }

            if (typeof this.invoiceId == 'undefined') {
                return;
            }

            return axios.get(`/api/uploads/${this.invoice_type}/${this.invoiceId}`).then((response) => {
                this.uploaded_rows = response.data.uploads;
            });
        },

        /**
         * Delete the upload from the system via API call
         *
         * @param  {Integer} id Upload ID
         *
         * @return {Promise}
         */
        deleteUploads (id) {
            return axios.delete(`/api/delete_uploads/${id}/${this.invoice_type}`);
        },

        /**
         * Delete the upload row with user's confirmation on sweet alert modal.
         *
         * @param  {Integer} index Index of Upload item in array
         * @param  {Integer} id    Upload ID
         *
         * @return {void}
         */
        deleteUploadsRow (index, id) {
            swal({
                title: 'Are you sure you want to delete this file?',
                text: "You won't be able to undo this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: true
            }, () => {
                this.deleteUploads(id);
                this.uploaded_rows.splice(index, 1);
            })
        },
    },
}
