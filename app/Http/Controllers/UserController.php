<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class UserController extends Controller
{
/********************registor new user**********************/
    public function register(Request $request){      
        try {
            //vallidation
        $val=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required |email | unique:users,email',
            'phone'=>'required | numeric | digits:10 | unique:users,phone',
            'password'=>'required|confirmed',
         ]);

           if($val->fails()){
            return response([
                'status'=>'failed',
                'message' => 'Validation error',
                'error'=>$val->errors()

            ], 401);
        }
        //create user fields in database using model
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password),
       
        ]);

        // create token
        $token = $user->createToken($request->email)->plainTextToken;
        return response([
            'status'=>'success',
            'message' => 'Registration Success',
            'token'=>$token
        ], 201);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        
    }
   
/***************************login********************************/

    public function login(Request $request){
       
      try {

        //validation if email
        if($request->email){
            $request->validate([
                'email'=>'required|email',
                'password'=>'required',
            ]);}
            
        else{

        //validation if phone
            $request->validate([
                'phone'=>'required | numeric | digits:10 ',
                'password'=>'required',
             ]);}
    
            $user = User::where('email', $request->email)
            ->orWhere('phone', $request->phone)->first();
           
            if($user && Hash::check($request->password, $user->password)){
                $token = $user->createToken($user->email)->plainTextToken;
               
                return response([
                    'status'=>'success',
                    'token'=>$token,
                    'message' => 'Login Success'
                ], 200);
            }
            return response([
                'message' => 'The Provided Credentials are incorrect',
                'status'=>'failed'
            ], 401);
         
        }  catch (\Throwable $th) {
          return response()->json([
              'status' => false,
              'message' => $th->getMessage()
          ], 500);
         }
        }     
    
/***************************logout********************************/

    public function logout(){

        try {
            auth()->user()->tokens()->delete();
            return response([
                'message' => 'Logout Success',
                'status'=>'success'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
           }
        
    }

 /********************logged user data show************************/   
    public function loggedUserData(){
        try {
            $userName = auth()->user()->name;
            $userEmail = auth()->user()->email;
            $userPhone = auth()->user()->phone;
            return response([
                'status'=>'success',
                'message' => 'Logged User Data',
                'user data'=>[
                    'name' =>$userName,
                    'email' =>$userEmail,
                    'phone '=>$userPhone,
                ]
             
            ], 200);
           
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
           }
    }


 /********************change password************************/  
    public function changePassword(Request $request){
        try {
            $request->validate([
                'password' => 'required|confirmed',
            ]);
            $loggeduser = auth()->user();
            $loggeduser->password = Hash::make($request->password);
            $loggeduser->save();
            return response([
                'message' => 'Password Changed Successfully',
                'status'=>'success'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
           
        }
        
    }
}
