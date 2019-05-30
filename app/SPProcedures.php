<?php

namespace App;

trait SPProcedures {

	public $sp_procedures = [

		/**
		 * @see User
		 */
		'sp_ValidateUserAndRole', 'sp_User_iu', 'sp_GetUser', 'sp_GetInputNames', 'sp_GetDropDown', 'sp_getAutoComplete', 'sp_DeleteUser', 'sp_GetAllUser',


		/**
		* @see Listing
		*/
		'sp_Records_iu', 'sp_DeleteRecords', 'sp_MainData_iu', 'sp_Credit_iu', 'sp_Felony_iu', 'sp_Misdemeanor_iu', 'sp_Price_iu', 'sp_RentalIssue_iu', 'sp_Sq_iu', 'sp_GetAllRecords'
			
	];

}