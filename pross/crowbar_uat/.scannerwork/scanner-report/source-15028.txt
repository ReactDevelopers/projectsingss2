@php 
	$header = 'innerheader';
	$footer = 'footer';
	$settings = \Cache::get('configuration');
@endphp
@extends('layouts.front.main')

@section('content')
<section class="error-section">
	<div class="container">
		<div class="error-wrapper">
			<h3 class="error-type">500</h3>
			<p class="error-type-msg">Be right back</p>
			<span>:(</span>			
		</div>
	</div>	
</section>
@endsection