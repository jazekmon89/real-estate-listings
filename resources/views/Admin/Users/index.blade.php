@extends('layouts.app')
@groupblock('app-header', 'layouts.headers.custom', 'header')

@css("plugins/jQueryUI/jquery-ui.min.css",'selectable-css')
@css("plugins/datatables/jquery.dataTables.min.css", 'datatables-css')
@cssblock("Admin.Users.css.styles", "users-style")
@cssblock('auth.css.styles','support-styles')

@js("plugins/jQueryUI/jquery-ui.min.js")
@js("plugins/datatables/jquery.dataTables.min.js")
@jsblock("Admin.Users.js.scripts", "selection_scripts", ['roles'=>$roles])
@jsblock("auth.js.support", "support-scripts")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel-body">
                <div class="text-left">
                    <button class="btn btn-primary" type="button" id="addNewUser">Add New</button>
                </div>
                @include('flash::message')
                <table id="users-table" class="cell-border stripe hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Last Login Date</th>
                            <th>Remember Token</th>
                            <th>Role</th>
                            <th>Activated</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div id="ItemPopup" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <!--<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>-->
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
