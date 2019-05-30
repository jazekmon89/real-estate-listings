@if(Auth::user()->is_admin)
	<img src='{{ $img_thumb }}' /><form enctype='multipart/form-data' method='POST' action=''><input type='file' class='form-control' name='image' accept='image/*' /><input type='submit' value='Change' disabled/></form>
@elseif(!empty($img_thumb))
	<img src='{{ $img_thumb }}' />
@else
	<img src='images/Small.jpg' />
@endif