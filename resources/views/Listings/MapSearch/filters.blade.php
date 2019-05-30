<input id="searchTextField" type="text" placeholder="Search address">
<div id="polygonsWrapper"></div>
{!! Form::open(['url' => ['/'],'id'=>'search-form']) !!}
	<div class="form-group" >
		<input type="text" class="hidden array">
		<input type="text" class="hidden infoWindowSearch">
	@php $counter = 0; @endphp
	@foreach($new_filters as $m=>$n)
		<div id="accordion-{{ $counter }}" class="section-wrapper">
			<input type="checkbox" class="accordion-section-header-checkbox">
			<h4>{{ $m }}</h4>
			<div>
			@foreach($n as $k=>$j)
				@if(is_integer($k) && $j->Active)
					@php 
						$unq_nme = !empty($j->UniqueName)?'.'.$j->UniqueName:'';
						//combined-fields-pilot
						$combined_field_nums = !empty($j->CombinedFieldsID)?explode('-',$j->CombinedFieldsID):[];
						$combined_field_parent_class = count($combined_field_nums)>0?(intval($combined_field_nums[1])==1?'combined-fields-parent-pilot':'combined-fields-parent-sub'):'';
						$combined_field_class = str_replace('-parent','',$combined_field_parent_class);
						$combined_fields_id = count($combined_field_nums)>0?'data-combined-fid='.$combined_field_nums[0]:'';
					@endphp
					<div class="section-item-wrapper {{ $combined_field_parent_class }}" section-name="{{ $j->tableName }}" unique-name="{{ $j->UniqueName }}">
					{{ Form::label($j->tableName.'.'.$j->fieldName, $j->InputName, ['class'=>'labels']) }}
					@if($j->InputTypeName == 'dropdown')
						<select name="{{ $j->tableName }}.{{ $j->fieldName }}{{ $unq_nme }}" class="form-control dropdown {{ $j->tableName }}_{{ $j->fieldName }} {{ $combined_field_class }}" {{ $combined_fields_id }}>
							@foreach($j->options as $l=>$m)
							<option value="{{ $l }}">{{ $m }}</option>
							@endforeach
						</select>
					@elseif($j->InputTypeName == 'selectmultioption')
						<select multiple="multiple" name="{{ $j->tableName }}.{{ $j->fieldName }}{{ $unq_nme }}" class="form-control dropdown selectmultioption {{ $j->tableName }}_{{ $j->fieldName }} {{ $combined_field_class }}" {{ $combined_fields_id }}>
							@foreach($j->options as $l=>$m)
							    <option value="{{ $l }}">{{ strlen(strval($m))==0?'&nbsp;':$m }}</option>
							@endforeach
						</select>
					@elseif($j->InputTypeName == 'autocomplete')
						<input type="text" name="{{ $j->tableName }}.{{ $j->fieldName }}{{ $unq_nme }}" class="form-control autocomplete {{ $j->tableName }}_{{ $j->fieldName }} {{ $combined_field_class }}" {{ $combined_fields_id }}>
					@elseif($j->InputTypeName == 'range')
						<input type="number" name="{{ $j->tableName }}.{{ $j->fieldName }}{{ $unq_nme }}" class="form-control range min {{ $combined_field_class }}" placeholder="min" min="0" max="{{ $j->MaxAmount }}" {{ $combined_fields_id }}><input type="number" name="{{ $j->tableName }}.{{ $j->fieldName }}{{ $unq_nme }}" class="form-control range max {{ $j->tableName }}_{{ $j->fieldName }} {{ $combined_field_class }}" placeholder="max" min="0" max="{{ $j->MaxAmount }}" {{ $combined_fields_id }}>
					@elseif($j->InputTypeName == 'number')
						<input type="number" name="{{ $j->tableName }}.{{ $j->fieldName }}{{ $unq_nme }}" class="form-control number {{ $j->tableName }}_{{ $j->fieldName }} {{ $combined_field_class }}" field-name="{{ $j->fieldName }}" min="0" max="{{ $j->MaxAmount }}" placeholder="{{ $j->Placeholder }}" {{ $combined_fields_id }}>
					@elseif($j->InputTypeName == 'dropdownshow')
						<select name="{{ $j->tableName }}.{{ $j->fieldName }}{{ $unq_nme }}" size="3" class="form-control dropdown dropdownshow {{ $j->tableName }}_{{ $j->fieldName }} {{ $combined_field_class }}" {{ $combined_fields_id }}>
							@foreach($j->options as $l=>$m)
							<option value="{{ $l }}">{{ $m }}</option>
							@endforeach
						</select>
					@elseif($j->InputTypeName == 'rangemultiselect')
						<input type="number" name="{{ $j->tableName }}.{{ $j->fieldName }}{{ $unq_nme }}" class="form-control rangemax {{ $combined_field_class }}" field-name="{{ $j->fieldName }}" min="0" max="{{ $j->MaxAmount }}" {{ $combined_fields_id }}>
						<select multiple="multiple" name="{{ $j->tableName }}.{{ $j->fieldName }}{{ $unq_nme }}" class="form-control dropdown rangemultiselect {{ $j->tableName }}_{{ $j->fieldName }} {{ $combined_field_class }}" {{ $combined_fields_id }}>
						</select>
					@endif
					</div>
				@endif
			@endforeach
			</div>
		</div>
		@php $counter++; @endphp
	@endforeach
	</div>

{!! Form::close() !!}