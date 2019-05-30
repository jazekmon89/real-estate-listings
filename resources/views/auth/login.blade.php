@extends('layouts.app')
@groupblock('app-header', 'layouts.headers.default')
@jsblock("auth.js.scripts", "login-scripts")

@css("plugins/jQueryUI/jquery-ui.min.css",'selectable-css')
@js("plugins/jQueryUI/jquery-ui.min.js")

@cssblock('auth.css.styles','login-styles')

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
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    @include('flash::message')
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

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
                        {{--
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>
                        --}}
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>
                            </div>
                            <div class="col-md-12">
                                <a class="btn btn-link support req-acc" href="#">
                                    Request access
                                </a>
                                <a class="btn btn-link support req-pass" href="#">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
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
        <input type="hidden" class="problem" name="problem" value="">
        <div class="form-group">
            <label for="problem" class="col-md-4 control-label">Problem</label>
            <div class="col-md-6">
                <select id="problem" class="form-control dropdown" required>
                    <option value=''>Please select</option>
                    <option value='1'>Request access</option>
                    <option value='2'>Forgot password</option>
                    <option value='3'>Others</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="message" class="col-md-4 control-label">Message (Optional)</label>
            <div class="col-md-6">
                <textarea id="message" type="text" class="form-control" name="message"></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12 recaptcha-wrapper"></div>
        </div>
    </form>
</div>
@endsection
