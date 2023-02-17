<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TournamentController extends Controller
{
    public function index(){
        return view('admin.tournament.list');
    }

    public function addorupdatetournament(Request $request){
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
            $tournament = Tournament::find($request->tournament_id);
            if(!$tournament){
                return response()->json(['status' => '400']);
            }
            $old_image = $tournament->thumb_img;
            $image_name = $old_image;
            $tournament->name = $request->name;
            $tournament->short_name = $request->short_name;
            $tournament->sr_no = $request->sr_no;
        }else{
            $action = "add";
            $tournament = new Tournament();
            $tournament->name = $request->name;
            $tournament->short_name = $request->short_name;
            $tournament->sr_no = $request->sr_no;
            $tournament->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $image_name=null;
        }

        if ($request->hasFile('thumb_img')) {
            $image = $request->file('thumb_img');
            $image_name = 'thumb_img_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/tournament');
            $image->move($destinationPath, $image_name);
            if(isset($old_image)) {
                $old_image = public_path('images/tournament/' . $old_image);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            $tournament->thumb_img = $image_name;
        }
    
        $tournament->save();
        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function alltournamentslist(Request $request){
        if ($request->ajax()) {
           

            $columns = array(
                0 =>'id',
                1 =>'name',
                2 =>'shot_name',
                3=> 'estatus',
                4=> 'created_at',
                5=> 'action',
            );

            $totalData = Tournament::count();

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
                $tournaments = Tournament::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $tournaments =  Tournament::where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%")
                            ->orWhere('name', 'LIKE',"%{$search}%")
                            ->orWhere('shot_name', 'LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = Tournament::count();
            }

            $data = array();

            if(!empty($tournaments))
            {
                foreach ($tournaments as $tournament)
                {
                    $page_id = ProjectPage::where('route_url','admin.tournament.list')->pluck('id')->first();

                    if( $tournament->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="tournamentstatuscheck_'. $tournament->id .'" onchange="changetournamentStatus('. $tournament->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($tournament->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="tournamentstatuscheck_'. $tournament->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $tournament->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="tournamentstatuscheck_'. $tournament->id .'" onchange="changetournamentStatus('. $tournament->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($tournament->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="tournamentstatuscheck_'. $tournament->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    
                    
                   
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="edittournamentBtn"  title="edit" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#tournamentModel" onclick="" data-id="' .$tournament->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                        $action .= '<button id="viewteamBtn" title="Team" class="btn btn-gray text-blue btn-sm" data-id="' .$tournament->id. '"><i class="fa fa-users" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deletetournamentBtn" title="delete" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeletetournamentModel" onclick="" data-id="' .$tournament->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['name'] = $tournament->name;
                    $nestedData['shot_name'] = $tournament->short_name;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($tournament->created_at));
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

    public function changetournamentStatus($id){
        $tournament = Tournament::find($id);
        if ($tournament->estatus==1){
            $tournament->estatus = 2;
            $tournament->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($tournament->estatus==2){
            $tournament->estatus = 1;
            $tournament->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function edittournament($id){
        $tournament = Tournament::find($id);
        return response()->json($tournament);
    }

    public function deletetournament($id){
        $tournament = Tournament::find($id);
        if ($tournament){
            $tournament->estatus = 3;
            $tournament->save();
            $tournament->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function getsrno(){
        $tournament = Tournament::orderBy('id','desc')->first();
        $sr_no = isset($tournament->sr_no)?$tournament->sr_no+1:1;
        return response()->json(['sr_no' => $sr_no]);
    }
}
