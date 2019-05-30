@extends('layouts.app')
@groupblock('app-header', 'layouts.headers.custom', 'header')

@css("plugins/jQueryUI/jquery-ui.min.css",'selectable-css')
@css("plugins/datatables/jquery.dataTables.min.css", 'datatables-css')
@cssblock('auth.css.styles','support-styles')

@js("plugins/jQueryUI/jquery-ui.min.js")
@js("plugins/datatables/jquery.dataTables.min.js")
@jsblock("Admin.Listings.js.scripts", "selection_scripts")
@jsblock("Admin.Listings.css.styles", "users-style")
@jsblock("auth.js.support", "support-scripts")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
        	<div class="col-md-12">
	            <div class="panel-body">
	                @include('flash::message')
	                <table id="listings-table" class="cell-border stripe hover" style="width:100%">
	                    <thead>
	                        <tr>
	                        	<th>Last Updated</th>
	                        	<th>Shortest Term</th>
	                        	<th>Status</th>
	                        	<th>Community</th>
	                        	<th>Address	City</th>
	                        	<th>Zip</th>
	                        	<th>Phone #</th>
	                        	<th>Fax #</th>
	                        	<th>Felony Case</th>
	                        	<th>Felony DUI (Months)</th>
	                        	<th>Felony Drug (Months)</th>
	                        	<th>Felony Marijuana (Months)</th>
	                        	<th>Felony Theft (Months)</th>
	                        	<th>Felony Weapon (Months)</th>
	                        	<th>Felony VCAP (Months)</th>
	                        	<th>Felony (Notes)</th>
	                        	<th>Misdemeanor Case</th>
	                        	<th>Misdemeanor DUI (Months)</th>
	                        	<th>Misdemeanor Drug (Months)</th>
	                        	<th>Misdemeanor Marijuana (Months)</th>
	                        	<th>Misdemeanor Theft (Months)</th>
	                        	<th>Misdemeanor Weapon (Months)</th>
	                        	<th>Misdemeanor VCAP (Months)</th>
	                        	<th>Misdemeanor (Notes)</th>
	                        	<th>Rental Issue Age (Months)</th>
	                        	<th>Rental Issue Max</th>
	                        	<th>Rental Issue Amount ($)</th>
	                        	<th>Credit Score</th>
	                        	<th>Credit Bureau (TU,EQ,EX,3)</th>
	                        	<th>Credit System</th>
	                        	<th>W/D</th>
	                        	<th>SXS Included</th>
	                        	<th>Stack Included</th>
	                        	<th>SXS Hookup</th>
	                        	<th>Stack Hookup</th>
	                        	<th>On Site Facility</th>
	                        	<th>Laundry Notes</th>
	                        	<th>Pet Weight Limit</th>
	                        	<th>Restricted Breed?</th>
	                        	<th>Number of Pets Max</th>
	                        	<th>Utilitities	Income Requirement</th>
	                        	<th>Food Stamps Yes/No</th>
	                        	<th>Company Letterhead Yes/No</th>
	                        	<th>Non-Letterhead Letter Yes/No</th>
	                        	<th>Length of Job (Months)</th>
	                        	<th>Section 8</th>
	                        	<th>HOM INC</th>
	                        	<th>Biltmore Properties</th>
	                        	<th>Rapid Rehousing</th>
	                        	<th>HUD VASH</th>
	                        	<th>Management</th>
	                        	<th>S - Price</th>
	                        	<th>1X1 - Price</th>
	                        	<th>1X1 + DEN - Price</th>
	                        	<th>2X1 - Price</th>
	                        	<th>2X2 - Price</th>
	                        	<th>2BR + DEN - Price</th>
	                        	<th>3X1 - Price</th>
	                        	<th>3X2 - Price</th>
	                        	<th>4X2 - Price</th>
	                        	<th>S - Sq'</th>
	                        	<th>1X1 - Sq'</th>
	                        	<th>1X1 + DEN - Sq'</th>
	                        	<th>2X1 - Sq'</th>
	                        	<th>2X2 - Sq'</th>
	                        	<th>2BR + DEN - Sq'</th>
	                        	<th>3X1 - sq'</th>
	                        	<th>3X2 - Sq'</th>
	                        	<th>4X2 - Sq'</th>
	                        	<th>Garage</th>
	                        	<th>Open Bankruptcy</th>
	                        	<th>Dis Bankruptcy Age (Months)</th>
	                        	<th>Fitness	Handicap</th>
	                        	<th>No SS#</th>
	                        	<th>Mex ID</th>
	                        	<th>Visa</th>
	                        	<th>ITIN#</th>
	                        	<th>Gated</th>
	                        	<th>Furnished</th>
	                        	<th>Cable Incl</th>
	                        	<th>Sublevel</th>
	                        	<th>Occupant</th>
	                        </tr>
	                    </thead>
	                </table>
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