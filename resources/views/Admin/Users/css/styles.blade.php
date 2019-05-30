<style>
#users-table_paginate, #users-table_filter {
	text-align: right;
}
#users-table_paginate .paginate_button {
	padding: 5px;
}
#users-table_filter, #users-table_length {
	width: 50%;
	display: inline-block;
}
#users-table_length {
	text-align: left;
}
.dataTables_processing {
	padding-bottom: 20px;
	height: 60px !important;
}
#users-table tr td a:hover {
	cursor: pointer;
}
#users-table_wrapper {
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
</style>