<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Stadium;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StadiumController extends Controller
{
    public function index(){
        $countries = Country::where('estatus',1)->get();
        return view('admin.stadium.list',compact('countries'));
    }

    public function addorupdatestadium(Request $request){
        $messages = [
            'name.required' =>'Please provide a name',
            'country_id.required' =>'Please provide a country',
            'state.required' =>'Please provide a state',
            'city.required' =>'Please provide a city',
        ];

        $validator = Validator::make($request->all(), [
            'country_id' => 'required',
            'name' => 'required',
            'state' => 'required',
            'city' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        
        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $stadium = Stadium::find($request->stadium_id);
            if(!$stadium){
                return response()->json(['status' => '400']);
            }
           
            $stadium->name = $request->name;
            $stadium->country_id = $request->country_id;
            $stadium->short_name = $request->short_name;
            $stadium->state = $request->state;
            $stadium->city = $request->city;
        }else{
            $action = "add";
            $stadium = new Stadium();
            $stadium->name = $request->name;
            $stadium->country_id = $request->country_id;
            $stadium->short_name = $request->short_name;
            $stadium->state = $request->state;
            $stadium->city = $request->city;
            $stadium->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
           
        }

        $stadium->save();
        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allstadiumslist(Request $request){
        if ($request->ajax()) {
           

            $columns = array(
                0 =>'id',
                1 =>'name',
                2 =>'shot_name',
                3 =>'country',
                4 =>'state',
                5 =>'city',
                6=> 'estatus',
                7=> 'created_at',
                8=> 'action',
            );

            $totalData = Stadium::count();

            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            //dd($columns[$request->input('order.0.column')]);
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order == "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $stadiums = Stadium::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $stadiums =  Stadium::where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%")
                            ->orWhere('name', 'LIKE',"%{$search}%")
                            ->orWhere('shot_name', 'LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = Stadium::count();
            }

            $data = array();

            if(!empty($stadiums))
            {
                foreach ($stadiums as $stadium)
                {
                    $page_id = ProjectPage::where('route_url','admin.stadium.list')->pluck('id')->first();

                    if( $stadium->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="stadiumstatuscheck_'. $stadium->id .'" onchange="changestadiumStatus('. $stadium->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($stadium->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="stadiumstatuscheck_'. $stadium->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $stadium->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="stadiumstatuscheck_'. $stadium->id .'" onchange="changestadiumStatus('. $stadium->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($stadium->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="stadiumstatuscheck_'. $stadium->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    
                    
                   
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editstadiumBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#stadiumModel" onclick="" data-id="' .$stadium->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deletestadiumBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeletestadiumModel" onclick="" data-id="' .$stadium->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['name'] = $stadium->name;
                    $nestedData['shot_name'] = $stadium->short_name;
                    $nestedData['country'] = $stadium->coutry->name;
                    $nestedData['state'] = $stadium->state;
                    $nestedData['city'] = $stadium->city;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($stadium->created_at));
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

    public function changestadiumStatus($id){
        $stadium = Stadium::find($id);
        if ($stadium->estatus==1){
            $stadium->estatus = 2;
            $stadium->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($stadium->estatus==2){
            $stadium->estatus = 1;
            $stadium->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function editstadium($id){
        $stadium = Stadium::find($id);
        return response()->json($stadium);
    }

    public function deletestadium($id){
        $stadium = Stadium::find($id);
        if ($stadium){
            $stadium->estatus = 3;
            $stadium->save();
            $stadium->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function getsrno($id){
        $stadium = Stadium::where('tournament_id',$id)->orderBy('id','desc')->first();
        $sr_no = isset($stadium->sr_no)?$stadium->sr_no+1:1;
        return response()->json(['sr_no' => $sr_no]);
    }
}
