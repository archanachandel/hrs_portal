<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\Models\Lead;
use App\Models\User;
use App\Models\ResetCodePassword;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Str;




class ForgetPasswordController extends Controller
{
   public function forget_password(Request $request){
    try{
        $user= User::where('email',$request->email)->get();
         if(count($user)>0){
         $token=Str::random(40);
        
        // $domain=URL::to('https://stgn.appsndevs.com/metaland');
         $url='https://stgn.appsndevs.com/metaland/resetPassword?token='.$token;
         
         $user['to']=$request->email;
        //  $data['url']=$url;
        //  $data['email']=$request->email;
        //  $data['title']="Password reset";
        //  $data['body']="Please click on below link";
         $data=['url'=> $url,'email'=>$request->email,'title'=>"Password reset",'subject'=>"Regarding lead assigned"];

         Mail::send('forgetPasword', $data, function($message) use ($user)
         {
            $message->to($user['to'])->subject('Regarding password reset');
        });
         $datetime=Carbon::now()->format('Y-m-d H:i:s');
         ResetCodePassword::updateOrCreate(
            ['email'=>$request->email],
            ['email'=>$request->email,
            'token'=>$token,
            'created_at'=>$datetime]

         );
         return response()->json(['status'=>'success', 'code'=>'200','msg'=>'Plaese check your mail to  reset your password']);
         }
         else{
            return response()->json(['status'=>'error', 'code'=>'400','data'=>'user not found']);
         }

    }
    catch(Exception $e){
        return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);

    }
   }
   //reset password view load
   public function reset_password(Request $request){
    try{
        $reset=ResetCodePassword::where('token',$request->token)->get();
        if(isset($request->token) && count($reset)>0){
            $user= User::where('email',$reset[0]['email'])->get();
            return response()->json(['status'=>'success', 'code'=>'200','msg'=> $user]);
        }
        else{
            return response()->json(['status'=>'error','code'=>'404','message'=>"token not found"]);
        }
    }
    catch(Exception $e){
        return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);

    }
   }
   public function updateNewPassword(Request $request){
    try{
        $validator = Validator::make($request->all(),[ 
            'password'=>'required|string|min:6|confirmed'
        ]);
        if($validator->fails()){ 
            return response()->json(['code'=>'302','error'=>$validator->errors()]);            
        }
       
        $user= User::find($request->id);
        $user->password=Hash::make($request->password);
        $user->save();
        return response()->json(['status'=>'success', 'code'=>'200','msg'=> "password updated"]);


    }
    catch(Exception $e){
        return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);

    }

   }
}
