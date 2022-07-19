<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\Models\Lead;
use App\Models\User;
use App\Models\ResetCodePassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Str;
use DB;




class ForgetPasswordController extends Controller
{
   public function forget_password(Request $request){
    try{
        $user= User::where('email',$request->email)->get();
         if(count($user)>0){
         $token=Str::random(40);
        
        // $domain=URL::to('https://stgn.appsndevs.com/metaland');
         $url='https://stgn.appsndevs.com/metaland/resetPassword?token='.$token;
         
         $name=$user[0]['name'];
         $user['to']=$request->email;
        //  $data['url']=$url;
        //  $data['email']=$request->email;
        //  $data['title']="Password reset";
        //  $data['body']="Please click on below link";
         $data=['url'=> $url, 'name'=>$name,'email'=>$request->email,'title'=>"Reset password",'subject'=>"Regarding lead assigned"];

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
        //dd($request->all());
        $reset = DB::table('reset_code_passwords')->select('email')->where('token', $request->token)->first();


        //$reset=ResetCodePassword::find($request->token);
        //dd($reset);
       //$reset=$reset->toArray();
        // dd($reset->email);
        //dd($reset[0]['email']);
        if(isset($request->token) && $reset!=""){
            $user= User::where('email',$reset->email)->first();
           // dd( $user);
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
            'password'=>'required|string'
        ]);
        if($validator->fails()){ 
            return response()->json(['code'=>'302','error'=>$validator->errors()]);            
        }
        $user= User::find($request->id);
        if($request->password!=$request->cm_password){
            return response()->json(['status'=>'error', 'code'=>'302','msg'=> "confirmation password not match"]);
 
        }
        $user->password=Hash::make($request->password);
        $user->save();
        ResetCodePassword::where('email',$user->email)->delete();
        return response()->json(['status'=>'success', 'code'=>'200','msg'=> "password updated"]);
    }
    catch(Exception $e){
        return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);

    }
   }
}
