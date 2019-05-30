<p>There's {{ (ctype_alpha($_request) && preg_match('/^[aeiou]/i', $_request)?'an':'a') }} {{ $_request }} from {{ $customer }} with an email {{ $customer_email }}.</p>
@if(!empty($_message))
<p>The message contains:</p>
<p>{{ $_message }}</p>
@endif