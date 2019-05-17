module.exports = {

    methods: {
        /**
         * Function to search table by column.
         * @param  {String} page rooming_list/reconcile
         *
         * @return {void}
         */
        searchTable (page) {
            let q = document.getElementById("q");
            let v = q.value.toLowerCase();
            let t = document.getElementById("search_table_by_column");
            let rows = t.getElementsByTagName("tr");
            let on = 0;
            for ( i = this.initilizeI(page); i < rows.length; i++ ) {
                let fullname = rows[i].getElementsByTagName("td");
                if (typeof fullname[3] !== 'undefined') {
                    fullname = fullname[3].innerHTML.toLowerCase(); //for column Guest Name
                }
                if ((fullname != '') && (typeof fullname !== 'undefined') && (typeof fullname[3] !== 'undefined')) {
                    if ( v.length == 0 || (v.length < 3 && fullname.indexOf(v) == 0) || (v.length >= 3 && fullname.indexOf(v) > -1 ) ) {
                        rows[i].style.display = "";
                        on++;
                    } else {
                        rows[i].style.display = "none";
                    }
                } else {
                    rows[i].style.display = "none";
                }
            }
        },
        /**
         * Function to reset the search table.
         * @param  {String} page rooming_list/reconcile
         *
         * @return {void}
         */
        resetSearchTable (page) {
            let q = document.getElementById("q");
            let v = q.value.toLowerCase();
            let t = document.getElementById("search_table_by_column");
            let rows = t.getElementsByTagName("tr");
            let on = 0;
            for ( i = this.initilizeI(page); i < rows.length; i++ ) {
                rows[i].style.display = "";
            }
            document.getElementById("q").value = '';
        },
        /**
         * Function to initialize i
         * @param  {String} page rooming_list/reconcile
         *
         * @return {integer}
         */
        initilizeI (page) {
            if (page == 'rooming_list') {
                return 2;
            } else {
                return 1;
            }
        }
    }
};
