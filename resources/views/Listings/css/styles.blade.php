<style>
.range {
	width: 50%;
	display: inline-block;
}
.labels {
	width: 100%;
	text-align: left;
	padding-top:10px;
}
.ui-widget-content {
	text-align: left;
}
.map-wrapper {
	/*height: 400px;*/
	height: 75vh;
}
.filter-wrapper {
	height: 78vh;
}
.map {
	height: 100%;
}
.form-buttons {
	padding-top: 5px;
}
.filters {
	padding-right: 5px;
	height: 91%;
	overflow-y: scroll;
}
.section-wrapper.ui-accordion .ui-accordion-header/*, .group-wrapper.ui-accordion .ui-accordion-header*/ {
	text-align: left;
	padding: .5em .5em .5em 1.8em;
}
/*.form-group .group-wrapper.ui-accordion,*/ .form-group .section-wrapper.ui-accordion {
	position: relative;
}
.ui-accordion .accordion-section-header-checkbox/*, .ui-accordion .accordion-group-header-checkbox*/ {
    display: inline-block;
    position: absolute;
    z-index: 1;
    left: 11px;
    top: 2px;
}
.ui-accordion .accordion-section-header-checkbox {

}
.ui-accordion .ui-accordion-content {
	padding: 1em 1.2em;
}
.marker-loading {
	height: 18px;
	width: 18px;
	background: url({{ url('/images/marker-loading.gif') }});
}
.marker-read-more:hover {
	cursor: pointer;
}
.marker-read-more {
	color: #0000ff;
}

#searchTextField {
	width: 100%;
	font-size: 10px;
}


#listings-table_paginate, #listings-table_filter {
	text-align: right;
}
#listings-table_paginate .paginate_button {
	padding: 5px;
}
#listings-table_filter, #listings-table_length {
	width: 50%;
	display: inline-block;
}
#listings-table_length {
	text-align: left;
}
#listings-table tbody .image-upload {
	margin: 0 auto;
}
#listings-table tbody .search-item-infos .col-md-6 {
	min-height: 22.4px;
	display: block;
	white-space: normal;
	font-size: 10px;
}
#listings-table tbody .search-item-infos .col-md-6.text-right {
	padding-right: 3px;
}
#listings-table tbody .search-item-infos .col-md-6.text-left {
	padding-left: 3px;
}
#listings-table tbody .search-item-infos:nth-child(1) > .col-md-6 {
	border: 1px solid black;
}
#listings-table tbody .search-item-infos:nth-child(1) > .col-md-6 .col-md-6.text-right {
	border-right: 1px solid black;
}
.dataTables_processing {
	padding-bottom: 20px;
	height: 60px !important;
}
.dataTables_scrollBody {
	height: 60vh;
}
.image-upload {
	width: 211px;
}
.image-upload input[type='submit'] {
	width: 100%;
}
.nav-tabs {
	margin-bottom: 8px;
}
.nav-tabs>li>a {
	padding: 4px 15px !important;
}
#listings-table_processing {
	z-index: 1;
}
.tb-left-cell {
	padding-right: 10px;
	text-align: left;
}
.tb-right-cell {
	text-align: left;
	width: 200px;
}
#polygonsWrapper {
	text-align: left;
}
.polygon-items {
	display: inline-block;
	background: #A4A29F;
	color: #fff;
	margin: 1px 2px;
    padding: 1px 5px;
    font-size: 9px;
}
.polygon-items span:hover {
	cursor: pointer;
}
/*#listings-table_wrapper .dataTables_scrollHead tr th:first-child, #listings-table_wrapper .dataTables_scrollBody tr td:first-child {
	display: none;
}*/
.ms-container .ms-list {
	height: auto !important;
	max-height: 200px;
}
.search-item-infos .fields-container {
	padding-left: 3px;
    padding-right: 3px;
}
.search-item-infos .fields-container .text-right.fields-wrapper {
	padding-left: 0px;
}
.search-item-infos .fields-container .text-left.fields-wrapper {
	padding-right: 0px;
}
.dropdownshow {
	overflow: auto;
}
.dataTables_filter {
	opacity: 0;
}
.section-wrapper, .section-wrapper label, .section-wrapper span, .section-wrapper h4 {
	font-size: 10px;
}
.section-item-wrapper .ms-container {
	width: 100%;
}
#listings-table tbody tr td {
	vertical-align: top;
}
.combined-fields-parent-sub, .combined-fields-sub {
	display: none;
} 
</style>