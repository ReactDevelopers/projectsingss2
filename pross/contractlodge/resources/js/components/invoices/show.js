import DatePick from '../vendor/vue-date-pick/vueDatePick';
import '../vendor/vue-date-pick/vueDatePick.css';

Vue.component('invoices-show', {
    props: [
        'user',
        'invoiceId',
        'invoiceType',
        'isApproved',
        'inventoryContactId',
        'clientId',
        'raceId',
        'hotelId',
        'raceHotelId',
        'inventoryCurrencyId',
        'customInvoiceId',
        'confirmationId',
    ],

    data() {
        return {
            termsOpen: false,
            upload_file: '',
            uploaded_rows: [],
            form: new SparkForm({
               name: '',
               tag:'',
            }),
            confirmation_contact_id: this.inventoryContactId,
            raceIdData: this.raceId,
            hotelIdData: this.hotelId,
            raceHotelIdData: this.raceHotelId,
        }
    },

    mixins: [
        require('../../mixins/money'),
        require('../../mixins/invoices'),
        require('../../mixins/payments'),
        require('../../mixins/uploads'),
        require('../../mixins/clients/autosuggest'),
        require('../../mixins/invoices/confirmations'),
        require('../../mixins/invoices/custom_invoices'),
    ],

    components: { DatePick },

    methods: {
        toggleTerms () {
            this.termsOpen = ! this.termsOpen;

            if (this.termsOpen) {
                setTimeout(function() {
                    window.scrollTo(0, document.body.scrollHeight);
                }, 300);
            }
        },

        //----------------------------------------------------------------------------------
        // FIXME: All Upload methods should use the `uploads` mixin. Refactor if it you need
        // but don't repeat code. This is a big no no.  Please don't do this again.
        //----------------------------------------------------------------------------------

        /**
         * Gather the form data for the file upload.
         */
        gatherFormData() {
            const data = new FormData();

            data.append('upload_file', (typeof this.$refs.upload_file.files[0] === 'undefined') ? '' : this.$refs.upload_file.files[0]);
            data.append('common_invoice_id', this.invoiceId);
            data.append('invoice_type', this.invoiceType);

            return data;
        },

        /**
         * function for preview uploaded file.
         */
        previewFile(event) {
            // Reference to the DOM input element
            var input = event.target;

            // Ensure that you have a file before attempting to read it
            if (input.files && input.files[0]) {
                var reader = new FileReader();

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
         * Upload the file.
         */
        upload(e) {
            var self = this;
            var swal = this;

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
                        console.log(error.response.data);
                        self.form.setErrors(error.response.data.errors);
                    }
                );
        },

        fetchUploads() {
            if (typeof this.invoiceId == 'undefined') {
                return;
            }

            return axios.get(`/api/uploads/${this.invoiceType}/${this.invoiceId}`).then((response) => {
                this.uploaded_rows = response.data.uploads;
            });
        },

        deleteUploads(id) {
            return axios.delete(`/api/delete_uploads/${id}/${this.invoiceType}`).then((response) => {
                //
            });
        },

        deleteUploadsRow(index,id) {
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

        setClientContactOnChange (event) {
            let contact_id = event.target.options[event.target.selectedIndex].attributes['id'].value;

            axios.post(`/api/client/${this.clientId}/contact/${contact_id}/invoices/${this.invoiceType}/${this.invoiceId}`)
                .then((response) => {
                    //
            });
        },
    },

    mounted() {
        this.invoice_type = this.invoiceType; // requires invoices mixin

        this.fetchCurrencies().then(() => {
            this.setCurrencies();
            this.fetchRaceHotel().then(() => { // gets this.meta
                this.fetchInvoice().then(() => {
                    this.setExchangedRates();
                });

                this.fetchRace();
                this.fetchHotel();
                this.fetchClient();
                this.fetchUploads();
            });
        });
    }
});
