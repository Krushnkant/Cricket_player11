<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    public function index(){
        return view('admin.country.list');
    }

    public function addorupdatecountry(Request $request){
        $messages = [
            'name.required' =>'Please provide a name',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        
        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $country = Country::find($request->country_id);
            if(!$country){
                return response()->json(['status' => '400']);
            }
            $old_image = $country->thumb_img;
            $image_name = $old_image;

            $country->name = $request->name;
        }else{
            $action = "add";
            $country = new Country();
            $country->name = $request->name;
            $country->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $image_name=null;
        }

        if ($request->hasFile('thumb_img')) {
            $image = $request->file('thumb_img');
            $image_name = 'thumb_img_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/country');
            $image->move($destinationPath, $image_name);
            if(isset($old_image)) {
                $old_image = public_path('images/country/' . $old_image);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            $country->thumb_img = $image_name;
        }
    
        $country->save();
        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allcountryslist(Request $request){
        if ($request->ajax()) {
           

            $columns = array(
                0 =>'id',
                1 =>'name',
                2=> 'estatus',
                3=> 'created_at',
                4=> 'action',
            );

            $totalData = Country::count();

            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            //dd($columns[$request->input('order.0.column')]);
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "name";
                $dir = 'ASC';
            }

            if(empty($request->input('search.value')))
            {
                $countrys = Country::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    // ->toSql();
                    ->get();
                    // dd($countrys);
            } else {
                $search = $request->input('search.value');
                $countrys =  Country::where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%")
                            ->orWhere('name', 'LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = Country::count();
            }

            $data = array();

            if(!empty($countrys))
            {
                foreach ($countrys as $country)
                {
                    $page_id = ProjectPage::where('route_url','admin.country.list')->pluck('id')->first();

                    if( $country->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="countrystatuscheck_'. $country->id .'" onchange="changecountryStatus('. $country->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($country->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="countrystatuscheck_'. $country->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $country->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="countrystatuscheck_'. $country->id .'" onchange="changecountryStatus('. $country->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($country->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="countrystatuscheck_'. $country->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    
                    
                   
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editcountryBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#countryModel" onclick="" data-id="' .$country->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                        $action .= '<button id="viewplayerBtn" title="Player" class="btn btn-gray text-blue btn-sm" data-id="' .$country->id. '"><i class="fa fa-users" aria-hidden="true"></i></button>';

                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deletecountryBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeletecountryModel" onclick="" data-id="' .$country->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['name'] = $country->name;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($country->created_at));
                    $nestedData['action'] = $action;
                    $data[] = $nestedData;

                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);
        }
    }

    public function changecountryStatus($id){
        $country = Country::find($id);
        if ($country->estatus==1){
            $country->estatus = 2;
            $country->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($country->estatus==2){
            $country->estatus = 1;
            $country->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function editcountry($id){
        $country = Country::find($id);
        return response()->json($country);
    }

    public function deletecountry($id){
        $country = Country::find($id);
        if ($country){
            $country->estatus = 3;
            $country->save();
            $country->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }
}
