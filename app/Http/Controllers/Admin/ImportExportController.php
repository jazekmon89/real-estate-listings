<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Flash;
use Storage;
use App\Listing;
use File;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;

class ImportExportController extends Controller
{

    protected $listing = null;
    protected $headers_fieldnames = [
        "Community"=>"MND_Community",
        "Last Updated"=>"MND_LastUpdated",
        "ACTIVE"=>"MND_Status",
        "Management"=>"MND_Management",
        "Address"=>"MND_Address",
        "City"=>"MND_City",
        "Zip"=>"MND_Zip",
        "Phone #"=>"MND_PhoneNo",
        "Fax #"=>"MND_FaxNo",
        "Felony Case"=>"FLN_FelonyCase",
        "Felony DUI (Months)"=>"FLN_FelonyDUIMonths",
        "Felony Drug (Months)"=>"FLN_FelonyDrugMonths",
        "Felony Marijuana (Months)"=>"FLN_FelonyMarijuanaMonths",
        "Felony Theft (Months)"=>"FLN_FelonyTheftMonths",
        "Felony Weapon (Months)"=>"FLN_FelonyWeaponMonths",
        "Felony VCAP (Months)"=>"FLN_FelonyVCAPMonths",
        "Felony (Notes)"=>"FLN_FelonyNotes",
        "Misdemeanor Case"=>"MSD_MisdemeanorCase",
        "Misdemeanor DUI (Months)"=>"MSD_MisdemeanorDUIMonths",
        "Misdemeanor Drug (Months)"=>"MSD_MisdemeanorDrugMonths",
        "Misdemeanor Marijuana (Months)"=>"MSD_MisdemeanorMarijuanaMonths",
        "Misdemeanor Theft (Months)"=>"MSD_MisdemeanorTheftMonths",
        "Misdemeanor Weapon (Months)"=>"MSD_MisdemeanorWeaponMonths",
        "Misdemeanor VCAP (Months)"=>"MSD_MisdemeanorVCAPMonths",
        "Misdemeanor (Notes)"=>"MSD_MisdemeanorNotes",
        "Rental Issue Age (Months)"=>"RNT_RentalIssueAgeMonths",
        "Rental Issue Max"=>"RNT_RentalIssueMax",
        "Rental Issue Amount ($)"=>"RNT_RentalIssueAmount",
        "Credit Score"=>"CRD_CreditScore",
        "Credit Friendly"=>"CRD_CreditFriendly",
        "Credit Bureau (TU,EQ,EX,3)"=>"CRD_CreditBureau",
        "Credit System"=>"CRD_CreditSystem",
        "Open Bankruptcy"=>"MND_OpenBankruptcy",
        "Dis Bankruptcy Age (Months)"=>"MND_DisBankruptcyAgeMonths",
        "Income Requirement"=>"MND_IncomeRequirement",
        "Food Stamps Yes/No"=>"MND_FoodStampsYesNo",
        "Company Letterhead Yes/No"=>"MND_CompanyLetterHeadYesNo",
        "Non-Letterhead Letter Yes/No"=>"MND_NonLetterHeadLetterYesNo",
        "Length of Job (Months)"=>"MND_LengthofJobMonths",
        "Section 8"=>"MND_Section8",
        "HOM INC"=>"MND_HOMINC",
        "Biltmore Properties"=>"MND_BiltmoreProperties",
        "Rapid Rehousing"=>"MND_RapidRehousing",
        "HUD VASH"=>"MND_HUDVASH",
        "VISA"=>"MND_Visa",
        "No SS#"=>"MND_NoSSNo",
        "Mex ID"=>"MND_MexID",
        "ITIN#"=>"MND_ITINNo",
        "W/D"=>"MND_WD",
        "SXS Included"=>"MND_SXSIncluded",
        "Stack Included"=>"MND_StackIncluded",
        "SXS Hookup"=>"MND_SXSHookup",
        "Stack Hookup"=>"MND_StackHookUp",
        "On Site Facility"=>"MND_OnSiteFacility",
        "Laundry Notes"=>"MND_LaundryNotes",
        "Pet Weight Limit"=>"MND_PetWeightLimit",
        "Restricted Breed?"=>"MND_RestrictedBreed",
        "Number of Pets Max"=>"MND_NumberOfPetMax",
        "Utilitities"=>"MND_Utilities",
        "APS"=>"MND_APS",
        "SRP"=>"MND_SRP",
        "INCL"=>"MND_INCL",
        "GAS"=>"MND_GAS",
        "S - Price RANGE"=>"PRC_SPriceRANGE",
        "S - Price LOW"=>"PRC_SPriceLOW",
        "S - Price HIGH"=>"PRC_SPriceHIGH",
        "1X1 - Price RANGE"=>"PRC_1X1PriceRANGE",
        "1X1 Price LOW"=>"PRC_1X1PriceLOW",
        "1X1 Price HIGH"=>"PRC_1X1PriceHIGH",
        "1X1 + DEN - Price"=>"PRC_1X1DENPrice",
        "2X1 - Price RANGE"=>"PRC_2X1PriceRANGE",
        "2X1 - Price LOW"=>"PRC_2X1PriceLOW",
        "2X1 - Price HIGH"=>"PRC_2X1PriceHIGH",
        "2X2 - Price RANGE"=>"PRC_2X2PriceRANGE",
        "2X2 - Price LOW"=>"PRC_2X2PriceLOW",
        "2X2 - Price HIGH"=>"PRC_2X2PriceHIGH",
        "2BR + DEN - Price"=>"PRC_2BRDENPrice",
        "3X1 - Price"=>"PRC_3X1Price",
        "3X2 - Price RANGE"=>"PRC_3X2PriceRANGE",
        "3X2 - Price LOW"=>"PRC_3X2PriceLOW",
        "3X2 - Price HIGH"=>"PRC_3X2PriceHIGH",
        "4X2 - Price"=>"PRC_4X2Price",
        "S - Sq'"=>"SQ_SqS",
        "1X1 - Sq'"=>"SQ_Sq1X1",
        "1X1 + DEN - Sq'"=>"SQ_Sq1X1DEN",
        "2X1 - Sq'"=>"SQ_Sq2X1",
        "2X2 - Sq'"=>"SQ_Sq2X2",
        "2BR + DEN - Sq'"=>"SQ_Sq2BRDEN",
        "3X1 - sq'"=>"SQ_Sq3X1",
        "3X2 - Sq'"=>"SQ_Sq3X2",
        "4X2 - Sq'"=>"SQ_Sq4X2",
        "Garage"=>"MND_Garage",
        "Fitness"=>"MND_Fitness",
        "Handicap"=>"MND_Handicap",
        "Gated"=>"MND_Gated",
        "Furnished"=>"MND_Furnished",
        "Cable Incl"=>"MND_CableIncl",
        "Sublevel"=>"MND_Sublevel",
        "Occupant"=>"MND_Occupant",
        "Shortest Term (months)"=>"MND_ShortestTerm",
        "Latitude"=>"LOC_Latitude",
        "Longitude"=>"LOC_Longitude"
    ];
    protected $static_headers = [
        "Community",
        "Last Updated",
        "ACTIVE",
        "Management",
        "Address",
        "City",
        "Zip",
        "Phone #",
        "Fax #",
        "Felony Case",
        "Felony DUI (Months)",
        "Felony Drug (Months)",
        "Felony Marijuana (Months)",
        "Felony Theft (Months)",
        "Felony Weapon (Months)",
        "Felony VCAP (Months)",
        "Felony (Notes)",
        "Misdemeanor Case",
        "Misdemeanor DUI (Months)",
        "Misdemeanor Drug (Months)",
        "Misdemeanor Marijuana (Months)",
        "Misdemeanor Theft (Months)",
        "Misdemeanor Weapon (Months)",
        "Misdemeanor VCAP (Months)",
        "Misdemeanor (Notes)",
        "Rental Issue Age (Months)",
        "Rental Issue Max",
        "Rental Issue Amount ($)",
        "Credit Score",
        "Credit Friendly",
        "Credit Bureau (TU,EQ,EX,3)",
        "Credit System",
        "Open Bankruptcy",
        "Dis Bankruptcy Age (Months)",
        "Income Requirement",
        "Food Stamps Yes/No",
        "Company Letterhead Yes/No",
        "Non-Letterhead Letter Yes/No",
        "Length of Job (Months)",
        "Section 8",
        "HOM INC",
        "Biltmore Properties",
        "Rapid Rehousing",
        "HUD VASH",
        "VISA",
        "No SS#",
        "Mex ID",
        "ITIN#",
        "W/D",
        "SXS Included",
        "Stack Included",
        "SXS Hookup",
        "Stack Hookup",
        "On Site Facility",
        "Laundry Notes",
        "Pet Weight Limit",
        "Restricted Breed?",
        "Number of Pets Max",
        "Utilitities",
        "APS",
        "SRP",
        "INCL",
        "GAS",
        "S - Price RANGE",
        "S - Price LOW",
        "S - Price HIGH",
        "1X1 - Price RANGE",
        "1X1 Price LOW",
        "1X1 Price HIGH",
        "1X1 + DEN - Price",
        "2X1 - Price RANGE",
        "2X1 - Price LOW",
        "2X1 - Price HIGH",
        "2X2 - Price RANGE",
        "2X2 - Price LOW",
        "2X2 - Price HIGH",
        "2BR + DEN - Price",
        "3X1 - Price",
        "3X2 - Price RANGE",
        "3X2 - Price LOW",
        "3X2 - Price HIGH",
        "4X2 - Price",
        "S - Sq'",
        "1X1 - Sq'",
        "1X1 + DEN - Sq'",
        "2X1 - Sq'",
        "2X2 - Sq'",
        "2BR + DEN - Sq'",
        "3X1 - sq'",
        "3X2 - Sq'",
        "4X2 - Sq'",
        "Garage",
        "Fitness",
        "Handicap",
        "Gated",
        "Furnished",
        "Cable Incl",
        "Sublevel",
        "Occupant",
        "Shortest Term (months)",
        "Latitude",
        "Longitude"
    ];
    protected $limit = 50;
    protected $file_headers = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->listing = new Listing;
        $this->middleware('adminaccess');
    }

    public function getSampleCSVWithHeaders(){
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=listing.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, $this->static_headers);
    }

    private function fixEncoding(&$data){
        foreach($data as $k=>$i){
            foreach($i as $h=>$j){
                $data[$k]->$h = utf8_decode($j);
            }
        }
    }

    public function export(){
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=listing'.date('YmdHis').'.csv');
        $output = fopen('php://output', 'w');
        $rows = DB::table('MainData')->leftJoin('Felony','MainData.MND_ID','Felony.FLN_MND_ID')->leftJoin('Credit','MainData.MND_ID','Credit.CRD_MND_ID')->leftJoin('Price','MainData.MND_ID','Price.PRC_MND_ID')->leftJoin('RentalIssue','MainData.MND_ID','RentalIssue.RNT_MND_ID')->leftJoin('Misdemeanor','MainData.MND_ID','Misdemeanor.MSD_MND_ID')->leftJoin('Sq','MainData.MND_ID','Sq.SQ_MND_ID')->leftJoin('Location','MainData.MND_ID','Location.LOC_MND_ID')->select(
                            "MainData.MND_Community",
                            "MainData.MND_LastUpdated",
                            "MainData.MND_Status",
                            "MainData.MND_Management",
                            "MainData.MND_Address",
                            "MainData.MND_City",
                            "MainData.MND_Zip",
                            "MainData.MND_PhoneNo",
                            "MainData.MND_FaxNo",
                            "Felony.FLN_FelonyCase",
                            "Felony.FLN_FelonyDUIMonths",
                            "Felony.FLN_FelonyDrugMonths",
                            "Felony.FLN_FelonyMarijuanaMonths",
                            "Felony.FLN_FelonyTheftMonths",
                            "Felony.FLN_FelonyWeaponMonths",
                            "Felony.FLN_FelonyVCAPMonths",
                            "Felony.FLN_FelonyNotes",
                            "Misdemeanor.MSD_MisdemeanorCase",
                            "Misdemeanor.MSD_MisdemeanorDUIMonths",
                            "Misdemeanor.MSD_MisdemeanorDrugMonths",
                            "Misdemeanor.MSD_MisdemeanorMarijuanaMonths",
                            "Misdemeanor.MSD_MisdemeanorTheftMonths",
                            "Misdemeanor.MSD_MisdemeanorWeaponMonths",
                            "Misdemeanor.MSD_MisdemeanorVCAPMonths",
                            "Misdemeanor.MSD_MisdemeanorNotes",
                            "RentalIssue.RNT_RentalIssueAgeMonths",
                            "RentalIssue.RNT_RentalIssueMax",
                            "RentalIssue.RNT_RentalIssueAmount",
                            "Credit.CRD_CreditScore",
                            "Credit.CRD_CreditFriendly",
                            "Credit.CRD_CreditBureau",
                            "Credit.CRD_CreditSystem",
                            "MainData.MND_OpenBankruptcy",
                            "MainData.MND_DisBankruptcyAgeMonths",
                            "MainData.MND_IncomeRequirement",
                            "MainData.MND_FoodStampsYesNo",
                            "MainData.MND_CompanyLetterHeadYesNo",
                            "MainData.MND_NonLetterHeadLetterYesNo",
                            "MainData.MND_LengthofJobMonths",
                            "MainData.MND_Section8",
                            "MainData.MND_HOMINC",
                            "MainData.MND_BiltmoreProperties",
                            "MainData.MND_RapidRehousing",
                            "MainData.MND_HUDVASH",
                            "MainData.MND_Visa",
                            "MainData.MND_NoSSNo",
                            "MainData.MND_MexID",
                            "MainData.MND_ITINNo",
                            "MainData.MND_WD",
                            "MainData.MND_SXSIncluded",
                            "MainData.MND_StackIncluded",
                            "MainData.MND_SXSHookup",
                            "MainData.MND_StackHookUp",
                            "MainData.MND_OnSiteFacility",
                            "MainData.MND_LaundryNotes",
                            "MainData.MND_PetWeightLimit",
                            "MainData.MND_RestrictedBreed",
                            "MainData.MND_NumberOfPetMax",
                            "MainData.MND_Utilities",
                            "MainData.MND_APS",
                            "MainData.MND_SRP",
                            "MainData.MND_INCL",
                            "MainData.MND_GAS",
                            "Price.PRC_SPriceRANGE",
                            "Price.PRC_SPriceLOW",
                            "Price.PRC_SPriceHIGH",
                            "Price.PRC_1X1PriceRANGE",
                            "Price.PRC_1X1PriceLOW",
                            "Price.PRC_1X1PriceHIGH",
                            "Price.PRC_1X1DENPrice",
                            "Price.PRC_2X1PriceRANGE",
                            "Price.PRC_2X1PriceLOW",
                            "Price.PRC_2X1PriceHIGH",
                            "Price.PRC_2X2PriceRANGE",
                            "Price.PRC_2X2PriceLOW",
                            "Price.PRC_2X2PriceHIGH",
                            "Price.PRC_2BRDENPrice",
                            "Price.PRC_3X1Price",
                            "Price.PRC_3X2PriceRANGE",
                            "Price.PRC_3X2PriceLOW",
                            "Price.PRC_3X2PriceHIGH",
                            "Price.PRC_4X2Price",
                            "Sq.SQ_SqS",
                            "Sq.SQ_Sq1X1",
                            "Sq.SQ_Sq1X1DEN",
                            "Sq.SQ_Sq2X1",
                            "Sq.SQ_Sq2X2",
                            "Sq.SQ_Sq2BRDEN",
                            "Sq.SQ_Sq3X1",
                            "Sq.SQ_Sq3X2",
                            "Sq.SQ_Sq4X2",
                            "MainData.MND_Garage",
                            "MainData.MND_Fitness",
                            "MainData.MND_Handicap",
                            "MainData.MND_Gated",
                            "MainData.MND_Furnished",
                            "MainData.MND_CableIncl",
                            "MainData.MND_Sublevel",
                            "MainData.MND_Occupant",
                            "MainData.MND_ShortestTerm",
                            "Location.LOC_Latitude",
                            "Location.LOC_Longitude")->get()->all();
        $this->fixEncoding($rows);
        fputcsv($output, $this->static_headers);
        foreach($rows as $k=>$i){
            fputcsv($output, (array)$i);
        }
    }

    private function backupDB(){
        $backup_path = "/home/aaronc123/public_html/storage/app/public/db/";
        $host = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $database = config('database.connections.mysql.database');
        $password = config('database.connections.mysql.password');
        $cmd = "mysqldump --add-drop-table -h {$host} -u {$username} -p{$password} {$database} MainData Credit Felony Location Misdemeanor Price RentalIssue Sq > " . $backup_path . date("Y_m_d_H_i_s") . "_backup_{$database}.sql";
        exec($cmd);
    }

    private function deleteRecords(){
        $tables = ['Price','Misdemeanor','MainData','Location','Felony','Credit','Sq','RentalIssue'];
        foreach($tables as $i){
            DB::statement('CREATE TABLE `c8_aaron`.`new'.$i.'` LIKE `c8_aaron`.`'.$i.'`');
            DB::statement('DROP TABLE `c8_aaron`.`'.$i.'`');
            DB::statement('ALTER TABLE `c8_aaron`.`new'.$i.'` RENAME TO `c8_aaron`.`'.$i.'`');
        }
    }

    public function restoreDB(){
        $host = config('database.connections.mysql.host');
        $username = config('database.connections.mysql.username');
        $database = config('database.connections.mysql.database');
        $password = config('database.connections.mysql.password');
        $restore_file = "/public_html/storage/app/bup/".date("Y_m_d") . "_{$database}.sql";
        $cmd = "mysql -h {$host} -u {$username} -p{$password} {$database} < $restore_file";
        exec($cmd);
        //$delete_cmd = "rm -rf ".$restore_file;
        //exec($delete_cmd);
    }

    private function setHeaders($headers){
        $this->file_headers = $headers;
    }

    private function getHeaders(){
        return $this->file_headers;
    }

    private function validateHeaders($headers){
        $headers = array_map('trim', $headers);
        $temp_headers = array_change_key_case($this->headers_fieldnames);
        foreach($headers as $k => $i){
            if(!empty($i) && !array_key_exists(strtolower($i), $temp_headers))
                return false;
        }
        return true;
    }

    private function createDataFromCSV($data){
        $headers = $this->getHeaders();
        $new_data = [];
        foreach($data as $k => $i){
            $new_data[$this->headers_fieldnames[$headers[$k]]] = $i;
        }
        return $new_data;
    }

    private function getLocation($address){
        $confidence_flag = 1;
        $locs = Mapper::location($address); // if not error, then confident. flag it as 1;
        if(!$locs){
            $confidence_flag = 0;
            $lesser_address = substr($address, 0, strrpos($address, ' ')-1);
            $locs = Mapper::location($lesser_address); // not so confident, flag it as -1
        }
        if(!$locs)
            return [0,0,-1];
        return [$locs->getLatitude(), $locs->getLongitude(), $confidence_flag];
    }

    private function newRecords($data) {
        $mnd = [];
        $fln = [];
        $crd = [];
        $msd = [];
        $prc = [];
        $rnt = [];
        $sq = [];
        $loc = [];
        foreach($data as $k=>$i){
            $prefix = explode("_", $k);
            $prefix = $prefix[0];
            switch($prefix){
                case 'MND': $mnd[$k] = ($k=='MND_LastUpdated'?date('Y-m-d H:i:s', strtotime($i)):utf8_encode($i));break;
                case 'FLN': $fln[$k] = utf8_encode($i);break;
                case 'CRD': $crd[$k] = utf8_encode($i);break;
                case 'MSD': $msd[$k] = utf8_encode($i);break;
                case 'PRC': $prc[$k] = utf8_encode($i);break;
                case 'RNT': $rnt[$k] = utf8_encode($i);break;
                case 'SQ': $sq[$k] = utf8_encode($i);break;
                case 'LOC': $loc[$k] = utf8_encode($i);break;
            }
        }
        $id = DB::table('MainData')->insertGetId($mnd);
        $fln['FLN_MND_ID'] = $id;
        $crd['CRD_MND_ID'] = $id;
        $msd['MSD_MND_ID'] = $id;
        $prc['PRC_MND_ID'] = $id;
        $rnt['RNT_MND_ID'] = $id;
        $sq['SQ_MND_ID'] = $id;
        $loc['LOC_MND_ID'] = $id;
        DB::table('Felony')->insert($fln);
        DB::table('Credit')->insert($crd);
        DB::table('Misdemeanor')->insert($msd);
        DB::table('Price')->insert($prc);
        DB::table('RentalIssue')->insert($rnt);
        DB::table('Sq')->insert($sq);
        if(!empty($loc['LOC_Longitude']) && !empty($loc['LOC_Latitude'])){
            $confidence = array_key_exists('LOC_Confidence', $loc) && in_array(intval($loc['LOC_Confidence']), [-1,0,1])?$loc['LOC_Confidence']:1;
            $loc['LOC_Confidence'] = $confidence;
            DB::table('Location')->insert($loc);
        }else{
            $location = $this->getLocation($data['MND_Address']);
            $loc['LOC_Latitude'] = $location[0];
            $loc['LOC_Longitude'] = $location[1];
            $loc['LOC_Confidence'] = $location[2];
            DB::table('Location')->insert($loc);
        }
    }

    public function upload(Request $request){
        $data = $request->all();
        $file = Input::file('csvFile');
        $orig_file_name = $file->getClientOriginalName();
        if($file->getClientOriginalExtension() != 'csv'){
            return response()->json(['response' =>'error']);
        }
        else {
            $filename = Storage::put('public/csv/'.$orig_file_name, File::get($data['csvFile']));
            $file = file('storage/csv/'.$orig_file_name, FILE_SKIP_EMPTY_LINES);
            if($filename){
                $this->backupDB();
                $this->deleteRecords();
                return response()->json(['response'=>'success', 'total' => count($file)]);
            }else
                return response()->json(['response' =>'error']);
        }
    }

    public function import(Request $request){
        $data = $request->all();
        $filename = $data['filename'];
        $start = intval($data['start']);
        $total = intval($data['total']);
        $ongoing = array_key_exists('ongoing', $data)?$data['ongoing']:'true';
        $file = fopen('storage/csv/'.$filename, 'r');
        $counter = 0;
        if($file !== FALSE){
            while(($data = fgetcsv($file)) !== FALSE){
                if($counter > 0 && $counter >= $start)
                    $this->newRecords($this->createDataFromCSV($data));
                else if($counter == 0){
                    if($this->validateHeaders($data))
                        $this->setHeaders($data);                        
                    else
                        return response()->json(['response' =>'error']);
                }
                $counter++;
                if($counter == ($start + $this->limit))
                    break;
            }
            if($counter >= $total)
                $ongoing = 'false';
            return response()->json(['ongoing' =>$ongoing, 'current' => $counter, 'start'=>$start, 'total'=>$total, 'filename'=>$filename]);
        }
    }

    public function cleanup(Request $request){
        $data = $request->all();
        $filename = $data['filename'];
        Storage::delete('/public/csv/'.basename($filename));
    }
}
