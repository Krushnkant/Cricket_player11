<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    public function index($id){
        return view('admin.team.list',compact('id'));
    }

    public function addorupdateteam(Request $request){
        $messages = [
            'sr_no.required' =>'Please provide valid Serial Number',
            'sr_no.numeric' =>'Please provide valid Serial Number',
            'name.required' =>'Please provide a name',
        ];

        $validator = Validator::make($request->all(), [
            'sr_no' => 'required|numeric',
            'name' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        
        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $team = Team::find($request->team_id);
            if(!$team){
                return response()->json(['status' => '400']);
            }
            $old_image = $team->thumb_img;
            $image_name = $old_image;
            $team->name = $request->name;
            $team->tournament_id = $request->tournament_id;
            $team->short_name = $request->short_name;
            $team->sr_no = $request->sr_no;
        }else{
            $action = "add";
            $team = new Team();
            $team->name = $request->name;
            $team->tournament_id = $request->tournament_id;
            $team->short_name = $request->short_name;
            $team->sr_no = $request->sr_no;
            $team->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $image_name=null;
        }

        if ($request->hasFile('thumb_img')) {
            $image = $request->file('thumb_img');
            $image_name = 'thumb_img_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/team');
            $image->move($destinationPath, $image_name);
            if(isset($old_image)) {
                $old_image = public_path('images/team/' . $old_image);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            $team->thumb_img = $image_name;
        }
    
        $team->save();
        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allteamslist(Request $request){
        if ($request->ajax()) {
           

            $columns = array(
                0 =>'id',
                1 =>'name',
                2 =>'shot_name',
                3=> 'estatus',
                4=> 'created_at',
                5=> 'action',
            );

            $totalData = Team::where('tournament_id',$request->input('tournament_id'))->count();

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
                $teams = Team::where('tournament_id',$request->input('tournament_id'))->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $teams =  Team::where('tournament_id',$request->input('tournament_id'))->where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%")
                            ->orWhere('name', 'LIKE',"%{$search}%")
                            ->orWhere('shot_name', 'LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = Team::where('tournament_id',$request->input('tournament_id'))->count();
            }

            $data = array();

            if(!empty($teams))
            {
                foreach ($teams as $team)
                {
                    $page_id = ProjectPage::where('route_url','admin.team.list')->pluck('id')->first();

                    if( $team->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="teamstatuscheck_'. $team->id .'" onchange="changeteamStatus('. $team->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($team->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="teamstatuscheck_'. $team->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $team->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="teamstatuscheck_'. $team->id .'" onchange="changeteamStatus('. $team->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($team->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="teamstatuscheck_'. $team->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    
                    
                   
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editteamBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#teamModel" onclick="" data-id="' .$team->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteteamBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteteamModel" onclick="" data-id="' .$team->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['name'] = $team->name;
                    $nestedData['shot_name'] = $team->short_name;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($team->created_at));
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

    public function changeteamStatus($id){
        $team = Team::find($id);
        if ($team->estatus==1){
            $team->estatus = 2;
            $team->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($team->estatus==2){
            $team->estatus = 1;
            $team->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function editteam($id){
        $team = Team::find($id);
        return response()->json($team);
    }

    public function deleteteam($id){
        $team = Team::find($id);
        if ($team){
            $team->estatus = 3;
            $team->save();
            $team->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function getsrno($id){
        $team = Team::where('tournament_id',$id)->orderBy('id','desc')->first();
        $sr_no = isset($team->sr_no)?$team->sr_no+1:1;
        return response()->json(['sr_no' => $sr_no]);
    }
}
