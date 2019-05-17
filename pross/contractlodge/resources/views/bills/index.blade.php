@extends('spark::layouts.app')

@section('content')
<bills :user="user" inline-template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @include('flash::message')

                <div class="card card-default">
                    <div class="card-header">
                        {{-- <a href="{{ route('bills.create') }}" class="btn btn-primary btn-sm float-right mr-3"
                            role="button" dusk="add-bill">
                            <i class="fa fa-plus"></i> {{__('Add Payment')}}
                        </a> --}}

                        @isset($showPaid)
                            <a href="{{ route('bills.index') }}" class="btn btn-success btn-sm float-right mr-3"
                                role="button">
                                <i class="fa fa-bolt mr-2"></i> {{ __('View Unpaid Payments')}}
                            </a>
                        @else
                            {{-- <a href="{{ route('bills.paid') }}" class="btn btn-secondary btn-sm float-right mr-3"
                                role="button" dusk="view-bill-paid">
                                <i class="fa fa-archive mr-2"></i> {{ __('View Paid Payments')}}
                            </a> --}}
                        @endif

                        Reports /

                        @isset($showPaid)
                            {{__('Paid')}}
                        @else
                            {{__('Unpaid')}}
                        @endif

                        {{__('Hotel Payments')}}
                    </div>

                    <div class="card-body">

                        <div class="form-row">
                            @if (! $bills->count())
                                @isset($showPaid)
                                    <div class="col-sm-12"><p>There are currently no paid hotel bills.</p></div>
                                @else
                                    <div class="col-sm-12"><p>No unpaid hotel bills found.</div>
                                @endif
                            @else
                                <div class="col-sm-12">
                                    <table class="table table-sm table-striped override-table">
                                        <thead>
                                            <tr>
                                                <th>Race</th>
                                                <th>Hotel</th>
                                                <th>Description</th>
                                                <th class="text-right">Amount Due</th>
                                                <th class="text-right">Amount Paid</th>
                                                <th class="text-right">Outstanding</th>
                                                <th class="text-center">Due on</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bills as $bill)
                                                @foreach($bill->payments as $payment)
                                                    <tr>
                                                        <td>
                                                            @isset($showPaid)
                                                                {{ $bill->race_hotel->race->full_name }}
                                                            @else
                                                                <a href="{{ route('races.show', [
                                                                    'race' => $bill->race_hotel->race->id
                                                                    ]) }}">
                                                                    {{ $bill->race_hotel->race->full_name }}
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @isset($showPaid)
                                                            @else
                                                                <a href="{{ route('races.hotels.show', [
                                                                    'race' => $bill->race_hotel->race->id,
                                                                    'hotel' => $bill->race_hotel->hotel->id
                                                                ])}}">{{ $bill->race_hotel->hotel->name }}</a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('races.hotels.bills.edit', [
                                                                'race' => $bill->race_hotel->race->id,
                                                                'hotel' => $bill->race_hotel->hotel->id,
                                                            ]) }}">{{ $payment->payment_name }}</a>
                                                        </td>
                                                        <td class="text-right">
                                                            @money($payment->amount_due, $bill->currency->symbol)
                                                        </td>
                                                        <td class="text-right">
                                                            @money($payment->amount_paid, $bill->currency->symbol)
                                                        </td>
                                                        <td class="text-right">
                                                            @money(($payment->amount_due - $payment->amount_paid), $bill->currency->symbol)
                                                        </td>
                                                        <td class="text-center">{{ $payment->friendly_due_on }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                        {{-- <tfoot> --}}
                                            {{-- <tr> --}}
                                                {{-- <td><strong>Totals</strong></td> --}}
                                                {{-- <td class="text-right"><strong>$46,131.00</strong></td> --}}
                                                {{-- <td class="text-right"><strong>$18,470.00</strong></td> --}}
                                                {{-- <td class="text-right"><strong>$27,661.00</strong></td> --}}
                                                {{-- <td></td> --}}
                                            {{-- </tr> --}}
                                        {{-- </tfoot> --}}
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</bills>
@endsection
