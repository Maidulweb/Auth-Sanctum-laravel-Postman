<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetLinkEmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ResetPasswordLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class PasswordResetController extends Controller
{
    public function __construct() {
        $this->middleware('guest');
    }

    public function sendResetLinkEmail(ResetLinkEmailRequest $request){

       $url = URL::temporarySignedRoute('password.reset',now()->addMinute(10),['email'=>$request->email]);

       $url = str_replace(env('APP_URL'), env('FRONTEND_URL'),$url);
       
       Mail::to($request->email)->send(new ResetPasswordLink($url));

       return response()->json([
        'message'=> 'Reset password sent your mail'
       ]);
    }

    public function reset(ResetPasswordRequest $request){
        $user = User::whereEmail($request->email)->first();

        if(!$user){
            return response()->json([
              'message'=>'User not found'
            ],404);
        }

        $user->password = bcrypt($request->password);

        $user->save();

        return response()->json([
            'message'=> 'Password reset successfully'
        ],200);
    }
}
