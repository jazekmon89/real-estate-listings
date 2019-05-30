<?php

namespace App\Http\Controllers\Listing;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Flash;
use DB;
use App\Listing;
use Yajra\Datatables\Facades\Datatables;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;

class ListingsController extends Controller
{
	protected $listing = null;

	public function __construct(){
		$this->listing = new Listing;
		$this->middleware('adminaccess');
	}

	public function manageRecords(){
		return view('Admin.Listings.index');
	}

    private function fixEncoding(&$data){
        foreach($data as $k=>$i){
            foreach($i as $h=>$j){
                $data[$k]->$h = utf8_decode($j);
            }
        }
    }

	public function getRecords(Request $request){
		$data = $request->all();
		$field = $data['columns'][$data['order'][0]['column']]['data'];
		$dir = $data['order'][0]['dir'];
		$records = $this->listing->sp_GetAllRecords([$field, $dir]);
        $this->fixEncoding($records);
        return Datatables::of($records)->make(true);
	}

	public function editRecords(Request $request) {
        $data = $request->all();
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
    			case 'MND': $mnd[$k] = utf8_encode($i);break;
    			case 'FLN': $fln[$k] = utf8_encode($i);break;
    			case 'CRD': $crd[$k] = utf8_encode($i);break;
    			case 'MSD': $msd[$k] = utf8_encode($i);break;
    			case 'PRC': $prc[$k] = utf8_encode($i);break;
    			case 'RNT': $rnt[$k] = utf8_encode($i);break;
    			case 'SQ': $sq[$k] = utf8_encode($i);break;
    			case 'LOC': $loc[$k] = utf8_encode($i);break;
    		}
    	}
    	$id = $data['id'];
    	$mnd['MND_ID'] = $id;
    	$fln['FLN_MND_ID'] = $id;
    	$crd['CRD_MND_ID'] = $id;
    	$msd['MSD_MND_ID'] = $id;
    	$prc['PRC_MND_ID'] = $id;
    	$rnt['RNT_MND_ID'] = $id;
    	$sq['SQ_MND_ID'] = $id;
    	$loc['LOC_MND_ID'] = $id;
    	$mnd['MND_LastUpdated'] = date('Y-m-d H:i:s');
    	DB::table('MainData')->where('MND_ID','=', $id)->update($mnd);
		DB::table('Felony')->where('FLN_MND_ID','=', $id)->update($fln);
		DB::table('Credit')->where('CRD_MND_ID','=', $id)->update($crd);
		DB::table('Misdemeanor')->where('MSD_MND_ID','=', $id)->update($msd);
		DB::table('Price')->where('PRC_MND_ID','=', $id)->update($prc);
		DB::table('RentalIssue')->where('RNT_MND_ID','=', $id)->update($rnt);
		DB::table('Sq')->where('SQ_MND_ID','=', $id)->update($sq);
		if(!empty($loc['LOC_Longitude']) && !empty($loc['LOC_Latitude']) && strval($loc['LOC_Longitude']) != '0' && strval($loc['LOC_Latitude']) != '0' && strval($loc['LOC_Confidence']) == '1'){
			DB::table('Location')->where('LOC_MND_ID','=', $id)->update($loc);
		}elseif(!empty($loc['LOC_Longitude']) && !empty($loc['LOC_Latitude']) && strval($loc['LOC_Longitude']) != '0' && strval($loc['LOC_Latitude']) != '0' && strlen(strval($loc['LOC_Confidence'])) > 0){
			$location_infos = $this->getLocation($mnd['MND_Address']);
			$location_flag = (($location_infos[0] != $loc['LOC_Latitude'] || $location_infos[1] != $loc['LOC_Longitude']) || ($loc['LOC_Latitude'] == $location_infos[0] && $loc['LOC_Longitude'] == $location_infos[1]));
			$confidence = in_array(intval($loc['LOC_Confidence']), [-1,0,1]) && $location_flag?1:$loc['LOC_Confidence'];
			$loc['LOC_Confidence'] = $confidence;
			DB::table('Location')->where('LOC_MND_ID','=', $id)->update($loc);
		}elseif(strval($loc['LOC_Longitude']) == '0' && strval($loc['LOC_Latitude']) == '0' && strval($loc['LOC_Confidence']) != '-1'){
			$loc['LOC_Confidence'] = -1;
			DB::table('Location')->where('LOC_MND_ID','=', $id)->update($loc);
		}
        return 'true';
    }


    public function deleteRecords(Request $request) { 
    	$data = $request->all();
		$ids = '';

		foreach ($data['MND_ID_Lists'] as $value) { 
			$ids .= $value.',';
		}

		$res = $this->listing->sp_DeleteRecords([substr($ids, 0, -1)]);

		if($res !== null)
		    return 'true';
		return 'false';
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

    public function newRecords(Request $request) { 
    	$data = $request->all();
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
    			case 'MND': $mnd[$k] = utf8_encode($i);break;
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
		if(!empty($loc['LOC_Longitude']) && !empty($loc['LOC_Latitude']) && !empty($loc['LOC_Confidence'])){
			$confidence = in_array(intval($loc['LOC_Confidence']), [-1,0,1])?$loc['LOC_Confidence']:1;
			$loc['LOC_Confidence'] = $confidence;
			DB::table('Location')->insert($loc);
		}else{
			$location = $this->getLocation($data['MND_Address']);
			$loc['LOC_Latitude'] = $location[0];
			$loc['LOC_Longitude'] = $location[1];
			$loc['LOC_Confidence'] = $location[2];
			DB::table('Location')->insert($loc);
		}
        return 'true';
    }

}
?>