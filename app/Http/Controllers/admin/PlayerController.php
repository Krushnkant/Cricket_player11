<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    public function index($id){
        return view('admin.player.list',compact('id'));
    }

    public function addorupdateplayer(Request $request){
        $messages = [
            'name.required' =>'Please provide a name',
            'player_type.required' =>'Please provide a player type',
            'batting_style.required' =>'Please provide a batting style',
            'bowling_style.required' =>'Please provide a bowling style',
            'bowling_arm.required' =>'Please provide a bowling arm',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'player_type' => 'required',
            'batting_style' => 'required',
            'bowling_style' => 'required',
            'bowling_arm' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        
        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $player = Player::find($request->player_id);
            if(!$player){
                return response()->json(['status' => '400']);
            }
            $old_image = $player->thumb_img;
            $image_name = $old_image;
            $player->name = $request->name;
            $player->country_id = $request->country_id;
            $player->player_type = $request->player_type;
            $player->batting_style = $request->batting_style;
            $player->bowling_style = $request->bowling_style;
            $player->bowling_arm = $request->bowling_arm;
        }else{
            $action = "add";
            $player = new Player();
            $player->name = $request->name;
            $player->country_id = $request->country_id;
            $player->player_type = $request->player_type;
            $player->batting_style = $request->batting_style;
            $player->bowling_style = $request->bowling_style;
            $player->bowling_arm = $request->bowling_arm;
            $player->is_approved_by_admin = 1;
            $player->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $image_name=null;
        }

        if ($request->hasFile('thumb_img')) {
            $image = $request->file('thumb_img');
            $image_name = 'thumb_img_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/player');
            $image->move($destinationPath, $image_name);
            if(isset($old_image)) {
                $old_image = public_path('images/player/' . $old_image);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            $player->thumb_img = $image_name;
        }
    
        $player->save();
        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allplayerslist(Request $request){
        if ($request->ajax()) {
           

            $columns = array(
                0 =>'id',
                1 =>'name',
                2 =>'player_type',
                3 =>'batting_style',
                4 =>'bowling_style',
                5 =>'bowling_arm',
                6 => 'estatus',
                7 => 'created_at',
                8 => 'action',
            );

            $totalData = Player::where('country_id',$request->input('country_id'))->count();

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
                $players = Player::where('country_id',$request->input('country_id'))->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $players =  Player::where('country_id',$request->input('country_id'))->where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%")
                            ->orWhere('name', 'LIKE',"%{$search}%")
                            ->orWhere('shot_name', 'LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = Player::where('country_id',$request->input('country_id'))->count();
            }

            $data = array();

            if(!empty($players))
            {
                foreach ($players as $player)
                {
                    $page_id = ProjectPage::where('route_url','admin.player.list')->pluck('id')->first();

                    if( $player->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="playerstatuscheck_'. $player->id .'" onchange="changeplayerStatus('. $player->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($player->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="playerstatuscheck_'. $player->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $player->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="playerstatuscheck_'. $player->id .'" onchange="changeplayerStatus('. $player->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($player->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="playerstatuscheck_'. $player->id .'" value="2"><span class="slider round"></span></label>';
                    }

    
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editplayerBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#playerModel" onclick="" data-id="' .$player->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteplayerBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteplayerModel" onclick="" data-id="' .$player->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['name'] = $player->name;
                    $nestedData['player_type'] = playerType($player->player_type);
                    $nestedData['batting_style'] = battingStyle($player->batting_style);
                    $nestedData['bowling_style'] = bowlingStyle($player->bowling_style);
                    $nestedData['bowling_arm'] = bowlingArm($player->bowling_arm);
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($player->created_at));
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

    public function changeplayerStatus($id){
        $player = Player::find($id);
        if ($player->estatus==1){
            $player->estatus = 2;
            $player->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($player->estatus==2){
            $player->estatus = 1;
            $player->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function editplayer($id){
        $player = Player::find($id);
        return response()->json($player);
    }

    public function deleteplayer($id){
        $player = Player::find($id);
        if ($player){
            $player->estatus = 3;
            $player->save();
            $player->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function getsrno($id){
        $player = Player::where('country_id',$id)->orderBy('id','desc')->first();
        $sr_no = isset($player->sr_no)?$player->sr_no+1:1;
        return response()->json(['sr_no' => $sr_no]);
    }
}
