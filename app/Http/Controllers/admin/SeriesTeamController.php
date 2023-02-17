<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Player;
use App\Models\SeriesTeam;
use App\Models\SeriesTeamPlayer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SeriesTeamController extends Controller
{
    public function index($id){
        $players =  Player::where('estatus',1)->get();
        return view('admin.seriesteam.list',compact('id','players'));
    }

    public function addorupdateseriesteam(Request $request){
        $messages = [
            'series_team_players.required' =>'Please provide a series team players',
        ];

        $validator = Validator::make($request->all(), [
            'series_team_players' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        
        if($request->seriesteam_id){
            $SeriesTeamPlayer = SeriesTeamPlayer::where('series_team_id',$request->seriesteam_id)->get();
            foreach($SeriesTeamPlayer as $TeamPlayer){
                if(!in_array($TeamPlayer->player_id,$request->series_team_players)){
                    $seriesteam = SeriesTeamPlayer::find($TeamPlayer->id);
                    $seriesteam->delete(); 
                }
            }
            
            foreach($request->series_team_players as $series_team_player){
                $SeriesTeamPlayer = SeriesTeamPlayer::where('series_team_id',$request->seriesteam_id)->where('player_id',$series_team_player)->get();
                if(count($SeriesTeamPlayer) <= 0){
                    $seriesteam = New SeriesTeamPlayer();
                    $seriesteam->series_team_id = $request->seriesteam_id;
                    $seriesteam->player_id = $series_team_player;
                    $seriesteam->save();
                }

            } 
        }


    
        $seriesteam->save();
        return response()->json(['status' => '200', 'action' => 'update']);
    }

    public function allseriesteamslist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'name',
                2 => 'created_at',
                3 => 'action',
            );

            $totalData = SeriesTeam::where('series_id',$request->input('series_id'))->count();

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
                $seriesteams = SeriesTeam::where('series_id',$request->input('series_id'))->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $seriesteams =  SeriesTeam::where('series_id',$request->input('series_id'))->where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = SeriesTeam::where('series_id',$request->input('series_id'))->count();
            }

            $data = array();

            if(!empty($seriesteams))
            {
                foreach ($seriesteams as $seriesteam)
                {
                    $page_id = ProjectPage::where('route_url','admin.seriesteam.list')->pluck('id')->first();

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editseriesteamBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#seriesteamModel" onclick="" data-id="' .$seriesteam->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteseriesteamBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteseriesteamModel" onclick="" data-id="' .$seriesteam->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['name'] = $seriesteam->team->name;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($seriesteam->created_at));
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

   

    public function editseriesteam($id){
       // $seriesteam = SeriesTeamPlayer::find($id);
        $seriesteamplayer = SeriesTeamPlayer::where('series_team_id',$id)->get()->pluck('player_id')->toArray();
        $data['seriesteamid'] = $id;
        $data['seriesteamplayer'] = $seriesteamplayer;
        return response()->json($data);
    }

    public function deleteseriesteam($id){
        $seriesteam = SeriesTeam::find($id);
        if ($seriesteam){
            $seriesteam->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function getsrno($id){
        $seriesteam = SeriesTeam::where('series_id',$id)->orderBy('id','desc')->first();
        $sr_no = isset($seriesteam->sr_no)?$seriesteam->sr_no+1:1;
        return response()->json(['sr_no' => $sr_no]);
    }
}
