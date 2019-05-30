@extends('layouts.app')
@groupblock('app-header', 'layouts.headers.custom', 'header')

@css("plugins/jQueryUI/jquery-ui.min.css",'selectable-css')
@css("plugins/jQuery/jquery-scrolling-tabs.min.css")
@css("plugins/jQuery/jquery.select-multiple.css")
@css("plugins/TinyColorPicker/tinycolorpicker.css")
@css("plugins/datatables/jquery.dataTables.min.css", 'datatables-css')
@cssblock("Listings.css.styles", "style")
@cssblock('auth.css.styles','support-styles')


@js("plugins/jQueryUI/jquery-ui.min.js")
@js("plugins/jQuery/jquery-scrolling-tabs.min.js")
@js("plugins/jQuery/jquery.select-multiple.js")
@js("plugins/TinyColorPicker/jquery.tinycolorpicker.min.js")
@jsblock("Listings.js.search", "selection_scripts")
@js("plugins/datatables/jquery.dataTables.min.js")
@jsblock("auth.js.support", "support-scripts")


@if (config('googlmapper.async'))
    @js("//maps.googleapis.com/maps/api/js?v=".config('googlmapper.version')."&region=".config('googlmapper.region')."&language=".config('googlmapper.language')."&key=".config('googlmapper.key')."&libraries=places,drawing,spherical,poly,places&callback=initialize_method", ['async'=>true, 'defer'=>true])

@else
    @js("//maps.googleapis.com/maps/api/js?v=".config('googlmapper.version')."&region=".config('googlmapper.region')."&language=".config('googlmapper.language')."&key=".config('googlmapper.key')."&libraries=places,drawing,spherical,poly,places")

@endif

@if (config('googlmapper.cluster'))
    @js("//googlemaps.github.io/js-marker-clusterer/src/markerclusterer.js")

@endif

@if (config('googlmapper.async'))

    <script type="text/javascript">

        var initialize_items = [];

        function initialize_method() {
            initialize_items.forEach(function(item) {
                item.method();
            });
        }

    </script>

@endif

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" class="_token" value="{{ csrf_token() }}">
        	<div class="col-md-3 filter-wrapper">
        		<div class="filters">
        			{!! $filters !!}
        		</div>
                <div class="form-group form-buttons" >
                    <input type="button" class="btn btn-default" id="filters-search-b" value="Search">
                    <input type="button" class="btn btn-default" id="filters-reset-b" value="Clear">
                </div>
        	</div>
            <div class="col-md-9">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#search">Map Search</a></li>
                    <li><a data-toggle="tab" href="#details">Results</a></li>
                </ul>
            </div>
            <div class="col-md-9 tab-content">
            	<div id="search" class="map-wrapper tab-pane fade in active">
                    <div class="map">
    	               {!! $map !!}
                    </div>
                </div>
                <div id="details" class="tab-pane fade">
                    <table id="listings-table" class="display nowrap cell-border stripe hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Property Info</th>
                                <th>Searched Infos</th>
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