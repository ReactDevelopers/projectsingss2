module.exports = {

    methods: {
        /**
         * Function to sort table by column.
         *
         * @param {Integer} n
         * @param {String}  strtype
         *
         * @return {void}
         */
        sortTable(n, strtype) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = this.$refs.sortTableByColumn;
            switching = true;
            //Set the sorting direction to ascending:
            dir = "asc";
            /*Make a loop that will continue until
            no switching has been done:*/
            while (switching) {
                //start by saying: no switching is done:
                switching = false;
                rows = table.rows;
                //start by saying there should be no switching:
                shouldSwitch = false;
                /*Loop through all table rows (except the
                first, which contains table headers):*/
                for (i = 2; i < (rows.length - 2); i++) {
                    /*Get the two elements you want to compare,
                    one from current row and one from the next:*/
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];

                    // Call to the function to compare column values
                    shouldSwitch = this.compareTableColumns(shouldSwitch, dir, strtype, x, y);
                    if (shouldSwitch) {
                        break;
                    }
                }
                if (shouldSwitch) {
                    /*If a switch has been marked, make the switch
                    and mark that a switch has been done:*/
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    //Each time a switch is done, increase this count by 1:
                    switchcount ++;
                } else {
                    /*If no switching has been done AND the direction is "asc",
                    set the direction to "desc" and run the while loop again.*/
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        },

        /**
         * Function to compare table columns.
         *
         * @param {Boolean} shouldSwitch
         * @param {String}  dir
         * @param {string}  strtype
         * @param {Object}  x
         * @param {Object}  y
         *
         * @return {Boolean}
         */
        compareTableColumns(shouldSwitch, dir, strtype, x, y) {
            /*check if the two rows should switch place,
            based on the direction, asc or desc:*/
            if (typeof x != "undefined" && typeof y != "undefined") {
                if (typeof strtype != "undefined" && 'number' == strtype) {
                    var num1 = x.innerHTML.split("-");
                    var num2 = y.innerHTML.split("-");

                    var num1 = Number(num1[0]);
                    var num2 = Number(num2[0]);
                    if ((dir == 'asc') && (num1 > num2)) {
                        //if so, mark as a switch
                        shouldSwitch= true;
                    } else if ((dir == 'desc') && (num1 < num2)) {
                        //if so, mark as a switch
                        shouldSwitch= true;
                    }
                } else {
                    if ((dir == 'asc') && (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase())) {
                        //if so, mark as a switch
                        shouldSwitch= true;
                    } else if ((dir == 'desc') && (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase())) {
                        //if so, mark as a switch
                        shouldSwitch= true;
                    }
                }
            }

            return shouldSwitch;
        }
    }
};
