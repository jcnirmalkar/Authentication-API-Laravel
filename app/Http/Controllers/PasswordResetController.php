<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Carbon\Carbon;
class PasswordResetController extends Controller
{

/***********token generating and sending mail for reset password************/  
    public function sendResetPasswordLink(Request $request){
        try {
             //validation
        $request->validate([
            'email' => 'required|email',
        ]);
       
          
          $reqEmail = $request->email;

        // match user details
        $user = User::where('email', $reqEmail)->first();
    
        if(!$user){
            return response([
                'status'=>'failed',
                'message'=>'Email doesnt exists'
            ], 404);
        }

        // Generate Token
        $token = Str::random(64);

        // save data to data base for password reset
        PasswordReset::create([
            'email'=>$reqEmail,
            'token'=>$token,
            'created_at'=>Carbon::now()
        ]);
        
        // sending email 
        $userName = $user->name;
        Mail::send('mail.resetPassword', ['token'=>$token,'userName'=>$userName], function(Message $message)use($reqEmail){
           
            $message->subject('Reset Your Password');
            $message->to($reqEmail);
        });
        return response([
            'status'=>'success',
            'message'=>'Check Your Email...... Reset your Password',
        ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
       
    }
/**********************reset password ***************************************/
    public function resetPassword(Request $request, $token){
       try {
        // Delete Token older than 2 minute
        $timer = Carbon::now()->subMinutes(3)->toDateTimeString();
        PasswordReset::where('created_at', '<=', $timer)->delete();

        $request->validate([
            'password' => 'required|confirmed',
        ]);

        $passwordreset = PasswordReset::where('token', $token)->first();

        if(!$passwordreset){
            return response([
                'message'=>'Token is Invalid or Expired',
                'status'=>'failed'
            ], 404);
        }

        $user = User::where('email', $passwordreset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token after resetting password
        PasswordReset::where('email', $user->email)->delete();

        return response([
            'message'=>'Password Reset Success',
            'status'=>'success'
        ], 200);
            
       } catch (\Throwable $th) {
        
       }
        
    }
}
