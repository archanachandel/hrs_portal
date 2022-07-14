<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
//use App\Models\UserChannel;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\Lead;
use Carbon\Carbon;
class UserController extends Controller
{
    function login(Request $request)
    {   
        $validator = Validator::make($request->all(),[ 
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()) { 
            return response()->json(['code'=>'302','error'=>$validator->errors()]);            
            }
        $user= User::where('email', $request->email)->first();
        $role_id= $user->role_id ;
        if(!$user || !Hash::check($request->password, $user->password)) {
        return response(['message' => ['Invalid Ceredentials.']], 404); 
        }
    
        $user1= User::where('email', $request->email)->first();
        $token = $user->createToken('my-app-token')->plainTextToken;
        $user1->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
        ]);
        return response()->json(['status'=>'Success','code'=>200, 'message' => "Successfully login", 'Token'=>$token,'role_id'=>$role_id]);
    }

    public function register(Request $request)
    {
        try{
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();         
            if($usercheck == ""){
                return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']); 
            } 
            $validator = Validator::make($request->all(),[ 
                'name' => 'required|string ',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'role_id' => 'required|integer',
                'channel_id' =>'required'
            ]);
            if($validator->fails()){ 
                return response()->json(['code'=>'302','error'=>$validator->errors()]);            
                }
            $userid = $user->id;
            $username = $user->name;
            $data['name'] = $request->name;    
            $data['email'] = $request->email;       
            $data['password'] =  Hash::make($request->password);
            $data['username'] = $request->username;    
            $data['phone_number'] = $request->phone_number;    
            $data['role_id'] = $request->role_id; 
            $data['channel_id']=$request->channel_id; 
            $data['created_by'] = $username;
            //dd($data->channel_id);
            $user = User::create($data);
            $channel_id=str_replace('[','',$user->channel_id);
            $channel_id=str_replace(']','',$channel_id);
            $channel_id=str_replace(' ','',$channel_id);
            $channel_id=explode(',',$channel_id);
            $user->channel_id=$channel_id;
            $token = $user->createToken('my-app-token')->plainTextToken;
            $response = [
            'token' => $token
            ];
            return response()->json(['status'=>'Success','code'=>200, 'Data'=> $user, 'Token'=>$response]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
    public function loginUserDetail(){
        try {
           // dd('here');
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
                return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            } 
            $userid=$user->id;
            $userdetail=User::find($userid);
            $channel_id=str_replace('[','',$userdetail->channel_id);
            $channel_id=str_replace(']','',$channel_id);
            $channel_id=str_replace(' ','',$channel_id);
            $channel_id=explode(',',$channel_id);
            $userdetail->channel_id=$channel_id;
            $lead_array = [];
            $totallead=Lead::select('*')->where('assignee','=',$userid)->get();
            foreach( $totallead as $key=>$lead){
                $lead_array[] = $lead->id;
                $userdetail->leads=$lead_array;
            }
            $userdetail->total_leads= $totallead->count();
            
            return response()->json(['status'=>'success', 'code'=>'200','data'=> $userdetail]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
    public function UserDetailById($id){
        try {
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
                return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            } 
            //$userid=$user->id;
            $userdetail=User::find($id);
            $channel_id=str_replace('[','',$userdetail->channel_id);
            $channel_id=str_replace(']','',$channel_id);
            $channel_id=str_replace(' ','',$channel_id);
            $channel_id=explode(',',$channel_id);
            $userdetail->channel_id=$channel_id;
            $userid=$userdetail->id;
            //$complete2=[];
            $lead_array = [];
            $totallead=Lead::select('*')->where('assignee','=',$userid)->get();
            foreach($totallead as $key=>$lead){
                $lead_array[] = $lead->id;
                $userdetail->leads=$lead_array;
            }
            $userdetail->total_leads= $totallead->count();
            //$complete2[] =$userdetail;
             //$userdetail = $complete2;
            return response()->json(['status'=>'success', 'code'=>'200','data'=> $userdetail]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }

    public function PasswordReset(Request $request){
        try{
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
                return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            } 
            $id= $user->id;
            if($request->filled('password')){
            $data=User::where('id',$id)->update(['password'=>Hash::make($request->password)]);
            if(count(array($data))){
                return response()->json(['status'=>'success', 'code'=>'200','data'=>"Passwordreset successfully"]);
            }
          }
          return response()->json(['status'=>'success', 'code'=>'200','data'=>"you have not fill password ,can login with old passwordgit "]);
        }
      catch(Exception $e){
        return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }

    }
    public function LogOut(Request $request){
        try{
            auth()->user()->tokens()->delete();
            return response()->json(['status'=>'success', 'code'=>'200','data'=>"user logout successfully"]);
        }
        catch(Exception $e){
                  return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
    public function UserDelete(Request $request){
        try{
            $user= auth('sanctum')->user();
            //dd($user);
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
                return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            } 
            $id=$request->id;
            if($user->role_id==1){
                $lead=Lead::where('assignee',$id)->where('status','active')->get();
                if(count($lead)>0){
                    return response()->json(['status'=>'success', 'code'=>'200','data'=>"user  have active lead"]);
                }
                $data=User::findorfail($id);
                if($data){
                 $data->delete();
                     return response()->json(['status'=>'success', 'code'=>'200','data'=>"user deleted successfully"]);
                }
                else{
                    return response()->json(['status'=>'error','code'=>'404','message'=>'User not found']);
                }
            }
            return response()->json(['status'=>'error','code'=>'401','message'=>'not authorised']);    
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
    
   
}
