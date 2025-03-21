<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\CustomerDeviceToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $mobile_no = $request->mobile_no;
        $user = User::where('mobile_no',$mobile_no)->where('role',3)->first();
        if ($user){
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }
            $data['otp'] =  mt_rand(100000,999999);
            $user->otp = $data['otp'];
            $user->otp_created_at = Carbon::now();
            $user->save();
            if($user->first_name == ""){
                $data['user_status'] = 'new_user';
            }else{
                $data['user_status'] = 'exist_user';
            }
            $final_data = array();
            array_push($final_data,$data);

            //send_sms($mobile_no, $data['otp']);
            return $this->sendResponseWithData($final_data, 'User login successfully.');
        }else{
            $data['otp'] =  mt_rand(100000,999999);

            $user = new User();
            $user->mobile_no = $mobile_no;

            $user->role = 3;
            $user->otp = $data['otp'];
            $user->otp_created_at = Carbon::now();
            $user->save();
            $data['user_status'] = 'new_user';
            $final_data = array();
            array_push($final_data,$data);

            //send_sms($mobile_no, $data['otp']);
            return $this->sendResponseWithData($final_data, 'User registered successfully.');
        }
    }

    public function verify_otp(Request $request){

        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required',
            'otp' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('mobile_no',$request->mobile_no)->where('otp',$request->otp)->where('estatus',1)->first();

        if ($user && isset($user['otp_created_at']))
        {
            $user->otp = null;
            $user->otp_created_at = null;
            //$user->is_verify = 1;
            $user->save();
            $user['token'] =  $user->createToken('P00j@13579WebV#d@n%p')->accessToken;
            //$data =  new UserResource($user);
            // $final_data = array();
            // array_push($final_data,$data);
            $this->user_login_log($user->id);
            return $this->sendResponseWithData($user,'OTP verified successfully.');
        }
        else{
            return $this->sendError('OTP verification Failed.', "verification Failed", []);
        }
    }

    public function update_token(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'token' => 'required',
            'device_type' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $device = CustomerDeviceToken::where('user_id',$request->user_id)->first();
        if ($device){
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        else{
            $device = new CustomerDeviceToken();
            $device->user_id = $request->user_id;
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        $device->save();

        return $this->sendResponseWithData($user,"Device Token updated.");
    }

    public function user_login_log(Request $request, $id){

        $user = User::where('id', $id)->where('estatus', 1)->first();
        if ($user)
        {
            // $user->last_login_date = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            // $user->save();

            $userlogin = New UserLogin();
            $userlogin->user_id =  $user->id;
            $userlogin->country =  isset($request->countryName) ? $request->countryName : "";
            $userlogin->state =  isset($request->regionName) ? $request->regionName : "";
            $userlogin->city =  isset($request->cityName) ? $request->cityName : "";
            $userlogin->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $userlogin->save();
            return $this->sendResponseSuccess('log create successfully.');
        }
        else{
            return $this->sendError('User Not Found.', "verification Failed", []);
        }
    }

    public function loginWithGmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'userId' => 'required|string',
            'email' => 'required|email',
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'profileUrl' => 'nullable|string',
            'providerId' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError([], "Validation Error", $validator->errors());
        }

        // Find or create user
        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'user_id' => $request->userId,
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'profile_url' => $request->profileUrl,
                'provider_id' => $request->providerId,
            ]
        );


        // Generate token
        $token = $user->createToken('P00j@13579WebV#d@n%p')->accessToken;

         // Check if user has an active coupon
         $userCoupon = DB::table('user_coupon')
         ->where('user_id', $user->id)
         ->where('estatus', 1) // Active status (1 = Active)
         ->where('expiry_date', '>=', now()) // Ensure coupon is not expired
         ->first();

        // Determine coupon availability
        $isCouponAvailable = $userCoupon ? true : false;
        $couponCode = $userCoupon->coupon_code ?? null;

        $temp['userId'] = $user->id;
        $temp['userType'] = $user->eUserType;
        $temp['isCouponCodeAvailable'] = $isCouponAvailable;
        $temp['couponCode'] = $couponCode;

        return $this->sendResponseWithData(['token' => $token, 'user' => $temp], "Login successful");
    }

    /**
     * Login with Email & Password
     */
    public function loginWithEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'firstName' => 'required|string',
            'lastName' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError([], "Validation Error", $validator->errors());
        }

        // Find user
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError([], "Invalid credentials");
        }

         // Generate token
         $token = $user->createToken('P00j@13579WebV#d@n%p')->accessToken;

         // Check if user has an active coupon
         $userCoupon = DB::table('user_coupon')
         ->where('user_id', $user->id)
         ->where('estatus', 1) // Active status (1 = Active)
         ->where('expiry_date', '>=', now()) // Ensure coupon is not expired
         ->first();

        // Determine coupon availability
        $isCouponAvailable = $userCoupon ? true : false;
        $couponCode = $userCoupon->coupon_code ?? null;

        $temp['userId'] = $user->id;
        $temp['userType'] = $user->eUserType;
        $temp['isCouponCodeAvailable'] = $isCouponAvailable;
        $temp['couponCode'] = $couponCode;

        return $this->sendResponseWithData(['token' => $token, 'user' => $temp], "Login successful");
    }

}
