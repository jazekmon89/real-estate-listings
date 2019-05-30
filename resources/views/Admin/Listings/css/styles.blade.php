<style>
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
.dataTables_processing {
	padding-bottom: 20px;
	height: 60px !important;
}
.dataTables_scrollBody {
	height: 70vh;
}

th {
	background: #eeeeee;
}

.dataTables_processing {
	z-index: 1000;
}

#listings-table_wrapper {
	padding-top: 10px;
}

.glyphicon-refresh-animate {
	-animation: spin .7s infinite linear;
	-webkit-animation: spin2 .7s infinite linear;
}
@-webkit-keyframes spin2 {
	from { -webkit-transform: rotate(0deg);}
	to { -webkit-transform: rotate(360deg);}
}
@keyframes spin {
	from { transform: scale(1) rotate(0deg);}
	to { transform: scale(1) rotate(360deg);}
}
.progress {
	width: 400px;
	position: relative;
}
.marg-top {
	margin-top: 10px;
}
</style>