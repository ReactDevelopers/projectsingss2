<!-- Left Side Of Navbar -->
<li class="nav-item">
    <a class="nav-link" href="/home">Races</a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('clients.index') }}">Clients</a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('hotels.index') }}">Hotels</a>
</li>

<li class="nav-item dropdown">
    <a href="#" class="d-block d-md-flex text-center nav-link dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown"
       aria-haspopup="true" aria-expanded="false">
        <span class="d-none d-md-block" href="#">Reports</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{{ route('hotels.bills.index') }}">
            {{-- <i class="fa fa-fw text-left fa-btn fa-user-secret"></i>  --}}
            {{__('Unpaid Hotel Payments')}}
        </a>
        <a class="dropdown-item" href="{{ route('reports.confirmations.outstanding') }}">
            {{-- <i class="fa fa-fw text-left fa-btn fa-user-secret"></i>  --}}
            {{__('Outstanding Client Confirmations')}}
        </a>
    </li>
</li>

{{-- <li class="nav-item">
    <a class="nav-link" href="{{ route('confirmations.index') }}">Confirmations</a>
</li> --}}

{{-- <li class="nav-item">
    <a class="nav-link" href="{{ route('invoices.index') }}">Invoices</a>
</li> --}}

{{-- <li class="nav-item">
    <a class="nav-link" href="{{ route('reports.index') }}">Reports</a>
</li> --}}
