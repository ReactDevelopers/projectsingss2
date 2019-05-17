<!-- Left Side Of Navbar -->
<li class="nav-item">
    <a class="nav-link" href="/home">Races</a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('clients.index')); ?>">Clients</a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('hotels.index')); ?>">Hotels</a>
</li>

<li class="nav-item dropdown">
    <a href="#" class="d-block d-md-flex text-center nav-link dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown"
       aria-haspopup="true" aria-expanded="false">
        <span class="d-none d-md-block" href="#">Reports</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="<?php echo e(route('hotels.bills.index')); ?>">
            
            <?php echo e(__('Unpaid Hotel Payments')); ?>

        </a>
        <a class="dropdown-item" href="<?php echo e(route('reports.confirmations.outstanding')); ?>">
            
            <?php echo e(__('Outstanding Client Confirmations')); ?>

        </a>
    </li>
</li>






