<?php


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ProjectPage;
use App\Models\UserPermission;


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
    }else{
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
    if($bowlingArm == 1){
        $Type = "Left Arm";
    }
    elseif($bowlingArm == 2){
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





