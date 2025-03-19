<?php


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ProjectPage;
use App\Models\UserPermission;
use App\Models\Player;
use App\Models\MatchCommentry;
use App\Models\MatchScoreboard;

function getLeftMenuPages(){
    $pages = ProjectPage::where('parent_menu',0)->orderBy('sr_no','ASC')->get()->toArray();
    return $pages;
}

function getUSerRole(){
    return  \Illuminate\Support\Facades\Auth::user()->role;
}

function is_write($page_id){
    $is_write = UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->where('project_page_id',$page_id)->where('can_write',1)->first();
    if ($is_write){
        return true;
    }
    return false;
}

function is_delete($page_id){
    $is_delete = UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->where('project_page_id',$page_id)->where('can_delete',1)->first();
    if ($is_delete){
        return true;
    }
    return false;
}

function is_read($page_id){
    $is_read = UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->where('project_page_id',$page_id)->where('can_read',1)->first();
    if ($is_read){
        return true;
    }
    return false;
}

function UploadImage($image, $path){
    $imageName = Str::random().'.'.$image->getClientOriginalExtension();
    $path = $image->move(public_path($path), $imageName);
    if($path == true){
        return $imageName;
    }else{
        return null;
    }
}


function outType($outType){
    if($outType == "lbw"){
        $Type = 1;
    }
    elseif($outType == "hit_wkt"){
        $Type = 2;
    }
    elseif($outType == "caught_bowled"){
        $Type = 3;
    }
    elseif($outType == "caught"){
        $Type = 4;
    }
    elseif($outType == "bowled"){
        $Type = 5;
    }
    elseif($outType == "stumped"){
        $Type = 6;
    }
    elseif($outType == "run_out"){
        $Type = 7;
    } else {
        $Type = 51;
    }
   
    return $Type;
}

function outTypeNo($outType){
    if($outType == 1){
        $Type = "lbw";
    }
    elseif($outType == 2){
        $Type = "hit_wkt";
    }
    elseif($outType == 3){
        $Type = "caught_bowled";
    }
    elseif($outType == 4){
        $Type = "caught";
    }
    elseif($outType == 5){
        $Type = "bowled";
    }
    elseif($outType == 6){
        $Type = "stumped";
    }
    elseif($outType == 7){
        $Type = "run_out";
    }else{
        $Type = "-";
    }
   
    return $Type;
}

function bollType($bollType){
    if($bollType == "noBall"){
        $Type = 1;
    }
    elseif($bollType == "regular"){
        $Type = 2;
    }
    elseif($bollType == "wide"){
        $Type = 3;
    }else{
        $Type = 51;
    }
   
    return $Type;
}

function bollTypeNo($bollType){
    if($bollType == 1){
        $Type = "noBall";
    }
    elseif($bollType == 2){
        $Type = "regular";
    }
    elseif($bollType == 3){
        $Type = "wide";
    }else{
        $Type = "Other";
    }
   
    return $Type;
}

function matchType($matchType){
    if($matchType == 1){
        $Type = "T20";
    }
    elseif($matchType == 2){
        $Type = "ODI";
    }
    elseif($matchType == 3){
        $Type = "Both";
    }else{
        $Type = "";
    }
   
    return $Type;
}

function playerType($playerType){
    if($playerType == 1){
        $Type = "Batsman";
    }
    elseif($playerType == 2){
        $Type = "Bowler";
    }elseif($playerType == 3){
        $Type = "WkBatsman";
    }elseif($playerType == 4){
        $Type = "Allrounder";
    }
   
    return $Type;
}

function battingStyle($battingStyle){
    if($battingStyle == 1){
        $Type = "Right Hand";
    }
    elseif($battingStyle == 2){
        $Type = "Left Hand";
    }
   
    return $Type;
}

function bowlingStyle($bowlingStyle){
    $Type = "None";
    if($bowlingStyle == 1){
        $Type = "Fast";
    }
    elseif($bowlingStyle == 2){
        $Type = "Spinner";
    }elseif($bowlingStyle == 3){
        $Type = "Medium";
    }
    return $Type;
}

function bowlingArm($bowlingArm){
    $Type = "None";
    if($bowlingArm == 1){
        $Type = "Left Arm";
    } elseif($bowlingArm == 2){
        $Type = "Right Arm";
    }elseif($bowlingArm == 3){
        $Type = "Both";
    }
    return $Type;
}

function send_sms($mobile_no, $otp){
    $url = 'https://www.smsgatewayhub.com/api/mt/SendSMS?APIKey=H26o0GZiiEaUyyy0kvOV5g&senderid=MADMRT&channel=2&DCS=0&flashsms=0&number=91'.$mobile_no.'&text=Welcome%20to%20Madness%20Mart,%20Your%20One%20time%20verification%20code%20is%20'.$otp.'.%20Regards%20-%20MADNESS%20MART&route=31&EntityId=1301164983812180724&dlttemplateid=1307165088121527950';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
//    echo $response;
}

function calculateFantasyPoint($match_id){

    $MatchCommentryData = MatchCommentry::where('match_id', $match_id)->get();
    $MatchScoreboardData = MatchScoreboard::where('match_id',$match_id)->get();
    if(!empty($MatchCommentryData) && !empty($MatchScoreboardData)) {
        foreach ($MatchCommentryData as $MatchCommentry) {

            $comment_id = $MatchCommentry->id;
            $batsman_id = $MatchCommentry->batsman_id;
            $bowler_id = $MatchCommentry->bowler_id;
            $run = $MatchCommentry->run;
            $ball_type = $MatchCommentry->ball_type;
            $is_boundary = $MatchCommentry->is_boundary;
            $is_out = $MatchCommentry->is_out;
            $out_type = $MatchCommentry->out_type;
            $out_by_fielder1_id = $MatchCommentry->out_by_fielder1_id;
            $out_by_fielder2_id = $MatchCommentry->out_by_fielder2_id;
            $run_out_batsman_id = $MatchCommentry->run_out_batsman_id;
            $is_extra_run = $MatchCommentry->is_extra_run;
            $out_by_fielder2_id = $MatchCommentry->out_by_fielder2_id;

            $newbatpoints = 0;
            $newbowlpoints = 0;
            $newfield1points = 0;
            $newfield2points = 0;
            
            $CommentRecord = MatchCommentry::where('id', $comment_id)->first();

            // Point for Run
            if($run > 0){
                $newbatpoints += $run;

                // Point for Boundary
                if($is_boundary == 1){
                    if($run >= 6){
                        $newbatpoints += 6; // for Six
                    } else {
                        $newbatpoints += 4; // for Four
                    }
                }
                
            } else {

                // Point for Dot ball
                $newbowlpoints += 1;
                
                // Point for Wicket
                if($is_out == 1){
                    
                    if($out_type != 3){

                        // Point for Wicket Excluding Runout
                        $newbowlpoints += 25;
                    }

                    if($out_type == 2){

                        // Point for Caught
                        $newfield1points += 8;

                    } else if($out_type == 3){

                        // Point for Runout
                        $newfield1points += 6;
                        $newfield2points += 6;

                    } else if($out_type == 4 || $out_type == 7){

                        // Point for Stumped or Direct Hit Wicket(Runout)
                        $newfield1points += 12;

                    } else {
                        // Point for Bowled/LBW/Bowled(Caught by Bowler)
                        $newbowlpoints += 8;
                    }                        
                }
            }
            $CommentRecord->fantasy_point_bat = $newbatpoints;
            $CommentRecord->fantasy_point_bowl = $newbowlpoints;
            $CommentRecord->fantasy_point_field1 = $newfield1points;
            $CommentRecord->fantasy_point_field2 = $newfield2points;
            $CommentRecord->save();

        }

        foreach ($MatchScoreboardData as $MatchScore) {

            $scoreboardId = $MatchScore->id;
            $playedBall = $MatchScore->ball;
            $playerId = $MatchScore->player_id;
            $totalRun = $MatchScore->run;
            $totalFour = $MatchScore->four;
            $totalSix = $MatchScore->six;
            $totalWicket = $MatchScore->wicket;
            $totalMaiden = $MatchScore->maiden;
            $totalOver = $MatchScore->over;
            $totalEconomyRate = $MatchScore->economy_rate;
            $totalStrikeRate = $MatchScore->strike_rate;

            $batting_fantasy_point = MatchCommentry::where('match_id', $match_id)->where('batsman_id', $playerId)->sum('fantasy_point_bat');
            $bowling_fantasy_point = MatchCommentry::where('match_id', $match_id)->where('bowler_id', $playerId)->sum('fantasy_point_bowl');
            $fielder1_fantasy_point = MatchCommentry::where('match_id', $match_id)->where('out_by_fielder1_id', $playerId)->sum('fantasy_point_field1');
            $fielder2_fantasy_point = MatchCommentry::where('match_id', $match_id)->where('out_by_fielder2_id', $playerId)->sum('fantasy_point_bat');

            $total_fantasy_point = $batting_fantasy_point + $bowling_fantasy_point + $fielder1_fantasy_point + $fielder2_fantasy_point;

            if($totalRun >= 25 && $totalRun < 50){
                // Bonus Point for 25 Run
                $total_fantasy_point += 4;

            } else if($totalRun >= 50 && $totalRun < 75){
                // Bonus Point for 50 Run
                $total_fantasy_point += 8;

            } else if($totalRun >= 75 && $totalRun < 100){
                // Bonus Point for 75 Run
                $total_fantasy_point += 12;
                
            } else if($totalRun >= 100){
                // Bonus Point for Century
                $total_fantasy_point += 16;
            }

            if($totalWicket == 3){
                // Bonus Point for 3 Wicket
                $total_fantasy_point += 4;

            } else if($totalWicket == 4){
                // Bonus Point for 4 Wicket
                $total_fantasy_point += 8;

            } else if($totalWicket >= 5){
                // Bonus Point for 5 Wicket
                $total_fantasy_point += 12;
            }

            if($totalMaiden > 0){
                // Bonus Point for Maiden Over
                $total_fantasy_point += ($totalMaiden * 12); 
            }

            $total_catch_by_player = MatchCommentry::where('out_type', 2)->where('out_by_fielder1_id', $playerId)->count();
            if($total_catch_by_player >= 3){
                // Bonus Point for 3 Catches
                $total_fantasy_point += 12;
            }

            // Economy Rate Points (Min 2 Overs To Be Bowled)
            if($totalOver >= 2){

                if($totalEconomyRate < 5){
                    // Bonus Point for Below 5 runs per over
                    $total_fantasy_point += 6;

                } else if($totalEconomyRate >= 5 && $totalEconomyRate < 5.99){
                    // Bonus Point for Between 5-5.99 runs per over
                    $total_fantasy_point += 4;

                } else if($totalEconomyRate >= 6 && $totalEconomyRate <= 7){
                    // Bonus Point for Between 5-5.99 runs per over
                    $total_fantasy_point += 2;

                } else if($totalEconomyRate >= 10 && $totalEconomyRate <= 11){
                    // Bonus Point for Between 10-11 runs per over
                    $total_fantasy_point -= 2;

                } else if($totalEconomyRate >= 11.01 && $totalEconomyRate <= 12){
                    // Bonus Point for Between 10-11 runs per over
                    $total_fantasy_point -= 4;

                } else if($totalEconomyRate >= 12){
                    // Bonus Point for Above 12 runs per over
                    $total_fantasy_point -= 6;
                }
            }

            $checkPlayerType = Player::where('id', $playerId)->first();
            if ($checkPlayerType) {
                $playerType = $checkPlayerType->player_type;
            }

            // Strike Rate (Except Bowler) Points (Min 10 Balls To Be Played)
            if($playedBall >= 10 && $playerType != 2){

                if($playedBall == 100 && $totalStrikeRate >= 170){
                    // Bonus Point for Below 5 runs per over
                    $total_fantasy_point += 6;

                } else if($playedBall == 100 && $totalStrikeRate >= 150.01 && $totalStrikeRate <= 170){
                    // Bonus Point for Below 5 runs per over
                    $total_fantasy_point += 4;

                } else if($playedBall == 100 && $totalStrikeRate >= 130 && $totalStrikeRate <= 150){
                    // Bonus Point for Below 5 runs per over
                    $total_fantasy_point += 2;

                } else if($playedBall == 100 && $totalStrikeRate >= 60 && $totalStrikeRate <= 70){
                    // Bonus Point for Below 5 runs per over
                    $total_fantasy_point -= 2;
                    
                } else if($playedBall == 100 && $totalStrikeRate >= 50 && $totalStrikeRate <= 59.99){
                    // Bonus Point for Below 5 runs per over
                    $total_fantasy_point -= 4;

                }  else if($playedBall == 100 && $totalStrikeRate <= 50){
                    // Bonus Point for Below 5 runs per over
                    $total_fantasy_point -= 6;
                }
            }

            $updateScoreBoard = MatchScoreboard::where('id', $scoreboardId)->first();
            $updateScoreBoard->fantasy_point = $total_fantasy_point;
            $updateScoreBoard->save();

        }
        return 1; // OK Response

    } else {
        return 2; // Data not found
    }
}



