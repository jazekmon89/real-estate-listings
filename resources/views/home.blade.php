@extends('layouts.app')
@groupblock('app-header', 'layouts.headers.default', 'header')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                    <br />
                    <br />
                    <div class="col-md-4 text-center rounded">
                        <a href="{{ route('users.page') }}">Users</a>
                    </div>
                    @if( Auth::user()->is_admin )
                    <div class="col-md-4 text-center rounded">
                        <a href="{{ route('listings.manage.page') }}">Manage Records</a>
                    </div>
                    @endif
                    <div class="col-md-4 text-center rounded">
                        <a href="{{ route('listings.mapsearch.page') }}">Map Search</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
