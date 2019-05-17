<div class="form-row mb-4">
    <div class="col-sm-12">
        <h5 class="mb-3"><strong>Reconcile:</strong> {{ $race->full_name }} / {{ $hotel->name }}</h5>
    </div>
    <div class="col-sm-4">

        <div class="row">
            <div class="col-sm-12">
                <p>Currency: {{ $meta->currency->name }}</p>
                <p>List Sent On: 31/12/2019</p>
                <p>List Confirmed On: 31/12/2019</p>
            </div>
        </div>
    </div>

    @include('partials.common.rooming-list-room-type-breakdown')

    <div class="col-sm-4">
        <div class="clearfix">
            <select name="team" class="form-control col-sm-12 col-md-8 col-lg-6 pull-right">
                <option value="">-- By Client --</option>
                <option value="">McLaren</option>
            </select>
        </div>
        <div class="clearfix mt-3">
            <input type="text" class="form-control col-sm-12 col-md-8 col-lg-6 pull-right" placeholder="Search for anything">
        </div>
        <div class="clearfix mt-3 text-right">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="showColummsFor" id="showColumnsForAll" value="all" checked>
                <label class="form-check-label" for="showColumnsForAll">
                    FOWT view
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="showColummsFor" id="showColumnsForHotel" value="hotel">
                <label class="form-check-label" for="showColumnsForHotel">
                    Hotel view
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="showColummsFor" id="showColumnsForClient" value="client">
                <label class="form-check-label" for="showColumnsForClient">
                    Client view
                </label>
            </div>
            <div class="clearfix mt-3">
                <button v-on:click="resetSearchTable('reconcile')" class="btn btn-primary btn-sm ml-2 float-right">
                    Reset
                </button>
                <input type="text" name="q" id="q" value="" 
                    v-on:keyup="searchTable('reconcile')" 
                    placeholder="Search for guest name" 
                    class="form-control col-sm-12 col-md-8 col-lg-4 float-right">
            </div>
        </div>
    </div>

    <div class="col-sm-12 mb-3">
        <strong>Notes Sent to Hotel</strong>
        <p>Here is where the notes to the hotel would go if we had some.</p>
    </div>

    @include('partials.common.rooming-list-legend')
</div>

<div class="form-row mb-4">
    <div class="col-sm-12">
        <table id="search_table_by_column" class="table table-sm table-striped override-table table-bordered table-fixed">
            <thead>
                <tr>
                    <th class="text-center">Row</th>
                    <th>Room Type</th>
                    <th>Client</th>
                    <th>Guest</th>
                    <th class="text-center">Th<br>27<br>SEP</th>
                    <th class="text-center">Fr<br>28<br>SEP</th>
                    <th class="text-center">Sa<br>29<br>SEP</th>
                    <th class="text-center">Su<br>30<br>SEP</th>
                    <th class="text-center">Mo<br>1<br>OCT</th>
                    <th class="text-center">Tu<br>2<br>OCT</th>
                    <th class="text-center">We<br>3<br>OCT</th>
                    <th class="text-center table-success">Th<br>4<br>OCT</th>
                    <th class="text-center table-success">Fr<br>5<br>OCT</th>
                    <th class="text-center table-success">Sa<br>6<br>OCT</th>
                    <th class="text-center table-success">Su<br>7<br>OCT</th>
                    <th class="text-center">Mo<br>8<br>OCT</th>
                    <th class="text-center">Tu<br>9<br>OCT</th>
                    <th class="text-center">We<br>10<br>OCT</th>
                    <th class="text-center">Th<br>11<br>OCT</th>
                    <th class="text-center">Fr<br>12<br>OCT</th>
                    <th class="text-center">Sa<br>13<br>OCT</th>
                    <th class="text-right table-info">Min Night Cost</th>
                    <th class="text-right table-info">P&P Cost</th>
                    <th class="text-center table-info">Room Total</th>
                    <th class="text-right table-info">Laundry</th>
                    <th class="text-right table-info">Extras</th>
                    <th class="text-right table-info">Hotel Cost</th>
                    <th class="text-right">Invoiced</th>
                    <th class="text-right">Credits</th>
                    <th class="text-right">To Invoice</th>
                    <th class="text-right">Client Cost</th>
                    <th class="text-right">Profit $</th>
                    <th class="text-right">Profit %</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1-1</td>
                    <td>Single</td>
                    <td>McLaren</td>
                    <td class="nowrap">BERBERIC, Aleksandar</td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center">
                        <strong class="text-success">EC</strong>
                    </td>
                    <td class="text-center">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <strong class="text-success">LC</strong>
                    </td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-right">$71,400.00</td>
                    <td class="text-right">$53,550.00</td>
                    <td class="text-center">$124,950.00</td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="4,500" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">$129,450.00</td>
                    <td class="text-right">$86,000.00</td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">$69,000.00</td>
                    <td class="text-right">$155,000.00</td>
                    <td class="text-right">$25,550.00</td>
                    <td class="text-right">19.74%</td>
                </tr>
                <tr>
                    <td class="text-center">2-2</td>
                    <td>Single</td>
                    <td>McLaren</td>
                    <td class="nowrap">HOLLAND, Ellen</td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-right">$71,400.00</td>
                    <td class="text-right">$35,700.00</td>
                    <td class="text-center">$107,100.00</td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="9,000" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">$116,100.00</td>
                    <td class="text-right">$86,000.00</td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">$52,000.00</td>
                    <td class="text-right">$138,000.00</td>
                    <td class="text-right">$23,400.00</td>
                    <td class="text-right">20.42%</td>
                </tr>
                <tr>
                    <td class="text-center">3-3</td>
                    <td>Single</td>
                    <td>McLaren</td>
                    <td class="nowrap">SPENCE, Paul</td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-success">
                        <i class="fa fa-check text-success"></i>
                    </td>
                    <td class="text-center table-warning"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-right">$71,400.00</td>
                    <td class="text-right">$0.00</td>
                    <td class="text-center">$71,400.00</td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">$71,400.00</td>
                    <td class="text-right">$86,000.00</td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">$0.00</td>
                    <td class="text-right">$86,000.00</td>
                    <td class="text-right">$14,600.00</td>
                    <td class="text-right">20.45%</td>
                </tr>
                <tr>
                    <td class="text-center">4-4</td>
                    <td>Single</td>
                    <td>McLaren</td>
                    <td class="nowrap"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center table-warning">
                        <i class="fa fa-close text-danger"></i>
                    </td>
                    <td class="text-center table-warning">
                        <i class="fa fa-close text-danger"></i>
                    </td>
                    <td class="text-center table-warning">
                        <i class="fa fa-retweet text-success"></i>
                    </td>
                    <td class="text-center table-warning">
                        <i class="fa fa-retweet text-success"></i>
                    </td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-right">$35,700.00</td>
                    <td class="text-right">$0.00</td>
                    <td class="text-center">$35,700.00</td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">$35,700.00</td>
                    <td class="text-right">$86,000.00</td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="43,000" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">-$43,000.00</td>
                    <td class="text-right">$43,000.00</td>
                    <td class="text-right">$7,300.00</td>
                    <td class="text-right">20.45%</td>
                </tr>
                <tr>
                    <td class="text-center"></td>
                    <td></td>
                    <td>McLaren</td>
                    <td class="nowrap">
                        <input type="text" class="form-control form-control-sm" value="Team meal" placeholder="Expense description">
                    </td>
                    <td colspan="17" class="text-center"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-center"></td>
                    <td class="text-right"></td>
                    <td class="text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm" value="1,000" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td class="text-right">$1,000.00</td>
                    <td class="text-right">$0.00</td>
                    <td class="text-right">$0.00</td>
                    <td class="text-right">$1,000.00</td>
                    <td class="text-right">$1,000.00</td>
                    <td class="text-right">$0.00</td>
                    <td class="text-right">0%</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="table-secondary text-center">178</td>
                    <td class="table-secondary text-center"></td>
                    <td class="table-secondary text-center"></td>
                    <td class="table-secondary text-center"></td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-center">1</td>
                    <td class="table-secondary text-center">2</td>
                    <td class="table-secondary text-center">2</td>
                    <td class="text-center table-success">3</td>
                    <td class="text-center table-success">3</td>
                    <td class="text-center table-success">3</td>
                    <td class="text-center table-success">2</td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-center">0</td>
                    <td class="table-secondary text-right">$249,900.00</td>
                    <td class="table-secondary text-right">$89,250.00</td>
                    <td class="table-secondary text-center">$339,150.00</td>
                    <td class="table-secondary text-right">$0.00</td>
                    <td class="table-secondary text-right">$14,500.00</td>
                    <td class="table-secondary text-right text-success">
                        <i class="fa fa-check-circle text-success"></i>
                        $353,650.00
                    </td>
                    <td class="table-secondary text-right">$344,000.00</td>
                    <td class="table-secondary text-right">$43,000.00</td>
                    <td class="table-secondary text-right">$79,000.00</td>
                    <td class="table-secondary text-right">$423,000.00</td>
                    <td class="table-secondary text-right">$69,350.00</td>
                    <td class="table-secondary text-right">19.61%</td>
                </tr>
                <tr>
                    <td colspan="26" class="table-secondary text-right">Hotel Invoice Total:</td>
                    <td class="table-secondary text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm"
                            value="353,650" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td colspan="6" class="table-secondary text-left"></td>
                </tr>
                <tr>
                    <td colspan="26" class="table-secondary text-right">Bank Charges / FX:</td>
                    <td class="table-secondary text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm"
                            value="" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td colspan="6" class="table-secondary text-left"></td>
                </tr>
                <tr>
                    <td colspan="26" class="table-secondary text-right">Deposits Paid to Hotel:</td>
                    <td class="table-secondary text-right">
                        <input type="text" class="text-right pull-right form-control form-control-sm"
                            value="249,900" placeholder="0" style="max-width: 7rem;">
                    </td>
                    <td colspan="6" class="table-secondary text-left"></td>
                </tr>
                <tr>
                    <td colspan="26" class="table-secondary text-right">Balance Due to Hotel:</td>
                    <td class="table-secondary text-right text-danger">
                        {{-- <i class="fa fa-check-circle text-success"></i> --}}
                        $103,750.00
                    </td>
                    <td colspan="6" class="table-secondary text-left text-success"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="col-sm-12 mt-4">
        <h5 class="mb-3">Reconciliation Notes</h5>
        {{-- NOTE: This is NOT the same as the notes to the hotel (on the rooming list) --}}
        <textarea class="form-control" rows="5">27/09 - For McLAren all room charges, taxes, breakfast, laundry and car parking to be applied on the Master Account
27/09 - McLaren - Incidental extras i.e. restaurant/bar bills, minibar usage, movies, telephone charges etc. are to be charged to the guest and settled directly with the hotel on check-out.
27/09 -  Please don’t ask guests for their credit card details – we will act as a guarantee</textarea>
    </div>
</div>


