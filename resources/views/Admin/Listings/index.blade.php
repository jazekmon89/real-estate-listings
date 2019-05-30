@extends('layouts.app')
@groupblock('app-header', 'layouts.headers.custom', 'header')

@css("plugins/jQueryUI/jquery-ui.min.css",'selectable-css')
@css("plugins/datatables/jquery.dataTables.min.css", 'datatables-css')
@css("https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css", 'font-awesome')
@cssblock("Admin.Listings.css.styles", "users-style")
@cssblock('auth.css.styles','support-styles')

@js("plugins/jQueryUI/jquery-ui.min.js")
@js("plugins/jQuery/jquery.form.min.js")
@js("plugins/datatables/jquery.dataTables.min.js")
@js("plugins/datatables/dataTables.fixedColumns.min.js")
@jsblock("Admin.Listings.js.scripts", "selection_scripts")
@jsblock("auth.js.support", "support-scripts")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
          <div class="text-left">
               <button class="btn btn-primary" type="button" id="addNewRecords">Add New</button>
               <a href="{{ route('listings.samplecsv') }}" target="_blank" class="btn btn-primary" type="button">Sample CSV</a>
               <a href="{{ route('listings.exportcsv') }}" target="_blank" class="btn btn-primary" type="button">Export</a>
               <button class="btn btn-primary" type="button" id="importRecordCSV">Import</button>
          </div>
            @include('flash::message')
            <table id="listings-table" class="display nowrap cell-border stripe hover" style="width:100%">
                <thead>
                    <tr class="table-heading">
                        <th id="deleteColumn">
                        <button class="btn btn-primary" type="button" id="deleteMultipleRecords" disabled>Delete</button>
                        <input type="hidden" class="checklistCount" value="0">
                        </th>
                        <th>ID</th>
                        <th id="MND_Community">Community</th>
                        <th id="MND_LastUpdated">Last Updated</th>
                        <th id="MND_Status">Active</th>
                        <th id="MND_Management">Management</th>
                        <th id="MND_Address">Address</th>
                        <th id="MND_City">City</th>
                        <th id="MND_Zip">Zip</th>
                        <th id="MND_PhoneNo">Phone #</th>
                        <th id="MND_FaxNo">Fax #</th>
                        <th id="FLN_FelonyCase">Felony Case</th>
                        <th id="FLN_FelonyDUIMonths">Felony DUI(Months)</th>
                        <th id="FLN_FelonyDrugMonths">Felony Drug (Months)</th>
                        <th id="FLN_FelonyMarijuanaMonths">Felony Marijuana (Months)</th>
                        <th id="FLN_FelonyTheftMonths">Felony Theft (Months)</th>
                        <th id="FLN_FelonyWeaponMonths">Felony Weapon (Months)</th>
                        <th id="FLN_FelonyVCAPMonths">Felony VCAP (Months)</th>
                        <th id="FLN_FelonyNotes">Felony (Notes)</th>
                        <th id="MSD_MisdemeanorCase">Misdemeanor Case</th>
                        <th id="MSD_MisdemeanorDUIMonths">Misdemeanor DUI (Months)</th>
                        <th id="MSD_MisdemeanorDrugMonths">Misdemeanor Drug (Months)</th>
                        <th id="MSD_MisdemeanorMarijuanaMonths">Misdemeanor Marijuana (Months)</th>
                        <th id="MSD_MisdemeanorTheftMonths">Misdemeanor Theft (Months)</th>
                        <th id="MSD_MisdemeanorWeaponMonths">Misdemeanor Weapon (Months)</th>
                        <th id="MSD_MisdemeanorVCAPMonths">Misdemeanor VCAP (Months)</th>
                        <th id="MSD_MisdemeanorNotes">Misdemeanor (Notes)</th>
                        <th id="RNT_RentalIssueAgeMonths">Rental Issue Age (Months)</th>
                        <th id="RNT_RentalIssueMax">Rental Issue Max</th>
                        <th id="RNT_RentalIssueAmount">Rental Issue Amount ($)</th>
                        <th id="CRD_CreditScore">Credit Score</th>
                        <th id="CRD_CreditFriendly">Credit Friendly</th>
                        <th id="CRD_CreditBureau">Credit Bureau (TU,EQ,EX,3)</th>
                        <th id="CRD_CreditSystem">Credit System</th>
                        <th id="MND_OpenBankruptcy">Open Bankruptcy</th>
                        <th id="MND_DisBankruptcyAgeMonths">Dis Bankruptcy Age (Months)</th>
                        <th id="MND_IncomeRequirement">Income Requirement</th>
                        <th id="MND_FoodStampsYesNo">Food Stamps Yes/No</th>
                        <th id="MND_CompanyLetterheadYesNo">Company Letterhead Yes/No</th>
                        <th id="MND_NonLetterHeadLetterYesNo">Non-Letterhead Letter Yes/No</th>
                        <th id="MND_LengthofJobMonths">Length of Job (Months)</th>
                        <th id="MND_Section8">Section 8</th>
                        <th id="MND_HOMINC">HOM INC</th>
                        <th id="MND_BiltmoreProperties">Biltmore Properties</th>
                        <th id="MND_RapidRehousing">Rapid Rehousing</th>
                        <th id="MND_HUDVASH">HUD VASH</th>
                        <th id="MND_Visa">Visa</th>
                        <th id="MND_NoSSNo">No SS#</th>
                        <th id="MND_MexID">Mex ID</th>
                        <th id="MND_ITINNo">ITIN#</th>
                        <th id="MND_WD">W/D</th>
                        <th id="MND_SXSIncluded">SXS Included</th>
                        <th id="MND_StackIncluded">Stack Included</th>
                        <th id="MND_SXSHookup">SXS Hookup</th>
                        <th id="MND_StackHookup">Stack Hookup</th>
                        <th id="MND_OnSiteFacility">On Site Facility</th>
                        <th id="MND_LaundryNotes">Laundry Notes</th>
                        <th id="MND_PetWeightLimit">Pet Weight Limit</th>
                        <th id="MND_RestrictedBreed">Restricted Breed?</th>
                        <th id="MND_NumberOfPetMax">Number of Pets Max</th>
                        <th id="MND_Utilities">Utilities</th>
                        <th id="MND_APS">APS</th>
                        <th id="MND_SRP">SRP</th>
                        <th id="MND_INCL">INCL</th>
                        <th id="MND_GAS">GAS</th>
                        <th id="PRC_SPriceRANGE">S - Price RANGE</th>
                        <th id="PRC_SPriceLOW">S - Price LOW</th>
                        <th id="PRC_SPriceHIGH">S - Price HIGH</th>
                        <th id="PRC_1X1PriceRANGE">1X1 - Price RANGE</th>
                        <th id="PRC_1X1PriceLOW">1X1 Price LOW</th>
                        <th id="PRC_1X1PriceHIGH">1X1 Price HIGH</th>
                        <th id="PRC_1X1DENPrice">1X1 + DEN - Price</th>
                        <th id="PRC_2X1PriceRANGE">2X1 - Price RANGE</th>
                        <th id="PRC_2X1PriceLOW">2X1 - Price LOW</th>
                        <th id="PRC_2X1PriceHIGH">2X1 - Price HIGH</th>
                        <th id="PRC_2X2PriceRANGE">2X2 - Price RANGE</th>
                        <th id="PRC_2X2PriceLOW">2X2 - Price LOW</th>
                        <th id="PRC_2X2PriceHIGH">2X2 - Price HIGH</th>
                        <th id="PRC_2BRDENPrice">2BR + DEN - Price</th>
                        <th id="PRC_3X1Price">3X1 - Price</th>
                        <th id="PRC_3X2PriceRANGE">3X2 - Price RANGE</th>
                        <th id="PRC_3X2PriceLOW">3X2 - Price LOW</th>
                        <th id="PRC_3X2PriceHIGH">3X2 - Price HIGH</th>
                        <th id="PRC_4X2Price">4X2 - Price</th>
                        <th id="SQ_SqS">S - Sq'</th>
                        <th id="SQ_Sq1X1">1X1 - Sq'</th>
                        <th id="SQ_Sq1X1Den">1X1 + DEN - Sq'</th>
                        <th id="SQ_Sq2X1">2X1 - Sq'</th>
                        <th id="SQ_Sq2X2">2X2 - Sq'</th>
                        <th id="SQ_Sq2BRDEN">2BR + DEN - Sq'</th>
                        <th id="SQ_Sq3X1">3X1 - sq'</th>
                        <th id="SQ_Sq3X2">3X2 - Sq'</th>
                        <th id="SQ_Sq4X2">4X2 - Sq'</th>
                        <th id="MND_Garage">Garage</th>
                        <th id="MND_Fitness">Fitness</th>
                        <th id="MND_Handicap">Handicap</th>
                        <th id="MND_Gated">Gated</th>
                        <th id="MND_Furnished">Furnished</th>
                        <th id="MND_CableIncl">Cable Incl</th>
                        <th id="MND_Sublevel">Sublevel</th>
                        <th id="MND_Occupant">Occupant</th>
                        <th id="MND_ShortestTerm">Shortest Term (months)</th>
                        <th id="LOC_Latitude">Latitude</th>
                        <th id="LOC_Longitude">Longitude</th>
                        <th id="LOC_Confidence">Location Confidence</th>
                        <th></th>
                </thead>
            </table>
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
<div id="importRecordCSVModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Import Records</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{ Form::open(array('route'=>'listings.uploadcsv', 'files'=>true, 'id'=>'importCsvForm', 'method'=>'post')) }}
                        <div class="col-md-12">
                            <p class="text-left">CSV File</p>
                        </div>
                        <div class="col-lg-12">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button id="browseCsvBtn" class="btn btn-default" type="button"><span class="glyphicon glyphicon-folder-open"></span> Browse</button>
                                </span>
                                <input id="csvFileInput" type="text" class="form-control" readonly="">
                                <input id="csvFile" type="file" name="csvFile" class="form-control hidden">
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                        <div class="marg-top col-md-12">
                            <button id="submitCsvId" type="submit" class="btn btn-default pull-right" disabled>Proceed</button>
                        </div>
                    {{ Form::close() }}
                </div>
                <button class="import-container marg-top btn btn-lg btn-warning hidden">
                    <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> 
                    <span class='load-text'>Importing...</span>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span class="sr-only">0%</span>
                        </div>
                    </div>
                </button>
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