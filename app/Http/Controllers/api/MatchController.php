<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\MatchCommentry;
use App\Models\Matche;
use App\Models\SeriesTeam;
use App\Models\SeriesTeamPlayer;
use App\Models\MatchScoreboard;
use App\Models\MatchPlayer;
use App\Models\Player;
use App\Models\Team;
use App\Models\Series;
use App\Models\Stadium;
use App\Models\Tournament;
use App\Models\User;
use App\Models\News;
use App\Models\AppOpenLog;
use App\Models\UserDevice;
use App\Models\UserCoupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MatchController extends BaseController
{
    public function players(Request $request){

        $validator = Validator::make($request->all(), [
            'match_id' => 'required',
            'players' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        if(count($request->players) == 2){
            $country1_id = 0;
            $team_id = 0;
            $SeriesT_id = 0;
            foreach($request->players as $player){

                foreach($player as $key => $player){
                    $matche =  Matche::with('series.tournament')->where('id',$request->match_id)->first();
                    if($key == 0){
                        $country1 = Country::where('name',$player)->first();
                        if (!$country1){
                            $cou1 = new Country();
                            $cou1->name = $player;
                            $cou1->save();
                            $country1_id = $cou1->id;
                        }else{
                            $country1_id = $country1->id;
                        }

                        $team = Team::where('name',$player)->first();
                        if (!$team){
                            $team = new Team();
                            $team->name = $player;
                            $team->tournament_id = isset($matche->series->tournament->id)?$matche->series->tournament->id:0;
                            $team->save();
                            $team_id = $team->id;
                        }else{
                            $team_id = $country1->id;
                        }

                        if(isset($matche->series->id) && $team_id > 0){
                            $SeriesTeam = SeriesTeam::where('series_id',$matche->series->id)->where('team_id',$team_id)->first();
                            if (!$SeriesTeam){
                                $SeriesT = new SeriesTeam();
                                $SeriesT->team_id = $team_id;
                                $SeriesT->series_id = isset($matche->series->id)?$matche->series->id:0;
                                $SeriesT->save();
                                $SeriesT_id = $SeriesT->id;
                            }else{
                                $SeriesT_id = $SeriesTeam->id;
                            }
                        }

                    }else{
                        $player1s = Player::where('name',$player)->first();
                        if (!$player1s){
                            $player1 = new Player();
                            $player1->name = $player;
                            $player1->country_id = $country1_id;
                            $player1->save();
                            $player1_id = $player1->id;
                        }else{
                            $player1_id = $player1s->id;
                        }

                        $seriesteam = SeriesTeamPlayer::where('player_id',$player1_id)->where('series_team_id',1)->first();
                        if (!$seriesteam){
                            $seriesteam1 = new SeriesTeamPlayer();
                            $seriesteam1->series_team_id = $SeriesT_id;
                            $seriesteam1->player_id = $player1_id;
                            $seriesteam1->save();
                        }

                        $matchplayer = MatchPlayer::where('match_id',$request->match_id)->where('player_id',$player1_id)->first();
                        if (!$matchplayer){
                            $matchplayer1 = new MatchPlayer();
                            $matchplayer1->match_id = $request->match_id;
                            $matchplayer1->player_id = $player1_id;
                            $matchplayer1->series_team_id =1;
                            $matchplayer1->save();
                        }
                    }
                }
            }
        }
        return $this->sendResponseSuccess("Players Added.");
    }

    public function match_commentries(Request $request){
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'match_id' => 'required',
            'match_commentries' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        foreach($request->match_commentries as $commentries){

            $batsmanplayer = Player::where('name',$commentries['batsman'])->first();
            if (!$batsmanplayer){
                $player = new Player();
                $player->name = $commentries['batsman'];
                $player->save();
                $batsman_id = $player->id;
            }else{
                $batsman_id = $batsmanplayer->id;
            }


            $ballerplayer = Player::where('name',$commentries['baller'])->first();
            if (!$ballerplayer){
                $player = new Player();
                $player->name = $commentries['baller'];
                $player->save();
                $baller_id = $player->id;
            }else{
                $baller_id = $ballerplayer->id;
            }

            if($commentries['runOutPlayer'] != "" && $commentries['runOutPlayer'] != null){
                $ballerplayer = Player::where('name',$commentries['runOutPlayer'])->first();
                if (!$ballerplayer){
                    $player = new Player();
                    $player->name = $commentries['runOutPlayer'];
                    $player->save();
                    $runOutPlayer = $player->id;
                }else{
                    $runOutPlayer = $ballerplayer->id;
                }
            }else{
               $runOutPlayer = 0;
            }

            $runOutPlayer1 = array();
            if($commentries['outBy'] != "" && $commentries['outBy'] != null){
                $outBy_array = explode(',',$commentries['outBy']);
                foreach($outBy_array as $outBy){
                    $outByplayer = Player::where('name',$outBy)->first();
                    if(!$outByplayer){
                        $player = new Player();
                        $player->name = $outBy;
                        $player->save();
                        $runOutPlayer1[] = $player->id;
                    }else{
                        $runOutPlayer1[] = $outByplayer->id;
                    }
                }
            }


            $matchcommentry = new MatchCommentry();
            $matchcommentry->match_id = $request->match_id;
            $matchcommentry->batsman_id = $batsman_id;
            $matchcommentry->bowler_id = $baller_id;
            $matchcommentry->ball_number = $commentries['ball'];
            $matchcommentry->ball_type = bollType($commentries['ballType']);
            $matchcommentry->run = $commentries['ballRun'];
            $matchcommentry->is_boundary = $commentries['isBoundry'];
            $matchcommentry->is_out = $commentries['isOut'];
            if($commentries['isOut'] == 1){
               $matchcommentry->out_type = outType($commentries['outType']);
            }
            $matchcommentry->out_by_fielder1_id = (count($runOutPlayer1) > 0) ? implode(',',$runOutPlayer1) : "";
            $matchcommentry->run_out_batsman_id = $runOutPlayer;
            $matchcommentry->is_extra_run = $commentries['isExtraRun'];
            $matchcommentry->commentry = $commentries['commentry'];
            $matchcommentry->save();
        }

        return $this->sendResponseSuccess("Commentry Added.");
    }

    public function match_scoreboards(Request $request){
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'match_id' => 'required',
            'match_scoreboards' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        foreach($request->match_scoreboards as $scoreboards){

            $batsmanplayer = Player::where('name',$scoreboards['Player'])->first();
            if (!$batsmanplayer){
                $player = new Player();
                $player->name = $scoreboards['Player'];
                $player->save();
                $player_id = $player->id;
            }else{
                $player_id = $batsmanplayer->id;
            }

            $matchcommentry = new MatchScoreboard();
            $matchcommentry->match_id = $request->match_id;
            $matchcommentry->player_id = $player_id;
            $matchcommentry->ball = $scoreboards['BatterBall'];
            $matchcommentry->run = $scoreboards['BatterRun'];
            $matchcommentry->four = $scoreboards['Batter4s'];
            $matchcommentry->six = $scoreboards['Batter6s'];
            $matchcommentry->strike_rate = $scoreboards['BatterSR'];
            $matchcommentry->over = $scoreboards['bowlingO'];
            $matchcommentry->ball_run = $scoreboards['bowlingR'];
            $matchcommentry->maiden = $scoreboards['bowlingM'];
            $matchcommentry->wicket = $scoreboards['bowlingW'];
            $matchcommentry->wide = $scoreboards['bowlingWd'];
            $matchcommentry->noball = $scoreboards['bowlingNb'];
            $matchcommentry->economy_rate = $scoreboards['bowlingEco'];
            $matchcommentry->save();
        }

        return $this->sendResponseSuccess("Scoreboards Added.");
    }

    public function match(Request $request){

        $MatchCommentries =  MatchCommentry::select('bat.name as batsman_name','bol.name as bowler_name','match_commentries.*')->leftJoin('players as bat', 'bat.id', '=', 'match_commentries.batsman_id')->leftJoin('players as bol', 'bol.id', '=', 'match_commentries.bowler_id')->where('match_commentries.match_id',1)->get();

        $data['MatchCommentries'] = $MatchCommentries;

        $MatchScoreboard =  MatchScoreboard::select('bat.name as player_name','match_scoreboards.*')->leftJoin('players as bat', 'bat.id', '=', 'match_scoreboards.player_id')->where('match_scoreboards.match_id',1)->get();
        $data['MatchScoreboard'] = $MatchScoreboard;
        return $this->sendResponseWithData($data,"Scoreboards Added.");
    }

    public function home(Request $request){
        $upcominglimit = ($request->upcominglimit)?$request->upcominglimit:6;
        $upcomingmatches =  Matche::where('start_date', '>=', now()->toDateTimeString())->orderBy('start_date','ASC')->paginate($upcominglimit);
        $upcoming_matches_arr = array();
        foreach ($upcomingmatches as $match){
            $temp = array();
            $temp['id'] = $match->id;
            $temp['serie'] = isset($match->series)?$match->series->name:"";
            $temp['serie_type'] = matchType(isset($match->series)?$match->series->series_type:0);
            $temp['tournament'] = isset($match->series->tournament)?$match->series->tournament->name:"";
            $temp['team1_id'] = $match->team1_id;
            $temp['team1'] = isset($match->team1)?$match->team1->name:"";
            $temp['team1_image'] = isset($match->team1)?url('images/team/'.$match->team1->thumb_img):"";
            $temp['team2_id'] = $match->team2_id;
            $temp['team2'] = isset($match->team2)?$match->team2->name:"";
            $temp['team2_image'] = isset($match->team2)?url('images/team/'.$match->team2->thumb_img):"";
            $temp['stadium'] = isset($match->stadium)?$match->stadium->name:"";
            $temp['stadium_country'] = isset($match->stadium->coutry)?$match->stadium->coutry->name:"";
            $temp['stadium_state'] = isset($match->stadium)?$match->stadium->state:"";
            $temp['stadium_city'] = isset($match->stadium)?$match->stadium->city:"";
            $temp['match_type'] = matchType($match->match_type);
            $temp['start_date'] = $match->start_date;
            array_push($upcoming_matches_arr,$temp);
        }
        $data['upcoming_matches'] = $upcoming_matches_arr;
        return $this->sendResponseWithData($data,"Home Retrieved Successfully.");
    }

    public function upcoming_series(Request $request){
        $limit = ($request->upcominglimit)?$request->upcominglimit:6;

        $upcomingseries =  Series::where('start_date', '>=', now()->toDateTimeString())->orderBy('start_date','ASC')->paginate($limit);
        $upcoming_series_arr = array();
        foreach ($upcomingseries as $serie){
            $temp = array();
            $temp['id'] = $serie->id;
            $temp['serie'] = $serie->name;
            $temp['serie_type'] = matchType($serie->series_type);
            $temp['tournament'] = isset($serie->tournament)?$serie->tournament->name:"";
            $temp['start_date'] = $serie->start_date;
            $temp['end_date'] = $serie->end_date;
            array_push($upcoming_series_arr,$temp);
        }
        $data['upcoming_series'] = $upcoming_series_arr;

        return $this->sendResponseWithData($data,"Upcoming Series Retrieved Successfully.");
    }

    // public function matchlist(Request $request){
    //     $series = Series::where('id',$request->serie_id)->first();
    //     if (!$series){
    //         return $this->sendError("Series Not Exist", "Not Found Error", []);
    //     }

    //     $matches =  Matche::where('series_id',$request->serie_id)->where('estatus',1)->get();
    //     $matches_arr = array();
    //     foreach ($matches as $match){
    //         $temp = array();
    //         $temp['id'] = $match->id;
    //         $temp['serie'] = isset($match->series)?$match->series->name:"";
    //         $temp['serie_type'] = matchType(isset($match->series)?$match->series->series_type:0);
    //         $temp['tournament'] = isset($match->series->tournament)?$match->series->tournament->name:"";
    //         $temp['team1_id'] = $match->team1_id;
    //         $temp['team1'] = isset($match->team1)?$match->team1->name:"";
    //         $temp['team1_image'] = isset($match->team1)?url('images/team/'.$match->team1->thumb_img):"";
    //         $temp['team2_id'] = $match->team2_id;
    //         $temp['team2'] = isset($match->team2)?$match->team2->name:"";
    //         $temp['team2_image'] = isset($match->team2)?url('images/team/'.$match->team2->thumb_img):"";
    //         $temp['stadium'] = isset($match->stadium)?$match->stadium->name:"";
    //         $temp['stadium_country'] = isset($match->stadium->coutry)?$match->stadium->coutry->name:"";
    //         $temp['stadium_state'] = isset($match->stadium)?$match->stadium->state:"";
    //         $temp['stadium_city'] = isset($match->stadium)?$match->stadium->city:"";
    //         $temp['match_type'] = matchType($match->match_type);
    //         $temp['start_date'] = $match->start_date;
    //         array_push($matches_arr,$temp);
    //     }
    //     $data['matches'] = $matches_arr;
    //     return $this->sendResponseWithData($data,"Matches Retrieved Successfully.");
    // }

    public function matchList(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'seriesId' => 'required|integer',
            'eFormat' => 'nullable|string',
            'limit' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->sendError([], "Validation Error", $validator->errors());
        }

        // Query Matches
        $query = Matche::where('estatus', 1);

        // If seriesId is not 0, filter by seriesId
        if ($request->seriesId != 0) {
            $query->where('series_id', $request->seriesId);
        }

        // Apply eFormat filter
        if ($request->eFormat) {
            $query->where('eFormat', $request->eFormat);
        }

        // Apply limit filter
        if ($request->limit) {
            $query->limit($request->limit);
        }

        // Fetch Matches with Relationships
        $matches = $query->get()->map(function ($match) {
            return [
                'matchId' => $match->id,
                'seriesId' => $match->series_id,
                'matchTitle' => $match->title ?? "",
                'seriesTeam1Id' => $match->team1_id,
                'team1Id' => $match->team1_id,
                'team1Name' => $match->team1->name ?? "",
                'team1ShortName' => $match->team1->short_name ?? "",
                'team1Logo' => isset($match->team1) ? url('images/team/' . $match->team1->thumb_img) : "",
                'seriesTeam2Id' => $match->team2_id,
                'team2Id' => $match->team2_id,
                'team2Name' => $match->team2->name ?? "",
                'team2ShortName' => $match->team2->short_name ?? "",
                'team2Logo' => isset($match->team2) ? url('images/team/' . $match->team2->thumb_img) : "",
                'eFormat' => matchType($match->eformat),
                'stadiumName' => $match->stadium->name ?? "",
                'stadiumLocation' => trim(implode(', ', array_filter([
                    $match->stadium->city ?? "",
                    $match->stadium->state ?? "",
                    $match->stadium->coutry->name ?? "",
                ]))),
                'matchTime' => $match->start_date,
                'team1Score' => $match->team1_score ?? "",
                'team2Score' => $match->team2_score ?? "",
                'winningText' => $match->win_text ?? "",
                'winningTeam' => $match->win_team->name ?? ""
            ];
        });

        // Return Response
        return $this->sendResponseWithData($matches, "Matches retrieved successfully");
    }


    public function otherlist(Request $request){
        $tournaments =  Tournament::where('estatus',1)->get();
        $tournaments_arr = array();
        foreach ($tournaments as $tournament){
            $teams =  Team::where('tournament_id',$tournament->id)->where('estatus',1)->get();
            $teams_arr = array();
            foreach ($teams as $team){
                $temp = array();
                $temp['id'] = $team->id;
                $temp['name'] = isset($team->name)?$team->name:"";
                $temp['short_name'] = isset($team->short_name)?$team->short_name:"";
                $temp['image'] = isset($team->thumb_img)?url('images/team/'.$team->thumb_img):"";
                array_push($teams_arr,$temp);
            }

            $temp = array();
            $temp['id'] = $tournament->id;
            $temp['name'] = isset($tournament->name)?$tournament->name:"";
            $temp['short_name'] = isset($tournament->short_name)?$tournament->short_name:"";
            $temp['image'] = isset($tournament->thumb_img)?url('images/tournament/'.$tournament->thumb_img):"";
            $temp['teams'] = $teams_arr;
            array_push($tournaments_arr,$temp);
        }

        $stadiums =  Stadium::where('estatus',1)->get();
        $stadiums_arr = array();
        foreach ($stadiums as $stadium){
            $temp = array();
            $temp['id'] = $stadium->id;
            $temp['name'] = isset($stadium->name)?$stadium->name:"";
            $temp['short_name'] = isset($stadium->short_name)?$stadium->short_name:"";
            $temp['country'] = isset($stadium->coutry)?$stadium->coutry->name:"";
            $temp['state'] = isset($stadium->state)?$stadium->state:"";
            $temp['city'] = isset($stadium->city)?$stadium->city:"";
            array_push($stadiums_arr,$temp);
        }

        $match_type_arr = array();
        $match_type_arr[0]['id'] = 1;
        $match_type_arr[0]['name'] = "T20s";
        $match_type_arr[1]['id'] = 2;
        $match_type_arr[1]['name'] = "ODIs";

        $data['tournaments'] = $tournaments_arr;
        $data['stadiums'] = $stadiums_arr;
        $data['match_type'] = $match_type_arr;
        return $this->sendResponseWithData($data,"Other List Retrieved Successfully.");
    }

    public function teamvsteam(Request $request){
      
        $totalmatches = Matche::where(function($q) use($request) {
            $q->where(['team1_id'=>$request->team1_id,'team2_id'=>$request->team2_id])
               ->orWhere(['team1_id'=>$request->team2_id,'team2_id'=>$request->team1_id]);
             });
            if(isset($request->match_type)){
                $totalmatches = $totalmatches->where('match_type',$request->match_type);
            }
            if(isset($request->stadium_id)){
                $totalmatches = $totalmatches->where('stadium_id',$request->stadium_id);
            }
            $totalmatches = $totalmatches->count();

        $team1_winner_matches = Matche::where(function($q) use($request) {
            $q->where(['team1_id'=>$request->team1_id,'team2_id'=>$request->team2_id])
               ->orWhere(['team1_id'=>$request->team2_id,'team2_id'=>$request->team1_id]);
             })->where('win_team_id',$request->team1_id);
             if(isset($request->match_type)){
                 $team1_winner_matches = $team1_winner_matches->where('match_type',$request->match_type);
             }
             if(isset($request->stadium_id)){
                 $team1_winner_matches = $team1_winner_matches->where('stadium_id',$request->stadium_id);
             }
             $team1_winner_matches = $team1_winner_matches->count();

        $team2_winner_matches = Matche::where(function($q) use($request) {
            $q->where(['team1_id'=>$request->team1_id,'team2_id'=>$request->team2_id])
                ->orWhere(['team1_id'=>$request->team2_id,'team2_id'=>$request->team1_id]);
                })->where('win_team_id',$request->team2_id);
                if(isset($request->match_type)){
                    $team2_winner_matches = $team2_winner_matches->where('match_type',$request->match_type);
                }
                if(isset($request->stadium_id)){
                    $team2_winner_matches = $team2_winner_matches->where('stadium_id',$request->stadium_id);
                }
                $team2_winner_matches = $team2_winner_matches->count();

        $noresultmatches = Matche::where(function($q) use($request) {
            $q->where(['team1_id'=>$request->team1_id,'team2_id'=>$request->team2_id])
                ->orWhere(['team1_id'=>$request->team2_id,'team2_id'=>$request->team1_id]);
                })->where('win_team_id',0);
                if(isset($request->match_type)){
                    $noresultmatches = $noresultmatches->where('match_type',$request->match_type);
                }
                if(isset($request->stadium_id)){
                    $noresultmatches = $noresultmatches->where('stadium_id',$request->stadium_id);
                }
                $noresultmatches = $noresultmatches->count();



        $data['total_matches'] = $totalmatches;
        $data['team1_winner_matches'] = $team1_winner_matches;
        $data['team1_loss_matches'] = $team2_winner_matches;
        $data['team2_winner_matches'] = $team2_winner_matches;
        $data['team2_loss_matches'] = $team1_winner_matches;
        $data['no_result_match'] = $noresultmatches;

        $matches = Matche::where(function($q) use($request) {
            $q->where(['team1_id'=>$request->team1_id,'team2_id'=>$request->team2_id])
                ->orWhere(['team1_id'=>$request->team2_id,'team2_id'=>$request->team1_id]);
                });
                if(isset($request->match_type)){
                    $matches = $matches->where('match_type',$request->match_type);
                }
                if(isset($request->stadium_id)){
                    $matches = $matches->where('stadium_id',$request->stadium_id);
                }
                $matches = $matches->get();

        $matches_arr = array();
        foreach ($matches as $match){
            $temp = array();
            $temp['id'] = $match->id;
            $temp['serie'] = isset($match->series)?$match->series->name:"";
            $temp['serie_type'] = matchType(isset($match->series)?$match->series->series_type:0);
            $temp['tournament'] = matchType(isset($match->series->tournament)?$match->series->tournament->name:"");
            $temp['team1_id'] = $match->team1_id;
            $temp['team1'] = isset($match->team1)?$match->team1->name:"";
            $temp['team1_image'] = isset($match->team1)?url('images/team/'.$match->team1->thumb_img):"";
            $temp['team2_id'] = $match->team2_id;
            $temp['team2'] = isset($match->team2)?$match->team2->name:"";
            $temp['team2_image'] = isset($match->team2)?url('images/team/'.$match->team2->thumb_img):"";
            $temp['stadium'] = isset($match->stadium)?$match->stadium->name:"";
            $temp['stadium_country'] = isset($match->stadium->coutry)?$match->stadium->coutry->name:"";
            $temp['stadium_state'] = isset($match->stadium)?$match->stadium->state:"";
            $temp['stadium_city'] = isset($match->stadium)?$match->stadium->city:"";
            $temp['match_type'] = matchType($match->match_type);
            $temp['winner_team_id'] = $match->win_team_id;
            $temp['team1_score'] = $match->team1_score;
            $temp['team2_score'] = $match->team2_score;
            $temp['winning_statement'] = $match->winning_statement;
            $temp['start_date'] = $match->start_date;
            array_push($matches_arr,$temp);
        }
        $data['matches'] = $matches_arr;

        return $this->sendResponseWithData($data,"Tournament Team Retrieved Successfully.");
    }

    public function countrylist(Request $request){
        $countries =  Country::where('estatus',1)->get();
        $countries_arr = array();
        foreach ($countries as $country){
            $temp = array();
            $temp['id'] = $country->id;
            $temp['name'] = isset($country->name)?$country->name:"";
            $temp['image'] = isset($country->thumb_img)?url('images/country/'.$country->thumb_img):"";
            array_push($countries_arr,$temp);
        }

        return $this->sendResponseWithData($countries_arr,"Team List Retrieved Successfully.");
    }

    public function playerlist(Request $request){
        $players =  Player::where('estatus',1);
        if(isset($request->country_id)){
            $players = $players->where('country_id',$request->country_id);
        }
        $players = $players->get();
        $players_arr = array();
        foreach ($players as $player){
            $temp = array();
            $temp['id'] = $player->id;
            $temp['name'] = isset($player->name)?$player->name:"";
            $temp['image'] = isset($player->thumb_img)?url('images/player/'.$player->thumb_img):"";
            $temp['player_type'] = playerType($player->player_type);
            $temp['batting_style'] = battingStyle($player->batting_style);
            $temp['bowling_style'] = bowlingStyle($player->bowling_style);
            $temp['bowling_arm'] = bowlingArm($player->bowling_arm);
            array_push($players_arr,$temp);
        }

        return $this->sendResponseWithData($players_arr,"Player List Retrieved Successfully.");
    }

    public function playervsplayer(Request $request){
        $matchcommentries = MatchCommentry::where(['batsman_id'=>$request->player1_id,'bowler_id'=>$request->player2_id]);
            if(isset($request->match_type)){
                $matchcommentries = $matchcommentries->where('match_type',$request->match_type);
            }
            if(isset($request->stadium_id)){
                $matchcommentries = $matchcommentries->where('stadium_id',$request->stadium_id);
            }
            $matchcommentries = $matchcommentries->get();

            $ballfaced = $matchcommentries->count();
            $totalmatch = $matchcommentries->groupBy('match_id')->count();
            $match_runs = $matchcommentries->sum('run');
            $total4 = $matchcommentries->where('run',4)->count();
            $total6 = $matchcommentries->where('run',6)->count();
            $total1 = $matchcommentries->where('run',1)->count();
            $total2 = $matchcommentries->where('run',2)->count();
            $total3 = $matchcommentries->where('run',3)->count();
            $duckout = $matchcommentries->where('is_out',1)->count();
            $runavg = $matchcommentries->avg('run');

            $data['total_match'] =  $totalmatch;
            $data['match_runs'] =  (int) $match_runs;
            $data['ballfaced'] =   $ballfaced;
            $data['total4'] =  (int) $total4;
            $data['total6'] =  (int) $total6;
            $data['total1'] =  (int) $total1;
            $data['total2'] =  (int) $total2;
            $data['total3'] =  (int) $total3;
            $data['duckout'] =  (int) $duckout;
            $data['runavg'] =   number_format((float)$runavg, 2, '.', '');
            $data['strikerate'] =   $this->strikerate($ballfaced,$match_runs);


            $commentry_arr = array();
            foreach ($matchcommentries as $ball){
                $temp = array();
                $temp['ball_number'] = $ball->ball_number;
                $temp['commentry'] = isset($ball->commentry)?$ball->commentry:"";
                array_push($commentry_arr,$temp);
            }
            $data['commentry'] = $commentry_arr;
            //$data['higthest_runs'] =   max($higthest_runs);

            return $this->sendResponseWithData($data,"Player Vs Player Retrieved Successfully.");
    }

    public function strikerate($bowls, $runs)
    {
        $z = 0;
        $z = ($runs / $bowls) * 100;
        return number_format((float)$z, 2, '.', '');
    }

    public function playervsteam(Request $request)
    {

        $match_ids = MatchPlayer::where('player_id',$request->player_id);
        if(isset($request->is_match_all) && $request->is_match_all == 0){
           $match_ids = $match_ids->where('series_team_id','<>',$request->team_id);
        }
        $match_ids = $match_ids->get()->pluck('match_id');
        $matchh_ids = Matche::whereIn('id',$match_ids);
        if(isset($request->match_type)){
            $matchh_ids = $matchh_ids->where('match_type',$request->match_type);
        }
        if(isset($request->stadium_id)){
            $matchh_ids = $matchh_ids->where('stadium_id',$request->stadium_id);
        }
        $matchh_ids = $matchh_ids->where(function($q) use($request) {
            $q->where(['team1_id'=>$request->team_id])
               ->orWhere(['team2_id'=>$request->team_id]);
             })->get()->pluck('id');


        $matchcommentry = MatchCommentry::whereIn('match_id',$matchh_ids)->where('batsman_id',$request->player_id);

            //$matchcommentries = $matchcommentries->distinct('match_id')->count();
            $matchcommentry = $matchcommentry->get();

            $matchscoreboard = MatchScoreboard::whereIn('match_id',$matchh_ids)->where('player_id',$request->player_id)->get();

            $ballfaced = $matchscoreboard->sum('ball');
            $totalmatch = $matchscoreboard->count();
            $match_runs = $matchscoreboard->sum('run');
            $total4 = $matchscoreboard->sum('four');
            $total6 = $matchscoreboard->sum('six');
            $total1 = $matchcommentry->where('run',1)->count();
            $total2 = $matchcommentry->where('run',2)->count();
            $total3 = $matchcommentry->where('run',3)->count();
            $duckout = $matchcommentry->where('is_out',1)->count();
            $runavg = $matchscoreboard->avg('run');
            $strikerate = $matchscoreboard->avg('strike_rate');
            $over = $matchscoreboard->sum('over');
            $ball_run = $matchscoreboard->sum('ball_run');
            $maiden = $matchscoreboard->sum('maiden');
            $wicket = $matchscoreboard->sum('wicket');
            $wide = $matchscoreboard->sum('wide');
            $noball = $matchscoreboard->sum('noball');
            $economy_rate = $matchscoreboard->avg('economy_rate');


            $higthest_runs = MatchCommentry::select([\DB::raw('SUM(run) AS score')])->whereIn('match_id',$matchh_ids)
            ->where('batsman_id',$request->player_id)->groupBy('match_id')->get()->pluck('score')->toArray();

            $data['total_match'] =  $totalmatch;
            $data['match_runs'] =   $match_runs;
            $data['ballfaced'] =   $ballfaced;
            $data['total4'] =   $total4;
            $data['total6'] =   $total6;
            $data['total1'] =   $total1;
            $data['total2'] =   $total2;
            $data['total3'] =   $total3;
            $data['duckout'] =   $duckout;
            $data['higthest_runs'] = (!empty($higthest_runs))? max($higthest_runs):0;
            $data['runavg'] =   number_format((float)$runavg, 2, '.', '');
            $data['strikerate'] =  $strikerate;
            $data['over'] =  $over;
            $data['ball_run'] =  $ball_run;
            $data['maiden'] =  $maiden;
            $data['wicket'] =  $wicket;
            $data['wide'] =  $wide;
            $data['noball'] =  $noball;
            $data['economy_rate'] =  $economy_rate;


            $commentry_arr = array();
            foreach ($matchcommentry as $ball){
                $temp = array();
                $temp['ball_number'] = $ball->ball_number;
                $temp['commentry'] = isset($ball->commentry)?$ball->commentry:"";
                array_push($commentry_arr,$temp);
            }
            $data['commentry'] = $commentry_arr;
            //$data['higthest_runs'] =   max($higthest_runs);

            return $this->sendResponseWithData($data,"Player Retrieved Successfully.");
    }

    public function player_profile(Request $request){
        $player = Player::where('id',$request->player_id)->first();
        if (!$player){
            return $this->sendError("Player Not Exist", "Not Found Error", []);
        }

        $matchh_ids = Matche::where('estatus',1);
        if(isset($request->match_type)){
            $matchh_ids = $matchh_ids->where('match_type',$request->match_type);
        }
        if(isset($request->stadium_id)){
            $matchh_ids = $matchh_ids->where('stadium_id',$request->stadium_id);
        }
        $matchh_ids = $matchh_ids->get()->pluck('id');

        $temp = array();
        $temp['id'] = $player->id;
        $temp['name'] = $player->name;
        $temp['image'] = ($player->thumb_img != "")?url('images/player/'.$player->thumb_img):"";
        $temp['player_type'] = playerType($player->player_type);
        $temp['batting_style'] = battingStyle($player->batting_style);
        $temp['bowling_style'] = bowlingStyle($player->bowling_style);
        $temp['bowling_arm'] = bowlingArm($player->bowling_arm);

        $matchscoreboard = MatchScoreboard::whereIn('match_id',$matchh_ids)->where('player_id',$request->player_id)->get();

        $temp['totalmatch'] = $matchscoreboard->count();
        $temp['match_runs'] = $matchscoreboard->sum('run');
        $temp['higthest_runs'] = $matchscoreboard->max('run');
        $temp['runavg'] = $matchscoreboard->avg('run');
        $temp['strikerate'] = $matchscoreboard->avg('strike_rate');
        $temp['maiden'] = $matchscoreboard->sum('maiden');
        $temp['wicket'] = $matchscoreboard->sum('wicket');

        return $this->sendResponseWithData($temp,"Player Profile Retrieved Successfully.");
    }

    public function splashData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId'          => 'nullable|integer',
            'deviceId'        => 'required|string',
            'eDeviceType'     => 'required|in:android,ios',
            'brand'           => 'nullable|string',
            'model'           => 'nullable|string',
            'device'          => 'nullable|string',
            'manufacturer'    => 'nullable|string',
            'osVersion'       => 'nullable|string',
            'appVersionName'  => 'nullable|string',
            'utmReferrer'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError([], "Validation Error", $validator->errors());
        }

        $loginUserId = 0;
        $device_id = $request->deviceId;
        $user_id = $request->userId;
        if ($user_id > 0) {

            $user = User::find($user_id);
            $loginUserId = $user->login_user_id;

        } else {
            $user = new User();
            $user->login_user_id = $loginUserId;
            $user->role = 3;
            $user->eUserType = 1;
            $user->username = $device_id;
            $user->save();
        }
        
        $userDevice = UserDevice::where('device_id', $device_id)->first();
        if (!$userDevice) {

            // Save Device Details
            $newUserDevice                      = new UserDevice();
            $newUserDevice->user_id             = $user_id;
            $newUserDevice->login_user_id       = $loginUserId;
            $newUserDevice->device_id           = $device_id;
            $newUserDevice->device_type         = $request->eDeviceType;
            $newUserDevice->brand               = $request->brand;
            $newUserDevice->model               = $request->model;
            $newUserDevice->device              = $request->device;
            $newUserDevice->manufacturer        = $request->manufacturer;
            $newUserDevice->os_version          = $request->osVersion;
            $newUserDevice->app_version_name    = $request->appVersionName;
            $newUserDevice->save();

            // Get last inserted ID
            $newUserDeviceId = $newUserDevice->id; 
        } else {
            $newUserDeviceId = $userDevice->id;
        }

        AppOpenLog::create([
            'user_id'               => $user->id,
            'user_device_id'        => $newUserDeviceId,
            'visit_time'        => now()->format('H:i:s'),
            'created_at'            => Carbon::now(),
        ]);

        $userCoupon = UserCoupon::where('user_id', $user_id)->where('estatus', 1)->whereDate('expiry_date', '>=', Carbon::now())->select('coupon_code')->first();

        $isCouponAvailable = $userCoupon ? true : false;
        $couponCode = $userCoupon->coupon_code ?? null;

        $temp['userId'] = $user->id;
        $temp['userType'] = (int) $user->eUserType;
        $temp['isCouponCodeAvailable'] = $isCouponAvailable;
        $temp['couponCode'] = $couponCode;
        $temp['email'] = $user->email ?? '';
        $temp['firstName'] = $user->first_name ?? '';
        $temp['lastName'] = $user->last_name ?? '';
        $temp['profileUrl'] = $user->profile_url ?? '';

        return $this->sendResponseWithData($temp,"splash Data Retrieved Successfully.");
    }

    public function getSeries(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'tournamentId' => 'required|integer',
            'eSeriesType' => 'nullable|integer',
            'limit' => 'nullable|integer'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $query = Series::query();

        if ($request->tournamentId != 0) {
            $query->where('tournament_id', $request->tournamentId);
        }

        if ($request->eSeriesType) {
            $query->where('series_type', $request->eSeriesType);
        }

        if ($request->limit) {
            $query->limit($request->limit);
        }

        $series = $query->get()->map(function ($s) {
            return [
                'seriesId' => $s->id,
                'seriesName' => $s->name, // Assuming 'name' column exists
                'startDate' => $s->start_date,
                'endDate' => $s->end_date,
                'tournamentName' => Tournament::where('id', $s->tournament_id)->value('name') ?? "N/A"
            ];
        });

        return $this->sendResponseWithData($series, "Series retrieved successfully");
    }

    public function newsList(Request $request)
    {
        $query = News::where('estatus', 1); // Fetch only active news

        // Apply limit filter
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $newsList = $query->get()->map(function ($news) {
            return [
                'newsId'          => $news->id,
                'newsTitle'       => $news->news_title ?? "",
                'newsImage'       => isset($news->thumb_img) ? url('images/news/' . $news->thumb_img) : "",
                'newsDescription' => $news->description ?? "",
                'newsLikes'       => $news->total_likes ?? 0,
                'newsShare'       => $news->total_share ?? 0,
            ];
        });

        return $this->sendResponseWithData($newsList, "News Retrieved Successfully.");
    }

    public function getSeriesTeam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seriesId' => 'required|integer',
            'eFormate' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $query = Series::where('id', $request->seriesId);

        if ($request->eFormate) {
            $query->where('eFormate', $request->eFormate);
        }

        $series = $query->with(['teams.players'])->first();

        if (!$series) {
            return $this->sendError([], "Series not found", []);
        }

        $response = [
            'seriesId' => $series->id,
            'eFormate' => matchType($series->series_type),
            'lstSeriesTeam' => $series->teams->map(function ($team) {
               
                return [
                    'teamId' => $team->team->id,
                    'teamName' => $team->team->name,
                    'teamShortName' => $team->team->short_name,
                    'teamLogo' => isset($team->team)?url('images/team/'.$team->team->thumb_img):"",
                    'lstTeamPlayer' => $team->players->map(function ($player) {
                   
                        return [
                            'playerId' => $player->player->id,
                            'playerName' => $player->player->name,
                            'playerImg' => isset($player->$player->thumb_img)?url('images/player/'.$player->$player->thumb_img):"",
                            'ePlayerType' => playerType($player->player->player_type),
                            'eBattingStyle' => battingStyle($player->player->batting_style),
                            'eBowlingStyle' => bowlingStyle($player->player->bowling_style),
                            'eBowlingArm' => bowlingArm($player->player->bowling_arm)
                        ];
                    }),
                ];
            }),
        ];

        return $this->sendResponseWithData($response, "Series team data retrieved successfully");
    }

    public function teamHeadToHead(Request $request){
      


        $matchQuery = Matche::where(function ($q) use ($request) {
            $q->where(['team1_id' => $request->team1_id, 'team2_id' => $request->team2_id])
              ->orWhere(['team1_id' => $request->team2_id, 'team2_id' => $request->team1_id]);
        });
        
        if ($request->filled('match_type')) {
            $matchQuery->where('match_type', $request->match_type);
        }
        if ($request->filled('stadium_id')) {
            $matchQuery->where('stadium_id', $request->stadium_id);
        }
        
        $totalmatches = (clone $matchQuery)->count();
        $team1_winner_matches = (clone $matchQuery)->where('win_team_id', $request->team1_id)->count();
        $team2_winner_matches = (clone $matchQuery)->where('win_team_id', $request->team2_id)->count();
        $noresultmatches = (clone $matchQuery)->where('win_team_id', 0)->count();

        $team1 = Team::find($request->team1_id);
        $team2 = Team::find($request->team2_id);

        $data['team1_name'] = $team1->name ?? '';
        $data['team1_shortname'] = $team1->short_name ?? '';
        $data['team1_logo'] = $team1->thumb_img ? url('images/team/'.$team1->thumb_img) : '';
        $data['team2_name'] = $team2->name ?? '';
        $data['team2_shortname'] = $team2->short_name ?? '';
        $data['team2_logo'] = $team2->thumb_img ? url('images/team/'.$team2->thumb_img) : '';
        $data['team1_win'] = $team1_winner_matches;
        $data['team2_win'] = $team2_winner_matches;

        $lst_team1_last_five_match = array();
        $lst_team2_last_five_match = array();
        $lst_top_batsman = array();
        $lst_top_bowler = array();
        $lst_top_allrounder = array();
        $lst_top_batsman_last_five_match = array();
        $lst_top_bowler_last_five_match = array();
        $lst_top_allrounder_last_five_match = array();

        $team1_last_5 = Matche::where(function($q) use($request) {
            $q->where('team1_id', $request->team1_id)
              ->orWhere('team2_id', $request->team1_id);
        })
        ->latest('start_date')
        ->take(5)
        ->get();
    
        $lst_team1_last_five_match = $team1_last_5->map(function ($match) use ($request) {
            return [
                'match_id'   => $match->id,
                'match_name' => $match->team1->short_name . ' vs ' . $match->team2->short_name,
                'is_win'     => $match->win_team_id == $request->team1_id ? 1 : 0,
            ];
        });
        
        $team2_last_5 = Matche::where(function($q) use($request) {
                $q->where('team1_id', $request->team2_id)
                ->orWhere('team2_id', $request->team2_id);
            })
            ->latest('start_date')
            ->take(5)
            ->get();
        
        $lst_team2_last_five_match = $team2_last_5->map(function ($match) use ($request) {
            return [
                'match_id'   => $match->id,
                'match_name' => $match->team1->short_name . ' vs ' . $match->team2->short_name,
                'is_win'     => $match->win_team_id == $request->team2_id ? 1 : 0,
            ];
        });

        $last_5_matches_between_teams = Matche::where(function ($q) use ($request) {
            $q->where('team1_id', $request->team1_id)
              ->where('team2_id', $request->team2_id)
            ->orWhere(function ($q) use ($request) {
                $q->where('team1_id', $request->team2_id)
                  ->where('team2_id', $request->team1_id);
            });
        })
        ->latest('start_date')
        ->take(5)
        ->pluck('id');

        $last_5_matches_team1_or_team2 = Matche::where(function ($q) use ($request) {
            $q->where('team1_id', $request->team1_id)
              ->orWhere('team2_id', $request->team1_id)
              ->orWhere('team1_id', $request->team2_id)
              ->orWhere('team2_id', $request->team2_id);
        })
        ->latest('start_date')
        ->take(5)
        ->pluck('id');

        $lst_top_batsman = DB::table('match_scoreboards as ms')
        ->join('players as p', 'ms.player_id', '=', 'p.id')
        ->whereIn('p.player_type', [1, 3])
        ->whereIn('ms.match_id', $last_5_matches_between_teams) // Filter for Team1 vs Team2 last 5 matches
        ->select(
            'ms.player_id as playerId',
            'p.name as playerName',
            'p.thumb_img as playerImg',
            DB::raw('SUM(ms.ball) as totalBall'),
            DB::raw('SUM(ms.run) as totalRun'),
            DB::raw('SUM(ms.four) as fours'),
            DB::raw('SUM(ms.six) as sixes'),
            DB::raw('AVG(ms.strike_rate) as strikeRate'),
            DB::raw('SUM(ms.fantasy_point) as fantasyPoint')
        )
        ->groupBy('ms.player_id', 'p.name', 'p.thumb_img')
        ->orderByDesc('totalRun')
        ->limit(5)
        ->get();

        $lst_top_batsman_last_five_match = DB::table('match_scoreboards as ms')
        ->join('players as p', 'ms.player_id', '=', 'p.id')
        ->whereIn('p.player_type', [1, 3])
        
        ->whereIn('ms.match_id', $last_5_matches_team1_or_team2) // Filter for either Team1 or Team2 last 5 matches
        ->select(
            'ms.player_id as playerId',
            'p.name as playerName',
            'p.thumb_img as playerImg',
            DB::raw('SUM(ms.ball) as totalBall'),
            DB::raw('SUM(ms.run) as totalRun'),
            DB::raw('SUM(ms.four) as fours'),
            DB::raw('SUM(ms.six) as sixes'),
            DB::raw('AVG(ms.strike_rate) as strikeRate'),
            DB::raw('SUM(ms.fantasy_point) as fantasyPoint')
        )
        ->groupBy('ms.player_id', 'p.name', 'p.thumb_img')
        ->orderByDesc('totalRun')
        ->limit(5)
        ->get();

        $lst_top_bowler = DB::table('match_scoreboards as ms')
        ->join('players as p', 'ms.player_id', '=', 'p.id')
        ->whereIn('p.player_type', [2])
        ->whereIn('ms.match_id', $last_5_matches_between_teams)
        ->select(
            'ms.player_id as playerId',
            'p.name as playerName',
            'p.thumb_img as playerImg',
            DB::raw('SUM(ms.over) as totalOver'),
            DB::raw('SUM(ms.ball_run) as totalRun'),
            DB::raw('SUM(ms.maiden) as maiden'),
            DB::raw('SUM(ms.wicket) as wickets'),
            DB::raw('AVG(ms.economy_rate) as economyRate'),
            DB::raw('SUM(ms.fantasy_point) as fantasyPoint')
        )
        ->groupBy('ms.player_id', 'p.name', 'p.thumb_img')
        ->orderByDesc('wickets')
        ->limit(5)
        ->get();

        $lst_top_bowler_last_five_match = DB::table('match_scoreboards as ms')
        ->join('players as p', 'ms.player_id', '=', 'p.id')
        ->whereIn('p.player_type', [2])
        ->whereIn('ms.match_id', $last_5_matches_team1_or_team2)
        ->select(
            'ms.player_id as playerId',
            'p.name as playerName',
            'p.thumb_img as playerImg',
            DB::raw('SUM(ms.over) as totalOver'),
            DB::raw('SUM(ms.ball_run) as totalRun'),
            DB::raw('SUM(ms.maiden) as maiden'),
            DB::raw('SUM(ms.wicket) as wickets'),
            DB::raw('AVG(ms.economy_rate) as economyRate'),
            DB::raw('SUM(ms.fantasy_point) as fantasyPoint')
        )
        ->groupBy('ms.player_id', 'p.name', 'p.thumb_img')
        ->orderByDesc('wickets')
        ->limit(5)
        ->get();

        $lst_top_allrounder = DB::table('match_scoreboards as ms')
        ->join('players as p', 'ms.player_id', '=', 'p.id')
        ->whereIn('p.player_type', [4]) // Allrounders
        ->whereIn('ms.match_id', $last_5_matches_between_teams)
        ->select(
            'ms.player_id as playerId',
            'p.name as playerName',
            'p.thumb_img as playerImg',
            DB::raw('SUM(ms.run) as totalRun'),
            DB::raw('SUM(ms.wicket) as wickets'),
            DB::raw('SUM(ms.fantasy_point) as fantasyPoint')
        )
        ->groupBy('ms.player_id', 'p.name', 'p.thumb_img')
        ->orderByDesc('fantasyPoint')
        ->limit(5)
        ->get();

   
    $lst_top_allrounder_last_five_match = DB::table('match_scoreboards as ms')
        ->join('players as p', 'ms.player_id', '=', 'p.id')
        ->whereIn('p.player_type', [4]) // Allrounders
        ->whereIn('ms.match_id', $last_5_matches_team1_or_team2)
        ->select(
            'ms.player_id as playerId',
            'p.name as playerName',
            'p.thumb_img as playerImg',
            DB::raw('SUM(ms.run) as totalRun'),
            DB::raw('SUM(ms.wicket) as wickets'),
            DB::raw('SUM(ms.fantasy_point) as fantasyPoint')
        )
        ->groupBy('ms.player_id', 'p.name', 'p.thumb_img')
        ->orderByDesc('fantasyPoint')
        ->limit(5)
        ->get();

        $data['lst_team1_last_five_match'] = $lst_team1_last_five_match;
        $data['lst_team2_last_five_match'] = $lst_team2_last_five_match;
        $data['lst_top_batsman'] = $lst_top_batsman;
        $data['lst_top_bowler'] = $lst_top_bowler;
        $data['lst_top_allrounder'] = $lst_top_allrounder;
        $data['lst_top_batsman_last_five_match'] = $lst_top_batsman_last_five_match;
        $data['lst_top_bowler_last_five_match'] = $lst_top_bowler_last_five_match;
        $data['lst_top_allrounder_last_five_match'] = $lst_top_allrounder_last_five_match;

        return $this->sendResponseWithData($data, "Tournament Team Retrieved Successfully.");
    }

    public function playerRecordOld(Request $request){

        // $matchcommentries = MatchCommentry::where(['batsman_id'=>$request->player1_id,'bowler_id'=>$request->player2_id]);
        // if(isset($request->match_type)){
        //     $matchcommentries = $matchcommentries->where('match_type',$request->match_type);
        // }
        // if(isset($request->stadium_id)){
        //     $matchcommentries = $matchcommentries->where('stadium_id',$request->stadium_id);
        // }
        // $matchcommentries = $matchcommentries->get();

        // $ballfaced = $matchcommentries->count();
        // $totalmatch = $matchcommentries->groupBy('match_id')->count();
        // $match_runs = $matchcommentries->sum('run');
        // $total4 = $matchcommentries->where('run',4)->count();
        // $total6 = $matchcommentries->where('run',6)->count();
        // $total1 = $matchcommentries->where('run',1)->count();
        // $total2 = $matchcommentries->where('run',2)->count();
        // $total3 = $matchcommentries->where('run',3)->count();
        // $duckout = $matchcommentries->where('is_out',1)->count();
        // $runavg = $matchcommentries->avg('run');

        // $data['total_match'] =  $totalmatch;
        // $data['match_runs'] =  (int) $match_runs;
        // $data['ballfaced'] =   $ballfaced;
        // $data['total4'] =  (int) $total4;
        // $data['total6'] =  (int) $total6;
        // $data['total1'] =  (int) $total1;
        // $data['total2'] =  (int) $total2;
        // $data['total3'] =  (int) $total3;
        // $data['duckout'] =  (int) $duckout;
        // $data['runavg'] =   number_format((float)$runavg, 2, '.', '');
        // $data['strikerate'] =   $this->strikerate($ballfaced,$match_runs);


        // $commentry_arr = array();
        // foreach ($matchcommentries as $ball){
        //     $temp = array();
        //     $temp['ball_number'] = $ball->ball_number;
        //     $temp['commentry'] = isset($ball->commentry)?$ball->commentry:"";
        //     array_push($commentry_arr,$temp);
        // }
        // $data['commentry'] = $commentry_arr;

        $data['playerId'] = 22;
        $data['player_name'] = "Rohit Sharma";
        $data['player_img'] = "https://theplayer.webvedanttechnology.com/images/team/thumb_img_9508371742641523.jpg";
        $data['obj_opp_team'] = array(
                                    "team_name"         => "Royal Challengers Banglore",
                                    "team_short_name"   => "RCB",
                                    "team_logo"         => "https://theplayer.webvedanttechnology.com/images/team/thumb_img_9508371742641523.jpg"
                                );
        $data['obj_opp_player'] = array(
                                    "player_id"     => 50,
                                    "player_name"   => "Virat Kohli",
                                    "player_img"    => "https://theplayer.webvedanttechnology.com/images/team/thumb_img_9508371742641523.jpg"
                                );

        $lst_player_record = array();
        $lst_player_last_five_record = array();

        for($b=0; $b<5; $b++){
 
            $lst_player_record[] = array(
                "total_ball"    => 15,
                "total_run"     => 20,
                "fours"         => 4,
                "sixes"         => 1,
                "strike_rate"   => 50.33,
                "total_over"    => 15,
                "total_run"     => 20,
                "maiden"        => 4,
                "wickets"       => 1,
                "economy_rate"  => 50.33,
                "fantasy_point" => 15
            );

            $lst_player_last_five_record[] = array(
                "total_ball"    => 15,
                "total_run"     => 20,
                "fours"         => 4,
                "sixes"         => 1,
                "strike_rate"   => 50.33,
                "total_over"    => 15,
                "total_run"     => 20,
                "maiden"        => 4,
                "wickets"       => 1,
                "economy_rate"  => 50.33,
                "fantasy_point" => 15
            );
        }

        $data['lst_player_record'] = $lst_player_record;
        $data['lst_player_last_five_record'] = $lst_player_last_five_record;

        return $this->sendResponseWithData($data, "Player Vs Player Retrieved Successfully.");
    }

    public function playerRecord(Request $request)
    {
        $player_id = $request->player_id;

        // Validate player_id is required
        if (!$player_id) {
            return $this->sendError("Player ID is required", 400);
        }

        // 🏏 Get Player Details
        $player = Player::find($player_id);
        if (!$player) {
            return $this->sendError("Player not found", 404);
        }

        // 🏏 Optional: Get Opponent Team Details
        $obj_opp_team = null;
        if ($request->has('opp_team_id')) {
            $opp_team = Team::find($request->opp_team_id);
            if ($opp_team) {
                $obj_opp_team = [
                    "team_name" => $opp_team->name ?? '',
                    "team_short_name" => $opp_team->short_name ?? '',
                    "team_logo" => $opp_team->thumb_img ? url('images/team/' . $opp_team->thumb_img) : '',
                ];
            }
        }

        // 🏏 Optional: Get Opponent Player Details
        $obj_opp_player = null;
        if ($request->has('opp_player_id')) {
            $opp_player = Player::find($request->opp_player_id);
            if ($opp_player) {
                $obj_opp_player = [
                    "player_id" => $opp_player->id ?? null,
                    "player_name" => $opp_player->name ?? '',
                    "player_img" => $opp_player->thumb_img ? url('images/team/' . $opp_player->thumb_img) : '',
                ];
            }
        }



        $query = MatchCommentry::where(function ($q) use ($player_id) {
            $q->where('batsman_id', $player_id)->orWhere('bowler_id', $player_id);
        });
        
        // 🏏 If Opponent Player is provided, check for both batsman and bowler
        if ($request->has('opp_player_id') && $request->opp_player_id != null) {
            $opp_player_id = $request->opp_player_id;
            $query->where(function ($q) use ($opp_player_id) {
                $q->where('batsman_id', $opp_player_id)->orWhere('bowler_id', $opp_player_id);
            });
        }
        
        // 🏏 Step 1: Get last 5 distinct match IDs
        $last_five_match_ids = $query->select('match_id')
            ->distinct()
            ->latest('match_id')
            ->limit(5)
            ->pluck('match_id');  // Returns an array of match IDs
       
        // 🏏 Step 2: Fetch all balls from these matches
        $last_five_matches = MatchCommentry::whereIn('match_id', $last_five_match_ids)
            ->where(function ($q) use ($player_id) {
                $q->where('batsman_id', $player_id)->orWhere('bowler_id', $player_id);
            })
            ->orderBy('match_id', 'desc')
            ->get();

        
     

        // 🏏 Calculate Stats
        $total_matches = $last_five_matches->groupBy('match_id')->count();
        $total_runs = $last_five_matches->sum('run');
        $balls_faced = $last_five_matches->count();
        $fours = $last_five_matches->where('run', 4)->count();
        $sixes = $last_five_matches->where('run', 6)->count();
        $strike_rate = $balls_faced > 0 ? number_format(($total_runs / $balls_faced) * 100, 2) : 0;
        $wickets = $last_five_matches->where('is_out', 1)->count();

        // 🏏 Player Record Object
        $lst_player_record = [
            "total_ball" => $balls_faced,
            "total_run" => $total_runs,
            "fours" => $fours,
            "sixes" => $sixes,
            "strike_rate" => $strike_rate,
            "total_over" => round($balls_faced / 6, 1),
            "maiden" => 0, // Need to fetch maiden overs separately
            "wickets" => $wickets,
            "economy_rate" => 0, // Economy rate calculation can be added later
            "fantasy_point" => ($total_runs + ($wickets * 25)), // Example formula for fantasy points
        ];

        // 🏏 Last Five Matches Record
        $lst_player_last_five_record = $last_five_matches->map(function ($match) {
            return [
                "total_ball" => 1,
                "total_run" => $match->run,
                "fours" => $match->run == 4 ? 1 : 0,
                "sixes" => $match->run == 6 ? 1 : 0,
                "strike_rate" => 100, // Can be improved
                "total_over" => 1,
                "maiden" => 0,
                "wickets" => $match->is_out ? 1 : 0,
                "economy_rate" => 0,
                "fantasy_point" => ($match->run + ($match->is_out ? 25 : 0)),
            ];
        });

        // 🏏 Final Response Data
        $data = [
            "player_id" => $player->id,
            "player_name" => $player->name,
            "player_img" => url('images/team/' . $player->thumb_img),
            "obj_opp_team" => $obj_opp_team,
            "obj_opp_player" => $obj_opp_player,
            "lst_player_record" => [$lst_player_record],
            "lst_player_last_five_record" => $lst_player_last_five_record,
        ];

        return $this->sendResponseWithData($data, "Player Record Retrieved Successfully.");
    }

}
