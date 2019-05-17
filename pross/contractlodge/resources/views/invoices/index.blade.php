@extends('spark::layouts.app')

@section('content')
{{-- <invoices :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">{{__('Finances')}}</div>

                    <div class="card-body">
                        <ul class="nav nav-tabs" id="accounts" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="receivable-tab" data-toggle="tab" href="#receivable" role="tab"
                                aria-controls="receivable" aria-selected="false">A/R Clients</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="payable-tab" data-toggle="tab" href="#payable"
                                    role="tab" aria-controls="payable" aria-selected="true">A/P Hotels</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="accountsContent">
                            <div class="tab-pane show active" id="receivable" role="tabpanel" aria-labelledby="receivable-tab">
                                <h4 class="p-3">Accounts Receivable (Clients)</h4>
                                <div class="px-3">
                                    <table class="table table-sm table-striped override-table">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Client</th>
                                                <th>Hotel</th>
                                                <th>Event</th>
                                                <th class="text-right">Amount due</th>
                                                <th class="text-right">Amount paid</th>
                                                <th class="text-center">Due on</th>
                                                <th>Paid in full on</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><a href="/invoices/1">102934-3</a></td>
                                                <td>Mercedes</td>
                                                <td>Four Seasons</td>
                                                <td>Pirelli 2018 US Grand Prix</td>
                                                <td class="text-right">$775.00</td>
                                                <td class="text-right">$420.00</td>
                                                <td>Dec 31, 2018</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-plus mr-1"></i>
                                                        Add Payment
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="/invoices/1">102934-2</a></td>
                                                <td>Mercedes</td>
                                                <td>Four Seasons</td>
                                                <td>Pirelli 2018 US Grand Prix</td>
                                                <td class="text-right">$18,450.00</td>
                                                <td class="text-right">$0.00</td>
                                                <td>Dec 31, 2018</td>
                                                <td><button type="button" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-plus mr-1"></i>
                                                    Add Payment
                                                </button></td>
                                            </tr>
                                            <tr>
                                                <td><a href="/invoices/1">102934-1</a></td>
                                                <td>Mercedes</td>
                                                <td>Four Seasons</td>
                                                <td>Pirelli 2018 US Grand Prix</td>
                                                <td class="text-right">$18,450.00</td>
                                                <td class="text-right">$18,450.00</td>
                                                <td>Sept 25, 2018</td>
                                                <td>Sept 25, 2018</td>
                                            </tr>
                                            <tr>
                                                <td><a href="/invoices/1">333885-8</a></td>
                                                <td>Sauber</td>
                                                <td>The W</td>
                                                <td>Pirelli 2018 US Grand Prix</td>
                                                <td class="text-right">$1,900.00</td>
                                                <td class="text-right">$1,900.00</td>
                                                <td>Sept 25, 2018</td>
                                                <td>Sept 25, 2018</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="payable" role="tabpanel" aria-labelledby="payable-tab">
                                <a href="/payables/create" type="button" class="btn btn-primary btn-sm float-right mt-3 mr-3" role="button">
                                    <i class="fa fa-plus"></i> Add Payable
                                </a>
                                <h4 class="p-3">Accounts Payable (Hotels)</h4>

                                <div class="form-row">
                                    <div class="col-sm-12 ">
                                        <table class="table table-sm table-striped override-table">
                                            <thead>
                                                <tr>
                                                    <th>Number</th>
                                                    <th>Hotel</th>
                                                    <th class="text-center">Due on</th>
                                                    <th class="text-right">Amount due</th>
                                                    <th>Currency</th>
                                                    <th>Paid on</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><a href="/payables/1">83775-9827</a></td>
                                                    <td>Four Seasons</td>
                                                    <td>Dec 31, 2018</td>
                                                    <td class="text-right">$14,452.50</td>
                                                    <td>USD</td>
                                                    <td>
                                                        <a class="btn btn-primary btn-sm" href="#"
                                                        role="button">Mark Paid</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><a href="/payables/1">83775-9827</a></td>
                                                    <td>Four Seasons</td>
                                                    <td>Sept 28, 2018</td>
                                                    <td class="text-right">$14,452.50</td>
                                                    <td>USD</td>
                                                    <td>Sept 28, 2018</td>
                                                </tr>
                                                <tr>
                                                    <td><a href="/payables/2">9999-000</a></td>
                                                    <td>La Quinta</td>
                                                    <td>Dec 31, 2018</td>
                                                    <td class="text-right">$69.00</td>
                                                    <td>USD</td>
                                                    <td>July 31, 2018</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</invoices> --}}
@endsection
