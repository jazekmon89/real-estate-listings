@extends('layouts.app')
@groupblock('app-header', 'layouts.headers.custom')
@jsblock("auth.js.support", "support-scripts")

@css("plugins/jQueryUI/jquery-ui.min.css",'selectable-css')
@js("plugins/jQueryUI/jquery-ui.min.js")

@cssblock('auth.css.styles','support-styles')

@section('content')
<style>
    @media (max-width: 992px) {
        form .form-group .col-md-6 {
            width: 100% !important;
        }
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Password Request</div>
                @include('flash::message')
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('password.request.activate') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="requestHash" value="{{ $requestHash }}">
                        <input type="hidden" name="email" value="{{ $email }}">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password_confirmation" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>                   
    </div>
<div id="dialog">
    <form id="support-form" class="form-horizontal" role="form" method="POST" action="{{ route('support') }}">
        {{ csrf_field() }}

        <div class="form-group">
            <label for="firstname" class="col-md-4 control-label">First name</label>
            <div class="col-md-6">
                <input id="firstname" type="text" class="form-control" name="firstname" required>
            </div>
        </div>

        <div class="form-group">
            <label for="lastname" class="col-md-4 control-label">Last name</label>
            <div class="col-md-6">
                <input id="lastname" type="text" class="form-control" name="lastname" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="col-md-4 control-label">E-Mail Address</label>
            <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email" value="" required>
            </div>
        </div>
        <input type="hidden" class="problem" name="problem" value="3">

        <div class="form-group">
            <label for="message" class="col-md-4 control-label">Message</label>
            <div class="col-md-6">
                <textarea id="message" type="text" class="form-control" name="message" required></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12 recaptcha-wrapper"></div>
        </div>
    </form>
</div>
@endsection