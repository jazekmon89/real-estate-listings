@extends('layouts.error')
@groupblock('app-header', 'layouts.headers.custom')

@section('content')
<div class="container text-center" id="error">
  <svg height="100" width="100">
    <polygon points="50,25 17,80 82,80" stroke-linejoin="round" style="fill:none;stroke:#ff8a00;stroke-width:8" />
    <text x="42" y="74" fill="#ff8a00" font-family="sans-serif" font-weight="900" font-size="42px">!</text>
  </svg>
 <div class="row">
    <div class="col-md-12">
      <div class="main-icon text-warning"><span class="uxicon uxicon-alert"></span></div>
        <h1>Internal Server Error (404 error)</h1>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 col-md-push-3 no-left">
      <p class="lead">If you think what you're looking for should be here, please contact the site owner.</p>
    </div>
  </div>
</div>
@endsection