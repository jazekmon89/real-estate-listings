<input type='hidden' class="listing-more-details" value='{{ $infos[0]->MND_ID }}'>
<table>
	<tr><th></th><th></th></tr>
	<tr>
		<td colspan=2>
			@php 
				$img = json_decode($infos[0]->IMG_SRC);
				if(is_object($img) && property_exists($img, 'img_0'))
					$img = $img->img_0->img_thumb;
				else
					$img = '';
				$img = !empty($img)?$img:'/images/Small.jpg';
			@endphp
			<img src="{{ $img }}" />
		</td>
	</tr>
	@if(!empty($infos[0]->MND_Community))
	<tr>
		<td class="tb-left-cell">Apartment Name: </td>
		<td class="tb-right-cell">{{ $infos[0]->MND_Community }}</td>
	</tr>
	@endif
	@if(!empty($infos[0]->MND_Address))
	<tr>
		<td class="tb-left-cell">Address: </td>
		<td class="tb-right-cell">{{ $infos[0]->MND_Address }}</td>
	</tr>
	@endif
	@if(!empty($infos[0]->MND_City))
	<tr>
		<td class="tb-left-cell">City: </td>
		<td class="tb-right-cell">{{ $infos[0]->MND_City }}</td>
	</tr>
	@endif
	@if(!empty($infos[0]->MND_ZIP))
	<tr>
		<td class="tb-left-cell">Zip: </td>
		<td class="tb-right-cell">{{ $infos[0]->MND_ZIP }}</td>
	</tr>
	@endif
	@if(!empty($infos[0]->MND_PhoneNo))
	<tr>
		<td class="tb-left-cell">Phone #: </td>
		<td class="tb-right-cell">{{ $infos[0]->MND_PhoneNo }}</td>
	</tr>
	@endif
	@if(!empty($infos[0]->MND_FaxNo))
	<tr>
		<td class="tb-left-cell">Fax #: </td>
		<td class="tb-right-cell">{{ $infos[0]->MND_FaxNo }}</td>
	</tr>
	@endif
	@if(!empty($infos[0]->RNT_RentalIssueAmount))
	<tr>
		<td class="tb-left-cell">Rental Issue Amount: </td>
		<td class="tb-right-cell">{{ $infos[0]->RNT_RentalIssueAmount }}</td>
	</tr>
	@endif
	@if(!empty($infos[0]->FLN_FelonyNotes))
	<tr>
		<td class="tb-left-cell">Felony Notes: </td>
		<td class="tb-right-cell">{{ $infos[0]->FLN_FelonyNotes }}</td>
	</tr>
	@endif
	<tr>
		<td colspan=2><div class="marker-read-more">Read more...</div></td>
	</tr>
</table>