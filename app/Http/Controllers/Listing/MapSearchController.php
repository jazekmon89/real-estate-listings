<?php

namespace App\Http\Controllers\Listing;
set_time_limit(0);
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Flash;
use DB;
use App\Listing;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\Facades\Datatables;
use Image;
use Storage;
use File;

class MapSearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $listing = null;

    public function __construct()
    {
        $this->listing = new Listing;
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $filters = $this->getFilters();
        $map = $this->sample_map();
        return view('Listings.MapSearch.mapSearch', compact('filters','map'));
    }

    public function getBlankMap(){
        return $this->sample_map(false, true);
    }

    public function sample_map($result_flag = false, $setOldCenter = false){
        $ip = $_SERVER['REMOTE_ADDR'];
        $ip = in_array($ip, ['127.0.0.1','::1']) || strpos($ip, '192.168.') !== false?json_decode(file_get_contents("https://api.ipify.org?format=json"))->ip:$ip;
        $details = json_decode(file_get_contents("http://ipinfo.io/".$ip."/json"));
        $coordinates = explode(',', $details->loc);
        return $this->getMapWithCoordinates($coordinates, $result_flag, $setOldCenter);
    }

    public function getLocs(){
        $address = DB::table('MainData')->select('MND_ID','MND_Address')->get()->all();
        //$coordinates = [];
        foreach($address as $k=>$i){
            $confidence_flag = 1;
            $locs = Mapper::location($i->MND_Address); // if not error, then confident. flag it as 1;
            if(!$locs){
                $confidence_flag = 0;
                $lesser_address = substr($i->MND_Address, 0, strrpos($i->MND_Address, ' ')-1);
                $locs = Mapper::location($lesser_address); // not so confident, flag it as 0
            }
            if(!$locs)
                print "Insert into Location(LOC_MND_ID, LOC_Latitude, LOC_Longitude, LOC_Confidence) values('".$i->MND_ID."','0', '0','-1');<br />";
            else
                //$coordinates[$k] = [$locs->getLatitude(), $locs->getLongitude()];
                print "Insert into Location(LOC_MND_ID, LOC_Latitude, LOC_Longitude, LOC_Confidence) values('".$i->MND_ID."','".$locs->getLatitude()."', '".$locs->getLongitude()."','".$confidence_flag."');<br />";
        }
        dd('awww!!');
    }

    public function getFilters(){
        $new_filters = [];
        $active_input_names = $this->listing->sp_GetInputNames();
        foreach($active_input_names as $filters){
            $new_filters[$filters->SearchName] = ['count'=>0];
        }
        foreach($active_input_names as $filters){
            $filter_infos = DB::table('InputName')->where('IPN_ID','=',$filters->IPN_ID)->selectRaw('IPN_UniqueName, UPPER(IPN_IncludeOnly) as IPN_IncludeOnly, UPPER(IPN_ExcludeAll) as IPN_ExcludeAll, IPN_Placeholder, IPN_NumberMaxAmount, IPN_CombinedFieldsID')->get()->first();
            $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']] = (object)[];
            $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->fieldName = $filters->Column;
            $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->tableName = $filters->Table;
            $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->InputTypeName = $filters->InputTypeName;
            $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->InputName = $filters->InputName;
            $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->Active = $filters->Active;
            $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->UniqueName = $filter_infos->IPN_UniqueName;
            $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->Placeholder = $filter_infos->IPN_Placeholder;
            $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->CombinedFieldsID = $filter_infos->IPN_CombinedFieldsID;
            if(in_array($filters->InputTypeName, ['dropdown','selectmultioption','dropdownshow'])){
                $records = DB::table($filters->Table)->groupBy($filters->Column)->pluck($filters->Column)->all();
                if($filters->InputTypeName != 'selectmultioption')
                    array_unshift($records, 'Please select');
                $new_records = [];
                $includeOnly = json_decode($filter_infos->IPN_IncludeOnly);
                $excludeAll = json_decode($filter_infos->IPN_ExcludeAll);
                foreach($records as $h=>$j){
                    if($j == "Please select")
                        $new_records['default'] = utf8_decode($j);
                    else if(((count($includeOnly) > 0 && in_array(strtoupper($j), $includeOnly)) || count($includeOnly)== 0) && ((count($excludeAll) > 0 && !in_array(strtoupper($j), $excludeAll)) || count($excludeAll)== 0)){/*in_array($j, ['CBC']) === FALSE && strlen($j) > 0)*/
                        $new_records[utf8_encode(strval($j))] = utf8_decode($j);
                    }
                }
                $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->options = $new_records;
            }else if(in_array($filters->InputTypeName, ['number','range','rangemultiselect'])){
                $new_filters[$filters->SearchName][$new_filters[$filters->SearchName]['count']]->MaxAmount = $filter_infos->IPN_NumberMaxAmount;
            }
            $new_filters[$filters->SearchName]['count'] += 1;
        }
        return view('Listings.MapSearch.filters', compact('new_filters'))->render();
    }

    public function searchProcessAND($data){
        $mnd_ids = [];
        $sql = [];
        $no_main_table_flag = true;
        foreach($data as $k=>$i){
            $table_field = explode("_",$k,2);
            if(is_array($table_field) && count($table_field) != 2)
                $table_field = explode(".",$k,2);
            if(is_array($table_field) && count($table_field) == 2){
                $prefix = '';
                $table_field['1'] = explode('_',$table_field['1'],3);
                if(count($table_field['1']) == 3)
                    unset($table_field['1']['2']);
                $table_field['1'] = implode('_',$table_field['1']);
                if($table_field['0'] != 'MainData'){
                    $prefix = explode('_', $table_field['1']);
                    $prefix = current($prefix)."_";
                    if(!array_key_exists('table_prefix', $sql))
                        $sql['table_prefix'] = $prefix;
                }else
                    $no_main_table_flag = false;
                $input_type = $data[$k]['input_type'];
                unset($data[$k]['input_type']);
                unset($i['input_type']);
                if($input_type == 'exact'){
                    if(is_array($i) && count($i) > 0){
                        $sql = [];
                        $sql[] = DB::table($table_field['0'])
                            ->where($table_field['1'], '=', $i[0])
                            ->select($prefix.'MND_ID as MND_ID');
                    }
                    break;
                }elseif($input_type == "array"){
                    if(is_array($i) && count($i) > 0){
                        $sql = [];
                        $sql[] = DB::table($table_field['0'])
                            ->whereIn($table_field['1'], explode(",", $i[0]))
                            ->select($prefix.'MND_ID as MND_ID');
                    }
                    break;
                }else{
                    if($input_type == "autocomplete"){
                        foreach($i as $l=>$m){
                            $sql[] = DB::table($table_field['0'])->where($table_field['1'], 'like', '%'.(empty($m)?'':$m).'%')->select($prefix.'MND_ID as MND_ID');
                        }
                    }
                    elseif($input_type == "dropdown"){
                        $first_value = current($i);
                        $first_value = $first_value==null?'':$first_value;
                        $_sql = DB::table($table_field['0'])->where(function($query) use ($table_field, $first_value, $i){//$table_field['1'], '=', $first_value);
                            $query->where($table_field['1'], '=', strval($first_value));
                            foreach($i as $h=>$j){
                                $j = strval($j);
                                if(intval($h) > 0){
                                    $query->orWhere($table_field['1'], '=', ($j==null?'':$j));
                                }
                            }
                        });
                        $sql[] = $_sql->select($prefix.'MND_ID as MND_ID');
                    }elseif($input_type == "range"){
                        $column_name = $table_field['1'];
                        $price_range = ['PRC_SPrice','PRC_1X1Price','PRC_2X1Price','PRC_2X2Price','PRC_3X2Price'];
                        $first_value = current($i);
                        $col_name = in_array($column_name, $price_range)?$column_name."LOW":$column_name;
                        $col_name2 = in_array($column_name, $price_range)?$column_name."HIGH":$column_name;
                        if($col_name != $col_name2){
                            $min_val = strpos($first_value['min'],'.') !== false?floatval($first_value['min']):intval($first_value['min']);
                            $max_val = strpos($first_value['max'],'.') !== false?floatval($first_value['max']):intval($first_value['max']);
                            $_sql = DB::table($table_field['0'])->where(function($query) use ($col_name, $col_name2, $min_val, $max_val){
                                $query->whereNotNull($col_name)
                                ->where($col_name,'<>', '')
                                ->where($col_name,'<>','NA')
                                ->where(function($query) use ($col_name, $col_name2, $min_val, $max_val){
                                    $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) >= ".$min_val." AND CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$max_val)
                                    ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) >= ".$min_val." AND CAST(".$col_name2." AS DECIMAL(10, 2)) <= ".$max_val."), FALSE)");
                                });
                            });
                        }else{
                            $min_val = strpos($first_value['min'],'.') !== false?floatval($first_value['min']):intval($first_value['min']);
                            $max_val = strpos($first_value['max'],'.') !== false?floatval($first_value['max']):intval($first_value['max']);
                            $_sql = DB::table($table_field['0'])->where(function($query) use ($col_name, $min_val, $max_val){
                                $query->whereNotNull($col_name)
                                ->where($col_name,'<>', '')
                                ->where($col_name,'<>','NA')
                                ->where(function($query) use ($col_name, $first_value){
                                    $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) >= ".$min_val." AND CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$max_val);
                                });
                            });
                        }
                        $sql[] = $_sql->select($prefix.'MND_ID as MND_ID');
                    }elseif($input_type == "number"){
                        $column_name = $table_field['1'];
                        $price_range = ['PRC_SPrice','PRC_1X1Price','PRC_2X1Price','PRC_2X2Price','PRC_3X2Price'];
                        $col_name = in_array($column_name, $price_range)?$column_name."LOW":$column_name;
                        $col_name2 = in_array($column_name, $price_range)?$column_name."HIGH":$column_name;
                        foreach($i as $l=>$m){
                            if($col_name != $col_name2){
                                $sql[] = DB::table($table_field['0'])
                                        ->where(function($query) use ($column_name, $m){
                                            $query->where($col_name, 'REGEXP', '^-?[0-9]+$')
                                            ->whereRaw('CAST('.$col_name.' as UNSIGNED) <= '.intval($m))
                                            ->orWhereRaw('CAST('.$col_name2.' AS DECIMAL(10, 2)) >= '.intval($m));
                                        })//->orWhere($table_field['1'],'=','CBC')
                                        ->orWhere(function($query) use ($column_name, $m){
                                            $query->where($col_name, 'REGEXP NOT', '^-?[0-9]+$')
                                            ->where($col_name,'=',strval($m))
                                            ->orWhere(function($query) use ($column_name, $m){
                                                $query->where($col_name2, 'REGEXP NOT', '^-?[0-9]+$')
                                                ->where($col_name2,'=',strval($m));
                                            });
                                        })
                                        ->select($prefix.'MND_ID as MND_ID');
                            }else{
                                $sql[] = DB::table($table_field['0'])
                                        ->where(function($query) use ($col_name, $m){
                                            $query->where($col_name, 'REGEXP', '^-?[0-9]+$')
                                            ->whereRaw('CAST('.$col_name.' as UNSIGNED) <= '.intval($m));
                                        })//->orWhere($table_field['1'],'=','CBC')
                                        ->orWhere(function($query) use ($col_name, $m){
                                            $query->where($col_name, 'REGEXP NOT', '^-?[0-9]+$')
                                            ->where($col_name,'=',strval($m));
                                        })
                                        ->select($prefix.'MND_ID as MND_ID');
                            }
                        }
                    }/*elseif($input_type == "array"){
                        if(is_array($i) && count($i) > 0){
                            $sql[] = DB::table($table_field['0'])
                                ->whereIn($table_field['1'], explode(",", $i[0]))
                                ->select($prefix.'MND_ID as MND_ID');
                        }
                    }elseif($input_type == 'exact'){
                        if(is_array($i) && count($i) > 0){
                            $sql[] = DB::table($table_field['0'])
                                ->where($table_field['1'], '=', $i[0])
                                ->select($prefix.'MND_ID as MND_ID');
                        }
                    }*/
                    elseif($input_type == 'rangemultiselect'){
                        $column_name = $table_field['1'];
                        $first_value = current($i);
                        $price_range = ['PRC_SPrice','PRC_1X1Price','PRC_2X1Price','PRC_2X2Price','PRC_3X2Price'];
                        $col_name = in_array($column_name, $price_range)?$column_name."LOW":$column_name;
                        $col_name2 = in_array($column_name, $price_range)?$column_name."HIGH":$column_name;
                        if($col_name != $col_name2){
                            $_sql = DB::table($table_field['0'])->where(function($query) use ($col_name, $col_name2, $first_value, $i){
                                $query->whereNotNull($col_name)
                                ->where($col_name,'<>', '')
                                ->where($col_name,'<>','NA')
                                ->where(function($query) use ($col_name, $col_name2, $first_value){
                                    $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) = ".$first_value)
                                    ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) = ".$first_value."), FALSE)");
                                });
                                foreach($i as $h=>$j){
                                    if(intval($h) > 0){
                                        $query->orWhere(function($query) use ($col_name, $col_name2, $j){
                                            $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) = ".$j)
                                            ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) = ".$j."), FALSE)");
                                        });
                                    }
                                }
                            });
                        }else{
                            $_sql = DB::table($table_field['0'])->where(function($query) use ($col_name, $first_value, $i){
                                $query->whereNotNull($col_name)
                                ->where($col_name,'<>', '')
                                ->where($col_name,'<>','NA')
                                ->where(function($query) use ($col_name, $first_value){
                                    $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) = ".$first_value);
                                });
                                foreach($i as $h=>$j){
                                    if(intval($h) > 0){
                                        $query->orWhere(function($query) use ($col_name, $j){
                                            $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) = ".$j);
                                        });
                                    }
                                }
                            });
                        }
                        $sql[] = $_sql->select($prefix.'MND_ID as MND_ID');
                    }
                }
            }
        }
        if(!$no_main_table_flag)
            unset($sql['table_prefix']);
        return $sql;
    }

    public function searchProcess($data){
        $mnd_ids = [];
        $sql = [];
        $no_main_table_flag = true;
        foreach($data as $k=>$i){
            $table_field = explode("_",$k,2);
            if(is_array($table_field) && count($table_field) != 2)
                $table_field = explode(".",$k,2);
            if(is_array($table_field) && count($table_field) == 2){
                $prefix = '';
                $table_field['1'] = explode('_',$table_field['1'],3);
                if(count($table_field['1']) == 3)
                    unset($table_field['1']['2']);
                $table_field['1'] = implode('_',$table_field['1']);
                if($table_field['0'] != 'MainData'){
                    $prefix = explode('_', $table_field['1']);
                    $prefix = current($prefix)."_";
                    if(!array_key_exists('table_prefix', $sql))
                        $sql['table_prefix'] = $prefix;
                }else
                    $no_main_table_flag = false;
                $input_type = $data[$k]['input_type'];
                unset($data[$k]['input_type']);
                unset($i['input_type']);
                if($input_type == 'exact'){
                    if(is_array($i) && count($i) > 0){
                        $sql = [];
                        $sql[] = DB::table($table_field['0'])
                            ->where($table_field['1'], '=', $i[0])
                            ->select($prefix.'MND_ID as MND_ID');
                    }
                    break;
                }elseif($input_type == "array"){
                    if(is_array($i) && count($i) > 0){
                        $sql = [];
                        $sql[] = DB::table($table_field['0'])
                            ->whereIn($table_field['1'], explode(",", $i[0]))
                            ->select($prefix.'MND_ID as MND_ID');
                    }
                    break;
                }else{
                    if($input_type == "autocomplete"){
                        foreach($i as $l=>$m){
                            $sql[] = DB::table($table_field['0'])->where($table_field['1'], 'like', '%'.(empty($m)?'':$m).'%')->select($prefix.'MND_ID as MND_ID');
                        }
                    }
                    elseif($input_type == "dropdown"){
                        $first_value = current($i);
                        $first_value = $first_value==null?'':$first_value;
                        $_sql = DB::table($table_field['0'])->where(function($query) use ($table_field, $first_value, $i){//$table_field['1'], '=', $first_value);
                            $query->where($table_field['1'], '=', strval($first_value));
                            foreach($i as $h=>$j){
                                $j = strval($j);
                                if(intval($h) > 0){
                                    $query->orWhere($table_field['1'], '=', ($j==null?'':$j));
                                }
                            }
                        });
                        $sql[] = $_sql->select($prefix.'MND_ID as MND_ID');
                    }elseif($input_type == "range"){
                        $column_name = $table_field['1'];
                        $price_range = ['PRC_SPrice','PRC_1X1Price','PRC_2X1Price','PRC_2X2Price','PRC_3X2Price'];
                        $first_value = current($i);
                        $col_name = in_array($column_name, $price_range)?$column_name."LOW":$column_name;
                        $col_name2 = in_array($column_name, $price_range)?$column_name."HIGH":$column_name;
                        if($col_name != $col_name2){
                            $min_val = strpos($first_value['min'],'.') !== false?floatval($first_value['min']):intval($first_value['min']);
                            $max_val = strpos($first_value['max'],'.') !== false?floatval($first_value['max']):intval($first_value['max']);
                            $_sql = DB::table($table_field['0'])->where(function($query) use ($col_name, $col_name2, $min_val, $max_val){
                                $query->whereNotNull($col_name)
                                ->where($col_name,'<>', '')
                                ->where($col_name,'<>','NA')
                                ->where(function($query) use ($col_name, $col_name2, $min_val, $max_val){
                                    $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) >= ".$min_val." AND CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$max_val)
                                    ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) >= ".$min_val." AND CAST(".$col_name2." AS DECIMAL(10, 2)) <= ".$max_val."), FALSE)");
                                });
                            });
                        }else{
                            $min_val = strpos($first_value['min'],'.') !== false?floatval($first_value['min']):intval($first_value['min']);
                            $max_val = strpos($first_value['max'],'.') !== false?floatval($first_value['max']):intval($first_value['max']);
                            $_sql = DB::table($table_field['0'])->where(function($query) use ($col_name, $min_val, $max_val){
                                $query->whereNotNull($col_name)
                                ->where($col_name,'<>', '')
                                ->where($col_name,'<>','NA')
                                ->where(function($query) use ($col_name, $first_value){
                                    $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) >= ".$min_val." AND CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$max_val);
                                });
                            });
                        }
                        $sql[] = $_sql->select($prefix.'MND_ID as MND_ID');
                    }elseif($input_type == "number"){
                        $column_name = $table_field['1'];
                        $price_range = ['PRC_SPrice','PRC_1X1Price','PRC_2X1Price','PRC_2X2Price','PRC_3X2Price'];
                        $col_name = in_array($column_name, $price_range)?$column_name."LOW":$column_name;
                        $col_name2 = in_array($column_name, $price_range)?$column_name."HIGH":$column_name;
                        foreach($i as $l=>$m){
                            if($col_name != $col_name2){
                                $sql[] = DB::table($table_field['0'])
                                        ->where(function($query) use ($column_name, $m){
                                            $query->where($col_name, 'REGEXP', '^-?[0-9]+$')
                                            ->whereRaw('CAST('.$col_name.' as UNSIGNED) <= '.intval($m))
                                            ->orWhereRaw('CAST('.$col_name2.' AS DECIMAL(10, 2)) >= '.intval($m));
                                        })//->orWhere($table_field['1'],'=','CBC')
                                        ->orWhere(function($query) use ($column_name, $m){
                                            $query->where($col_name, 'REGEXP NOT', '^-?[0-9]+$')
                                            ->where($col_name,'=',strval($m))
                                            ->orWhere(function($query) use ($column_name, $m){
                                                $query->where($col_name2, 'REGEXP NOT', '^-?[0-9]+$')
                                                ->where($col_name2,'=',strval($m));
                                            });
                                        })
                                        ->select($prefix.'MND_ID as MND_ID');
                            }else{
                                $sql[] = DB::table($table_field['0'])
                                        ->where(function($query) use ($col_name, $m){
                                            $query->where($col_name, 'REGEXP', '^-?[0-9]+$')
                                            ->whereRaw('CAST('.$col_name.' as UNSIGNED) <= '.intval($m));
                                        })//->orWhere($table_field['1'],'=','CBC')
                                        ->orWhere(function($query) use ($col_name, $m){
                                            $query->where($col_name, 'REGEXP NOT', '^-?[0-9]+$')
                                            ->where($col_name,'=',strval($m));
                                        })
                                        ->select($prefix.'MND_ID as MND_ID');
                            }
                        }
                    }/*elseif($input_type == "array"){
                        if(is_array($i) && count($i) > 0){
                            $sql[] = DB::table($table_field['0'])
                                ->whereIn($table_field['1'], explode(",", $i[0]))
                                ->select($prefix.'MND_ID as MND_ID');
                        }
                    }elseif($input_type == 'exact'){
                        if(is_array($i) && count($i) > 0){
                            $sql[] = DB::table($table_field['0'])
                                ->where($table_field['1'], '=', $i[0])
                                ->select($prefix.'MND_ID as MND_ID');
                        }
                    }*/
                    elseif($input_type == 'rangemultiselect'){
                        $column_name = $table_field['1'];
                        $first_value = current($i);
                        $price_range = ['PRC_SPrice','PRC_1X1Price','PRC_2X1Price','PRC_2X2Price','PRC_3X2Price'];
                        $col_name = in_array($column_name, $price_range)?$column_name."LOW":$column_name;
                        $col_name2 = in_array($column_name, $price_range)?$column_name."HIGH":$column_name;
                        if($col_name != $col_name2){
                            $_sql = DB::table($table_field['0'])->where(function($query) use ($col_name, $col_name2, $first_value, $i){
                                $query->whereNotNull($col_name)
                                ->where($col_name,'<>', '')
                                ->where($col_name,'<>','NA')
                                ->where(function($query) use ($col_name, $col_name2, $first_value){
                                    $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) = ".$first_value)
                                    ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) = ".$first_value."), FALSE)");
                                });
                                foreach($i as $h=>$j){
                                    if(intval($h) > 0){
                                        $query->orWhere(function($query) use ($col_name, $col_name2, $j){
                                            $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) = ".$j)
                                            ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) = ".$j."), FALSE)");
                                        });
                                    }
                                }
                            });
                        }else{
                            $_sql = DB::table($table_field['0'])->where(function($query) use ($col_name, $first_value, $i){
                                $query->whereNotNull($col_name)
                                ->where($col_name,'<>', '')
                                ->where($col_name,'<>','NA')
                                ->where(function($query) use ($col_name, $first_value){
                                    $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) = ".$first_value);
                                });
                                foreach($i as $h=>$j){
                                    if(intval($h) > 0){
                                        $query->orWhere(function($query) use ($col_name, $j){
                                            $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) = ".$j);
                                        });
                                    }
                                }
                            });
                        }
                        $sql[] = $_sql->select($prefix.'MND_ID as MND_ID');
                    }
                }
            }
        }
        if(!$no_main_table_flag)
            unset($sql['table_prefix']);
        return $sql;
    }

    public function searchAND(Request $request){
        $data = $request->all();
        $sql = $this->searchProcessAND($data);
        $to_return = '';
        if(count($sql) > 0){
            $sql_all = $sql[0];
            if(count($sql) > 1){
                $table_prefix = null;
                if(array_key_exists('table_prefix', $sql)){
                    $table_prefix = $sql['table_prefix'];
                    unset($sql['table_prefix']);
                }
                foreach($sql as $k=>$i){
                    if($k > 0){
                        if($table_prefix != null){
                            $sql_all->whereIn($table_prefix.'MND_ID', $i);
                        }else
                            $sql_all->whereIn('MND_ID', $i);
                    }
                }
            }
            //dd($sql_all->toSql());
            $mnd_ids = $sql_all->groupBy('MND_ID')->pluck('MND_ID')->all();
            $result_count = count($mnd_ids);
            if($result_count > 0)
                $to_return = $this->showMapWithResult($mnd_ids, $result_count);
            else
                $to_return = $this->sample_map(true);
        }else
            $to_return = $this->sample_map(true);
        return $to_return;
    }

    public function search(Request $request){
        $data = $request->all();
        $sql = $this->searchProcessAND($data);
        $to_return = '';
        if(count($sql) > 0){
            $sql_all = $sql[0];
            if(count($sql) > 1){
                $table_prefix = null;
                if(array_key_exists('table_prefix', $sql)){
                    $table_prefix = $sql['table_prefix'];
                    unset($sql['table_prefix']);
                }
                foreach($sql as $k=>$i){
                    if($k > 0){
                        if($table_prefix != null){
                            $sql_all->orWhereIn($table_prefix.'MND_ID', $i);
                        }else
                            $sql_all->orWhereIn('MND_ID', $i);
                    }
                }
            }
            //dd($sql_all->toSql());
            $mnd_ids = $sql_all->groupBy('MND_ID')->pluck('MND_ID')->all();
            $result_count = count($mnd_ids);
            if($result_count > 0)
                $to_return = $this->showMapWithResult($mnd_ids, $result_count);
            else
                $to_return = $this->sample_map(true);
        }else
            $to_return = $this->sample_map(true);
        return $to_return;
    }

    private function fixEncoding(&$data){
        foreach($data as $k=>$i){
            foreach($i as $h=>$j){
                $data[$k]->$h = utf8_decode($j);
            }
        }
    }

    public function completeSearchAND(Request $request){
        $data = $request->all();
        unset($data['_token']);
        $sql = $this->searchProcessAND($data);
        if(count($sql) > 0){
            $sql_all = $sql[0];
            if(count($sql) > 1){
                $table_prefix = null;
                if(array_key_exists('table_prefix', $sql)){
                    $table_prefix = $sql['table_prefix'];
                    unset($sql['table_prefix']);
                }
                foreach($sql as $k=>$i){
                    if($k > 0){
                        if($table_prefix != null){
                            $sql_all->whereIn($table_prefix.'MND_ID', $i);
                        }else
                            $sql_all->whereIn('MND_ID', $i);
                    }
                }
                //dd($sql_all->toSql());
            }
            $sql_all->groupBy('MND_ID');
            $all_records = DB::table('MainData')->whereIn('MND_ID', $sql_all)
                        ->leftJoin('Felony', 'Felony.FLN_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('Credit', 'Credit.CRD_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('Misdemeanor', 'Misdemeanor.MSD_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('Price', 'Price.PRC_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('RentalIssue', 'RentalIssue.RNT_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('Sq', 'Sq.SQ_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('DataImages', 'DataImages.IMG_MND_ID', 'MainData.MND_ID')
                        ->select("MainData.MND_ID",
                            "DataImages.IMG_SRC",
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
                            /*"Price.PRC_PriceS",
                            "Price.PRC_Price1X1",
                            "Price.PRC_Price1X1DEN",
                            "Price.PRC_Price2X1",
                            "Price.PRC_Price2X2",
                            "Price.PRC_Price2BRDEN",
                            "Price.PRC_Price3X1",
                            "Price.PRC_Price3X2",
                            "Price.PRC_Price4X2",*/
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
                            "MainData.MND_ShortestTerm");
            return Datatables::of($all_records)->make(true);
        }
        return '{}';
    }

    public function completeSearch(Request $request){
        $data = $request->all();
        unset($data['_token']);
        $sql = $this->searchProcess($data);
        if(count($sql) > 0){
            $sql_all = $sql[0];
            if(count($sql) > 1){
                $table_prefix = null;
                if(array_key_exists('table_prefix', $sql)){
                    $table_prefix = $sql['table_prefix'];
                    unset($sql['table_prefix']);
                }
                foreach($sql as $k=>$i){
                    if($k > 0){
                        if($table_prefix != null){
                            $sql_all->orWhereIn($table_prefix.'MND_ID', $i);
                        }else
                            $sql_all->orWhereIn('MND_ID', $i);
                    }
                }
                //dd($sql_all->toSql());
            }
            $sql_all->groupBy('MND_ID');
            $all_records = DB::table('MainData')->whereIn('MND_ID', $sql_all)
                        ->leftJoin('Felony', 'Felony.FLN_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('Credit', 'Credit.CRD_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('Misdemeanor', 'Misdemeanor.MSD_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('Price', 'Price.PRC_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('RentalIssue', 'RentalIssue.RNT_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('Sq', 'Sq.SQ_MND_ID', 'MainData.MND_ID')
                        ->leftJoin('DataImages', 'DataImages.IMG_MND_ID', 'MainData.MND_ID')
                        ->select("MainData.MND_ID",
                            "DataImages.IMG_SRC",
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
                            /*"Price.PRC_PriceS",
                            "Price.PRC_Price1X1",
                            "Price.PRC_Price1X1DEN",
                            "Price.PRC_Price2X1",
                            "Price.PRC_Price2X2",
                            "Price.PRC_Price2BRDEN",
                            "Price.PRC_Price3X1",
                            "Price.PRC_Price3X2",
                            "Price.PRC_Price4X2",*/
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
                            "MainData.MND_ShortestTerm");
            return Datatables::of($all_records)->make(true);
        }
        return '{}';
    }

    public function getFiltersWithSearch(Request $request){
        $data = $request->all();
        unset($data['_token']);
        $sql = $this->searchProcessAND($data);
        $to_return = [];
        if(count($sql) > 0){
            $sql_all = $sql[0];
            if(count($sql) > 1){
                $table_prefix = null;
                if(array_key_exists('table_prefix', $sql)){
                    $table_prefix = $sql['table_prefix'];
                    unset($sql['table_prefix']);
                }
                foreach($sql as $k=>$i){
                    if($k > 0){
                        if($table_prefix != null){
                            $sql_all->whereIn($table_prefix.'MND_ID', $i);
                        }else
                            $sql_all->whereIn('MND_ID', $i);
                    }
                }
            }
            $sql_all = $sql_all->groupBy('MND_ID')->pluck('MND_ID');
            $price_range = ['PRC_SPrice','PRC_1X1Price','PRC_2X1Price','PRC_2X2Price','PRC_3X2Price'];
            $multiselect = DB::table("InputName")->where('IPN_Active','=',1)->where('IPN_IPT_ID','=',6)->select('IPN_Table','IPN_Column','IPN_IPT_ID')->get()->all();
            foreach($multiselect as $k=>$i){
                $prefix = explode('_', $i->IPN_Column);
                if(count($prefix) > 0){
                    $prefix = $prefix[0];
                    $prefix = $prefix == 'MND'?'':$prefix.'_';
                    $col_name = $i->IPN_Column;
                    //$col_name = in_array($col_name, $price_range)?$col_name."LOW":$col_name;
                    //$col_name2 = in_array($col_name, $price_range)?$col_name."HIGH":$col_name;
                    $options = [];
                    if(!in_array($col_name, $price_range))
                        $options = DB::table($i->IPN_Table)->whereIn($prefix.'MND_ID',$sql_all)->groupBy($col_name)->pluck($col_name)->all();
                    /*if($col_name != $col_name2){
                        $options2 = DB::table($i->IPN_Table)->whereIn($prefix.'MND_ID',$sql_all)->groupBy($col_name2)->pluck($col_name2)->all();
                        $options = array_merge($options, $options2);
                        $options = array_unique($options);
                    }*/
                    sort($options);
                    $new_options = [];

                    foreach($options as $h=>$j){
                        if(in_array($j, ['CBC']) === FALSE && strlen($j) > 0)
                            $new_options[utf8_encode(strval($j))] = utf8_decode($j);
                    }
                    $to_return[$i->IPN_Table.'_'.$i->IPN_Column] = $new_options;
                }
            }
        }
        return response()->json($to_return);
    }

    public function getFiltersClean(){
        $to_return = [];
        $price_range = ['PRC_SPrice','PRC_1X1Price','PRC_2X1Price','PRC_2X2Price','PRC_3X2Price'];
        $multiselect = DB::table("InputName")->where('IPN_Active','=',1)->where('IPN_IPT_ID','=',6)->select('IPN_Table','IPN_Column','IPN_IPT_ID')->get()->all();
        foreach($multiselect as $k=>$i){
            $col_name = $i->IPN_Column;
            $options = DB::table($i->IPN_Table)->groupBy($col_name)->pluck($col_name)->all();
            $new_options = [];
            foreach($options as $h=>$j){
                if(in_array($j, ['CBC']) === FALSE && strlen($j) > 0)
                    $new_options[utf8_encode(strval($j))] = utf8_decode($j);
            }
            $to_return[$i->IPN_Table.'_'.$i->IPN_Column] = $new_options;
        }
        return response()->json($to_return);
    }

    public function autoComplete(Request $request){
        $data = $request->all();
        $table_field = explode(".",$data['name']);
        if(is_array($table_field) && count($table_field) == 2){
            //$prefix = explode('_', $table_field['1']);
            //$prefix = current($prefix);
            $records = DB::table($table_field['0'])->where($table_field['1'], 'like', '%'.$data['val'].'%')->groupBy($table_field['1'])->pluck($table_field['1'])->all();//only([$prefix.'MND_ID',$table_field['1']])->all();
            return response()->json($records);
        }
        return '';
    }

    public function getInRangeMax(Request $request){
        $data = $request->all();
        $temp_data = $data;
        $to_return = [];
        if(array_key_exists('range_with_multiple', $data)){
            unset($temp_data['range_with_multiple']);
            unset($temp_data['_token']);
            $price_range = ['PRC_SPrice','PRC_1X1Price','PRC_2X1Price','PRC_2X2Price','PRC_3X2Price'];
            foreach($data['range_with_multiple'] as $k=>$i){
                $table_field = explode(".",$i['name']);
                $data_val = $i['val'];
                if(is_numeric($data_val) && is_array($table_field) && count($table_field) == 2){
                    $data_val = intval($data_val);
                    $sql = $this->searchProcess($temp_data);
                    $records = [];
                    $column_name = $table_field[1];
                    $col_name = in_array($column_name, $price_range)?$column_name."LOW":$column_name;
                    $col_name2 = in_array($column_name, $price_range)?$column_name."HIGH":$column_name;
                    if(count($sql) > 0){
                        $sql_all = $sql[0];
                        if(count($sql) > 1){
                            $table_prefix = null;
                            if(array_key_exists('table_prefix', $sql)){
                                $table_prefix = $sql['table_prefix'];
                                unset($sql['table_prefix']);
                            }
                            foreach($sql as $k=>$i){
                                if($k > 0){
                                    if($table_prefix != null){
                                        $sql_all->whereIn($table_prefix.'MND_ID', $i);
                                    }else
                                        $sql_all->whereIn('MND_ID', $i);
                                }
                            }
                        }
                        $sql_all = $sql_all->groupBy('MND_ID')->pluck('MND_ID')->all();
                        $prefix = explode('_', $table_field['1']);
                        $prefix = current($prefix);
                        $records = [];
                        if($col_name != $col_name2){
                            $records = DB::table($table_field['0'])
                                    ->whereNotNull($col_name)
                                    ->where($col_name,'<>', '')
                                    ->where($col_name,'<>','NA')
                                    ->where(function($query) use ($col_name, $col_name2, $data_val){
                                        $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$data_val)
                                        ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) <= ".$data_val."), FALSE)");
                                    })
                                    ->whereIn($prefix."_MND_ID",$sql_all)
                                    ->groupBy($col_name)->pluck($col_name)->all();
                            $records2 = DB::table($table_field['0'])
                                    ->whereNotNull($col_name)
                                    ->where($col_name,'<>', '')
                                    ->where($col_name,'<>','NA')
                                    ->where(function($query) use ($col_name, $col_name2, $data_val){
                                        $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$data_val)
                                        ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) <= ".$data_val."), FALSE)");
                                    })
                                    ->whereIn($prefix."_MND_ID",$sql_all)
                                    ->groupBy($col_name2)->pluck($col_name2)->all();
                            foreach($records2 as $k=>$i){
                                if(!is_numeric($i) || (is_numeric($i) && intval($i) > $data_val))
                                    unset($records2[$k]);
                            }
                            $records = array_merge($records, $records2);
                            $records = array_unique($records);
                        }else{
                            $records = DB::table($table_field['0'])
                                    ->whereNotNull($col_name)
                                    ->where($col_name,'<>', '')
                                    ->where($col_name,'<>','NA')
                                    ->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$data_val)
                                    ->whereIn($prefix."_MND_ID",$sql_all)
                                    ->groupBy($col_name)->pluck($col_name)->all();
                        }
                        sort($records);
                    }else{
                        if($col_name != $col_name2){
                            $records = DB::table($table_field['0'])
                                        ->whereNotNull($col_name)
                                        ->where($col_name,'<>', '')
                                        ->where($col_name,'<>','NA')
                                        ->where(function($query) use ($col_name, $col_name2, $data_val){
                                            $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$data_val)
                                            ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) <= ".$data_val."), FALSE)");
                                        })
                                        ->groupBy($col_name)->pluck($col_name)->all();
                            $records2 = DB::table($table_field['0'])
                                        ->whereNotNull($col_name)
                                        ->where($col_name,'<>', '')
                                        ->where($col_name,'<>','NA')
                                        ->where(function($query) use ($col_name, $col_name2, $data_val){
                                            $query->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$data_val)
                                            ->orWhereRaw("IF(".$col_name2." REGEXP '^-?[0-9]+$', (CAST(".$col_name2." AS DECIMAL(10, 2)) <= ".$data_val."), FALSE)");
                                        })
                                        ->groupBy($col_name2)->pluck($col_name2)->all();
                            foreach($records2 as $k=>$i){
                                if(!is_numeric($i) || (is_numeric($i) && intval($i) > $data_val))
                                    unset($records2[$k]);
                            }
                            $records = array_merge($records, $records2);
                            $records = array_unique($records);
                        }else{
                            $records = DB::table($table_field['0'])
                                        ->whereNotNull($col_name)
                                        ->where($col_name,'<>', '')
                                        ->where($col_name,'<>','NA')
                                        ->whereRaw("CAST(".$col_name." AS DECIMAL(10, 2)) <= ".$data_val)
                                        ->groupBy($col_name)->pluck($col_name)->all();
                        }
                        sort($records);
                    }
                    $to_return[implode("_",$table_field)] = $records;
                }
            }
        }
        if(count($to_return) > 0)
            return response()->json($to_return);
        return '';
    }

    private function showMapWithResult($MainData_Ids, $result_count){
        $coordinates = DB::table('Location')->whereIn('LOC_MND_ID', $MainData_Ids)->get(['LOC_MND_ID','LOC_Latitude','LOC_Longitude'])->all();
        Mapper::map($coordinates['0']->LOC_Latitude, $coordinates['0']->LOC_Longitude, ['eventAfterLoad'=>'initOverlays(true);', 'controlMarkerBounds'=>"showVisibleMarkers();",'marker'=>false, 'zoom'=>14]);
        foreach($coordinates as $k=>$i){
            Mapper::marker($i->LOC_Latitude, $i->LOC_Longitude, ['markerid'=>$i->LOC_MND_ID,'draggable'=>false, 'eventClick'=>"infoWindow.close();infoWindow.setContent(\"<div class='marker-loading'></div>\");$.ajax({url:'".route('map.search.marker.details',[$i->LOC_MND_ID])."',success:function(data){infoWindow.setContent(data);initInfoWindowDetails();}});infoWindow.open(window.map, marker_".$k.");if(window.lastMarker !== undefined){window.lastMarker.setIcon('".url(config('googlmapper.markers.icon'))."');}marker_".$k.".setIcon('".url(config('googlmapper.markers.icon2'))."');window.lastMarker = marker_".$k.";"]);
        }
        return view('Listings.MapSearch.map', ['map_view'=>Mapper::render()])->render();
    }

    private function getMapWithCoordinates($coord, $result_flag = false, $setOldCenter = false){
        $opts = ['zoom' => 16, 'marker'=>false, 'triggerSetOldCenter'=>$setOldCenter];
        Mapper::map($coord['0'], $coord['1'], $opts);
        if($result_flag){
            return view('Listings.MapSearch.map', ['map_view'=>Mapper::render(), 'result_count'=>'0'])->render();
        }
        return view('Listings.MapSearch.map', ['map_view'=>Mapper::render()])->render();
    }

    public function showMap(Request $request){
        $data = $request->all();
        Mapper::location($data['default_address'])->map(['zoom' => 16, 'markers' => ['clusters' => ['size' => 10, 'center' => true, 'zoom' => 10], 'type'=>'TERRAIN']]);
        return view('Listings.MapSearch.map', ['map_view'=>Mapper::render()])->render();
    }

    public function getMarkerDetails($MND_ID){
        $infos = DB::table('MainData')->where('MND_ID','=', $MND_ID)->leftJoin('RentalIssue', 'MainData.MND_ID', 'RentalIssue.RNT_MND_ID')->leftJoin('DataImages','DataImages.IMG_MND_ID','MainData.MND_ID')->leftJoin('Felony', 'MainData.MND_ID','Felony.FLN_MND_ID')->select('MND_ID','MND_Community','MND_Address','MND_City','MND_ZIP','MND_PhoneNo','MND_FaxNo','RNT_RentalIssueAmount','IMG_SRC', 'FLN_FelonyNotes')->get()->all();
        return view('Listings.MapSearch.markerInfoWindow', compact('infos'))->render();
    }

    public function uploadImage(Request $request){
        $data = $request->all();
        if(array_key_exists('_token', $data))
            unset($data['_token']);
        $MND_ID = null;
        $to_return = '';
        $img_thumb = '';
        $json = [];
        if(array_key_exists('MND_ID', $data)){
            $MND_ID = $data['MND_ID'];
            unset($data['MND_ID']);
            $counter = 0;
            foreach($data as $k=>$i){
                $absolute_fname = pathinfo($i->getClientOriginalName(), PATHINFO_FILENAME);
                if(is_numeric($k)){
                    $filename = Storage::put('public/images/'.$i->getClientOriginalName(), File::get($i));//url(Storage::url($i->store('public/images')));
                    if($filename){
                        $filename = url('/storage/images/'.$i->getClientOriginalName());
                        $resize = Image::make($filename)->fit(100)->save($absolute_fname.'_100.png');
                        $res = Storage::put('public/images/'.$absolute_fname.'_100.png', $resize->__toString());
                        $new_file = '';
                        if($res)
                            $new_file = url('/storage/images/'.$absolute_fname.'_100.png');
                        if(empty($img_thumb))
                            $img_thumb = $new_file;
                        $json['img_'.$counter] = ['img_normal'=>$filename, 'img_thumb'=>$img_thumb];
                        File::delete($absolute_fname.'_100.png');
                        $counter++;
                    }
                }
            }
            $this->deleteImages($MND_ID, false);
            DB::table('DataImages')->where('IMG_MND_ID',$MND_ID)->update(['IMG_SRC' => json_encode($json)]);
            $to_return = view('Listings.MapSearch.uploadImage', compact('img_thumb'))->render();
        }
        return $to_return;
    }

    public function deleteImages($IMG_MND_ID, $delete_flag = true){
        $img = DB::table('DataImages')->where('IMG_MND_ID', $IMG_MND_ID)->select('IMG_SRC')->get()->all();
        $img = json_decode($img[0]->IMG_SRC);
        if(is_object($img) && property_exists($img, 'img_0'))
            $img = $img->img_0;
        else
            $img = '';
        if(!empty($img)){
            foreach($img as $k=>$i){
                Storage::delete('/public/images/'.basename($i));
            }
            if($delete_flag)
                $res = DB::table('DataImages')->where('IMG_MND_ID',$IMG_MND_ID)->update(['IMG_SRC' => '{}']);
        }
    }

    public function deleteImage(Request $request){
        $data = $request->all();
        $this->deleteImages($data['MND_ID']);
        $img_thumb = '';
        return view('Listings.MapSearch.uploadImage', compact('img_thumb'))->render();;
    }
}
?>