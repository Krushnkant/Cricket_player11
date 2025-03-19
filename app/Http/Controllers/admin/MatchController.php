<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Matche;
use App\Models\MatchPlayer;
use App\Models\Player;
use App\Models\SeriesTeamPlayer;
use App\Models\Series;
use App\Models\SeriesTeam;
use App\Models\Team;
use App\Models\Stadium;
use App\Models\ProjectPage;
use App\Models\MatchCommentry;
use App\Models\MatchScoreboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatchController extends Controller
{
    public function index(){
        $action = "list";
        return view('admin.match.list',compact('action'));
    }

    public function matchplayer($id){
        $action = "matchplayer";
        // $users = Matche::leftJoin("series_teams",function($join) use($id) {
        //     $join->on("series_teams.series_id","=","matches.series_id")
        //         ->on("series_teams.team_id","=","matches.team1_id");
        // })->where('id',$id)->get();
        $match = Matche::where('id',$id)->first();
        $seriesteams1 = SeriesTeam::where('series_id',$match->series_id)->where('team_id',$match->team1_id)->first();
        $seriesteams2 = SeriesTeam::where('series_id',$match->series_id)->where('team_id',$match->team2_id)->first();
        $seriesteamplayer1 = SeriesTeamPlayer::where('series_team_id', $seriesteams1->id)->get();
        $seriesteamplayer2 = SeriesTeamPlayer::where('series_team_id', $seriesteams2->id)->get();

        $matchplayer1 = MatchPlayer::where('match_id',$id)->where('series_team_id',$match->team1_id)->get()->pluck('player_id')->toArray();
        $matchplayer2 = MatchPlayer::where('match_id',$id)->where('series_team_id',$match->team2_id)->get()->pluck('player_id')->toArray();
        if($matchplayer1 == ""){
            $matchplayer1 = array();
        }
        if($matchplayer2 == ""){
            $matchplayer2 = array();
        }
        return view('admin.match.list',compact('action','seriesteamplayer1','seriesteamplayer2','match','matchplayer1','matchplayer2'));
    }

    public function matchcommentry($id){
        $action = "matchcommentry";
        return view('admin.match.list',compact('action','id'));
    }

    public function matchscoreboard($id){
        $action = "matchscoreboard";
        return view('admin.match.list',compact('action','id'));
    }

    public function create(){
        $action = "create";
        $series = Series::where('estatus',1)->get();
        $teams = Team::where('estatus',1)->get();
        $stadiums = Stadium::where('estatus',1)->get();
        return view('admin.match.list',compact('action','series','teams','stadiums'));
        // return redirect('/faqs');
    }

    public function save(Request $request){
        $messages = [
            'serie_id.required' =>'Please provide a serie id',
            'team1_id.required' =>'Please provide a team 1 id',
            'team2_id.required' =>'Please select team 2 id',
            'match_type.required' =>'Please select match type',
            'stadium_id.required' =>'Please select stadium id',
            'start_date.required' =>'Please select start date',
        ];

        $validator = Validator::make($request->all(), [
            'serie_id' => 'required',
            'team1_id' => 'required',
            'team2_id' => 'required',
            'match_type' => 'required',
            'stadium_id' => 'required',
            'start_date' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        if (isset($request->action) && $request->action=="update"){
            $action = "update";
            $Matche = Matche::where('id',$request->match_id)->first();

            if(!$Matche){
                return response()->json(['status' => '400']);
            }

            $Matche->series_id = $request->serie_id;
            $Matche->team1_id = $request->team1_id;
            $Matche->team2_id = $request->team2_id;
            $Matche->eformat = $request->match_type;
            $Matche->stadium_id = $request->stadium_id;
            $Matche->start_date = $request->start_date;
            $Matche->win_team_id = isset($request->winner_team_id)?$request->winner_team_id:0;
            $Matche->team1_score = isset($request->team1_score)?$request->team1_score:"";
            $Matche->team2_score = isset($request->team2_score)?$request->team2_score:"";
            $Matche->win_text = isset($request->winning_statement)?$request->winning_statement:"";
        }
        else{
            $action = "add";
            $Matche = new Matche();
            $Matche->series_id = $request->serie_id;
            $Matche->team1_id = $request->team1_id;
            $Matche->team2_id = $request->team2_id;
            $Matche->eformat = $request->match_type;
            $Matche->stadium_id = $request->stadium_id;
            $Matche->start_date = $request->start_date;
            $Matche->win_team_id = isset($request->winner_team_id)?$request->winner_team_id:0;
            $Matche->team1_score = isset($request->team1_score)?$request->team1_score:"";
            $Matche->team2_score = isset($request->team2_score)?$request->team2_score:"";
            $Matche->win_text = isset($request->winning_statement)?$request->winning_statement:"";
            $Matche->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        }

        $Matche->save();

        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function savematchplayer(Request $request){
        $messages = [
            'match_player1.required' =>'Please provide a match player 1',
            'match_player2.required' =>'Please provide a match player 2',
            
        ];

        $validator = Validator::make($request->all(), [
            'match_player1' => 'required',
            'match_player2' => 'required',
            
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        
        if($request->match_id){
            $MatchPlayer = MatchPlayer::where('match_id',$request->match_id)->get();
            foreach($MatchPlayer as $Player){
                if(!in_array($Player->player_id,$request->match_player1)){
                    $seriesteam = MatchPlayer::find($Player->id);
                    $seriesteam->delete(); 
                }
            }

            foreach($MatchPlayer as $Player){
                if(!in_array($Player->player_id,$request->match_player2)){
                    $seriesteam = MatchPlayer::find($Player->id);
                    $seriesteam->delete(); 
                }
            }
            
            foreach($request->match_player1 as $matchplayer1){
                $SeriesTeamPlayer = MatchPlayer::where('match_id',$request->match_id)->where('player_id',$matchplayer1)->get();
                if(count($SeriesTeamPlayer) <= 0){
                    $seriesteam = New MatchPlayer();
                    $seriesteam->match_id = $request->match_id;
                    $seriesteam->team_id = $request->team1_id;
                    $seriesteam->player_id = $matchplayer1;
                    $seriesteam->save();
                }
            }
            
            foreach($request->match_player2 as $matchplayer2){
                $SeriesTeamPlayer = MatchPlayer::where('match_id',$request->match_id)->where('player_id',$matchplayer2)->get();
                if(count($SeriesTeamPlayer) <= 0){
                    $seriesteam = New MatchPlayer();
                    $seriesteam->match_id = $request->match_id;
                    $seriesteam->team_id = $request->team2_id;
                    $seriesteam->player_id = $matchplayer2;
                    $seriesteam->save();
                }

            }
        }
       

        return response()->json(['status' => '200', 'action' => 'update']);
    }

    public function allmatchlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'series',
                2 => 'team',
                3 => 'stadium',
                4 => 'eformat',
                5 => 'start_date',
                6 => 'action',
            );
            $totalData = Matche::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $Matche = Matche::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Matche =  Matche::WhereHas('series',function ($mainQuery) use($search) {
                    $mainQuery->where('name', 'Like', '%' . $search . '%');
                })->orWhereHas('team1',function ($mainQuery) use($search) {
                    $mainQuery->where('name', 'Like', '%' . $search . '%');
                })->orWhereHas('team2',function ($mainQuery) use($search) {
                    $mainQuery->where('name', 'Like', '%' . $search . '%');
                })->orWhereHas('stadium',function ($mainQuery) use($search) {
                    $mainQuery->where('name', 'Like', '%' . $search . '%');
                })

                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();


                $totalFiltered = count($Matche->toArray());
            }
            //print_r($Faqs); die;
            $data = array();

            if(!empty($Matche))
            {
                foreach ($Matche as $Match)
                {
                    $page_id = ProjectPage::where('route_url','admin.match.list')->pluck('id')->first();

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editmatchBtn" class="btn btn-gray text-blue btn-sm" data-id="' .$Match->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                        $action .= '<button id="editmatchplayerBtn" title="Match Players" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#matchplayerModel" onclick="" data-id="' .$Match->id. '"><i class="fa fa-users" aria-hidden="true"></i></button>';
                        $action .= '<button id="viewMatchCommentriesBtn" title="Commentry" class="btn btn-gray text-blue btn-sm"  data-id="' .$Match->id. '"><i class="fa fa-comment" aria-hidden="true"></i></button>';
                        $action .= '<button id="viewMatchScoreboardsBtn" title="Score Board" class="btn btn-gray text-blue btn-sm"  data-id="' .$Match->id. '"><i class="fa fa-signal" aria-hidden="true"></i></button>';
                        $action .= '<button id="countFantasyPoints" title="Calculate Fantasy Points" class="btn btn-gray text-blue btn-sm"  data-id="' .$Match->id. '"><i id="fantasy_btn_icon" class="fa fa-star" aria-hidden="true"></i><i id="fantasy_btn_loader" class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none; margin-left: 0;"></i></button>';
                    }
                    $action .= '<button id="deletematchBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeletematchModal" data-id="' .$Match->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    
                    $nestedData['series'] = $Match->series->name;
                    $nestedData['team'] = $Match->team1->name ." vs ". $Match->team2->name;
                    $nestedData['stadium'] = $Match->stadium->name;
                    $nestedData['match_type'] = matchType($Match->eformat);
                    $nestedData['start_date'] = date('Y-m-d H:i:s', strtotime($Match->start_date));
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

    public function allmatchcommentrylist($id,Request $request){
   
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'ball_number',
                2 => 'batsman',
                3 => 'bowler',
                4 => 'ball_status',
                5 => 'run',
                6 => 'out',
                7 => 'is_extra_run',
                8 => 'commentry',
            );
            $totalData = MatchCommentry::where('match_id',$id)->count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "ball_number";
                $dir = 'asc';
            }

            if(empty($request->input('search.value')))
            {
                $Matche = MatchCommentry::where('match_id',$id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Matche =  MatchCommentry::where('match_id',$id)->where('commentry', 'Like', '%' . $search . '%')
                ->orWhereHas('batsman',function ($mainQuery) use($search) {
                    $mainQuery->where('name', 'Like', '%' . $search . '%');
                })->orWhereHas('bowler',function ($mainQuery) use($search) {
                    $mainQuery->where('name', 'Like', '%' . $search . '%');
                })

                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();


                $totalFiltered = count($Matche->toArray());
            }
            //print_r($Faqs); die;
            $data = array();

            if(!empty($Matche))
            {
                foreach ($Matche as $Match)
                {
                   
                    $is_out = "No";
                    if($Match->is_out == 1){
                        $is_out = "Yes";
                    }

                    $is_boundary = "No";
                    if($Match->is_boundary == 1){
                        $is_boundary = "Yes";
                    }

                    $is_extra_run = "No";
                    if($Match->is_extra_run == 1){
                        $is_extra_run = "Yes";
                    }

                    $ball_status = " Is Out : ". $is_out;
                    $ball_status .= "<br> Is Boundary : ". $is_boundary;
                    $ball_status .= "<br> Is Extra Run : ". $is_extra_run;

                    $outbyfielder_name = isset($Match->outbyfielder)?$Match->outbyfielder->name:"-";
                    $runoutbatsman_name = isset($Match->runoutbatsman)?$Match->runoutbatsman->name:"-";
                    $out = " Out Type : ". outTypeNo($Match->out_type);
                    $out .= "<br> Out By Fielder : ". $outbyfielder_name;
                    $out .= "<br> Run Out Batsman : ".  $runoutbatsman_name;

                    $nestedData['ball_number'] = $Match->ball_number;
                    $nestedData['batsman'] = isset($Match->batsman)?$Match->batsman->name:"";
                    $nestedData['bowler'] = isset($Match->bowler)?$Match->bowler->name:"";
                    $nestedData['ball_type'] = bollTypeNo($Match->ball_type);
                    $nestedData['run'] = $Match->run;
                    $nestedData['ball_status'] = $ball_status;
                    $nestedData['out'] = $out;
                    $nestedData['commentry'] = $Match->commentry;
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

    public function allmatchscoreboardlist($id,Request $request){
        
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'player_id',
                2 => 'ball',
                3 => 'run',
                4 => 'four',
                5 => 'six',
                6 => 'strike_rate',
                7 => 'over',
                8 => 'ball_run',
                9 => 'maiden',
                10 => 'wicket',
                11 => 'wide',
                12 => 'noball',
                13 => 'economy_rate',
            );
            $totalData = MatchScoreboard::where('match_id',$id)->count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "id";
                $dir = 'asc';
            }

            if(empty($request->input('search.value')))
            {
                $Matche = MatchScoreboard::where('match_id',$id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Matche =  MatchScoreboard::where('match_id',$id)->where('commentry', 'Like', '%' . $search . '%')
                ->orWhereHas('batsman',function ($mainQuery) use($search) {
                    $mainQuery->where('name', 'Like', '%' . $search . '%');
                })->orWhereHas('bowler',function ($mainQuery) use($search) {
                    $mainQuery->where('name', 'Like', '%' . $search . '%');
                })

                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();


                $totalFiltered = count($Matche->toArray());
            }
            //print_r($Faqs); die;
            $data = array();

            if(!empty($Matche))
            {
                foreach ($Matche as $Match)
                {
                    $nestedData['player_id']        = isset($Match->player) ? $Match->player->name : "";
                    $nestedData['ball']             = ($Match->ball != "") ? $Match->ball : "-";
                    $nestedData['run']              = ($Match->run != "") ? $Match->run : "-";
                    $nestedData['four']             = ($Match->four != "") ? $Match->four : "-";
                    $nestedData['six']              = ($Match->six != "") ? $Match->six : "-";
                    $nestedData['strike_rate']      = ($Match->strike_rate != "") ? $Match->strike_rate : "-";
                    $nestedData['over']             = ($Match->over != "") ? $Match->over : "-";
                    $nestedData['ball_run']         = ($Match->ball_run != "") ? $Match->ball_run : "-";
                    $nestedData['maiden']           = ($Match->maiden != "") ? $Match->maiden : "-";
                    $nestedData['wicket']           = ($Match->wicket != "") ? $Match->wicket : "-";
                    $nestedData['wide']             = ($Match->wide != "") ? $Match->wide : "-";
                    $nestedData['noball']           = ($Match->noball != "") ? $Match->noball : "-";
                    $nestedData['economy_rate']     = ($Match->economy_rate != "") ? $Match->economy_rate : "-";
                    $nestedData['fantasy_point']    = ($Match->fantasy_point != "") ? $Match->fantasy_point: "-";
                    
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

    function editmatch($id){
        $action = "edit";
        $series = Series::where('estatus',1)->get();
        $teams = Team::where('estatus',1)->get();
        $stadiums = Stadium::where('estatus',1)->get();
        $match = Matche::where('id',$id)->first();
        $winnerids = [$match->team1_id,$match->team2_id];
        $winnerteams = Team::where('estatus',1)->whereIn('id',$winnerids)->get();
        return view('admin.match.list',compact('action','match','series','teams','stadiums','winnerteams'));
    }

    function editmatchplayer($id){
        $action = "edit";
        $series = Series::where('estatus',1)->get();
        $teams = Team::where('estatus',1)->get();
        $stadiums = Stadium::where('estatus',1)->get();
        $match = Matche::where('id',$id)->first();
        return view('admin.match.list',compact('action','match','series','teams','stadiums'));
    }

    public function deletematch($id){
        $Faqform = Matche::where('id', $id)->first();
        if ($Faqform){
            $Faqform->estatus = 3;
            $Faqform->save();
            $Faqform->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function countfantaypoint($id){
        
        $calculateFantasyPoint = calculateFantasyPoint($id);
        $MatchCommentryData = MatchCommentry::where('match_id', $id)->get();
        $MatchScoreboardData = MatchScoreboard::where('match_id',$id)->get();
        if($calculateFantasyPoint == 2) {
            return response()->json(['msg' => 'Match not found.', 'status' => '404']);
            

        } else if($calculateFantasyPoint == 1) {
            return response()->json(['msg' => 'Fantasy point has been updated successfully.','status' => '200']);
        }
        return response()->json(['msg' => 'Something went wrong. Please try again.', 'status' => '400']);
    }

}
