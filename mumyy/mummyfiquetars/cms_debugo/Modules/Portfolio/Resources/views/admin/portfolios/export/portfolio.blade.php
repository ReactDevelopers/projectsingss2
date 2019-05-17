<table>
	<thead>
		<tr>
			<th>Vendor</th>
			<th>Category</th>
			<th>Sub Category</th>
			<th>City</th>
			<th>Title</th>
			<th>Description</th>
			<th>Photography</th>
			<th>Created Date Time</th>
			<th>Updated Date Time</th>
		</tr>
	</thead>
	<tbody>
		@if(count($portfolios) > 0)
			@foreach($portfolios as $key => $portfolio)
				<tr>
					<td>{{ $portfolio->vendor ? $portfolio->vendor->getVendorBusinessName() : ""}}</td>
					<td>{{ $portfolio->category ? $portfolio->category->name : ""}}</td>
					<td>{{ $portfolio->subCategory ? $portfolio->subCategory->name : ""}}</td>
					<td>{{ $portfolio->city }}</td>
					<td>{{ $portfolio->title }}</td>
					<td>{{ $portfolio->description }}</td>
					<td>{{ $portfolio->photography }}</td>
					<td>{{ $portfolio->created_at && $portfolio->created_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($portfolio->created_at)->format('d/m/Y H:i:s') : null }}</td>
					<td>{{ $portfolio->updated_at && $portfolio->updated_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($portfolio->updated_at)->format('d/m/Y H:i:s') : null}}</td>
				</tr>
			@endforeach
		@endif
	</tbody>
</table>