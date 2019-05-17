<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Email</th>
			<th>Status</th>
			<th>Website</th>
			<th>Business Category</th>
			<th>Business Sub Category</th>
			<th>Business Name</th>
			<th>Country</th>
			<th>City</th>
			<th>Postal Code</th>
			<th>Business Address</th>
			<th>Business Phone Code</th>
			<th>Business Phone Number</th>
			<th>Social Media Facebook</th>
			<th>Social Media Twitter</th>
			<th>Social Media Instagram</th>
			<th>Social Media Pinterest</th>
			<th>Created Date Time</th>
			<th>Last Login Date Time</th>
		</tr>
	</thead>
	<tbody>
		@if(count($vendors) > 0)
			@foreach($vendors as $key => $vendor)
				<tr>
					<td>{{ $vendor->full_name }}</td>
					<td>{{ $vendor->email }}</td>
					<td>{{ $vendor->getStatus() }}</td>
					<td>{{ $vendor->vendorProfile ? $vendor->vendorProfile->website: null }}</td>
					<td>{{ $vendor->category_name }}</td>
					<td>{{ $vendor->category_sub_name }}</td>
					<td>{{ $vendor->vendorProfile ? $vendor->vendorProfile->business_name: null }}</td>
					<td>{{ $vendor->location 	  ? $vendor->location->country_name: null }}</td>
					<td>{{ $vendor->location 	  ? $vendor->location->city_name: null }}</td>
					<td>{{ $vendor->location 	  ? $vendor->location->zip_code: null }}</td>
					<td>{{ $vendor->vendorProfile ? $vendor->vendorProfile->business_address: null }}</td>
					<td>{{ $vendor->location 	  ? $vendor->location->country_code: null }}</td>
					<td>{{ $vendor->vendorProfile ? $vendor->vendorProfile->business_phone: null }}</td>
					<td>{{ $vendor->vendorProfile ? $vendor->vendorProfile->getVendorSocial('facebook'): null }}</td>
					<td>{{ $vendor->vendorProfile ? $vendor->vendorProfile->getVendorSocial('twitter'): null }}</td>
					<td>{{ $vendor->vendorProfile ? $vendor->vendorProfile->getVendorSocial('instagram'): null }}</td>
					<td>{{ $vendor->vendorProfile ? $vendor->vendorProfile->getVendorSocial('pinterest'): null }}</td>
					<td>{{ $vendor->created_at && $vendor->created_at != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($vendor->created_at)->format('d/m/Y H:i:s') : null }}</td>
					<td>{{ $vendor->last_login && $vendor->last_login != \Carbon\Carbon::create(0, 0, 0, 0) ? \Carbon\Carbon::parse($vendor->last_login)->format('d/m/Y H:i:s') : null }}</td>
				</tr>
			@endforeach
		@endif
	</tbody>
</table>