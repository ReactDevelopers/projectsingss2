Vue.component('hotels-show', {
    props: ['user', 'raceHotelId'],
    mixins: [
        require('../../mixins/partials/sorttable'),
        require('../../mixins/partials/searchtable'),
        require('../../mixins/uploads')
    ],
    mounted() {
        this.fetchUploads();
    },
});
