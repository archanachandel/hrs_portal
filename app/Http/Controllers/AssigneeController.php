<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\Models\Lead;
use App\Models\User;
use App\Models\NotifyMsg;

class AssigneeController extends Controller
{
    
  public  function editLead(Request $request){
    try{
      $user= auth('sanctum')->user();
      $usercheck=auth('sanctum')->check();
      if($usercheck=""){
          return response()->json(['status'=>'error','code'=>'401','message'=>'user not login']);
      }
      $id=$request->id;
      $data=Lead::find($id);
      if(is_null($data)){
          return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
      }
      return response()->json(['status'=>'success','code'=>'200','data'=>$data]);
    }
    catch(Exception $e){
      return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);
    }
  }

  public function updateLead(Request $request){
    try{
        $user= auth('sanctum')->user();
        $usercheck=auth('sanctum')->check();
        $username=$user->name;
        if($usercheck=""){
            return response()->json(['status'=>'error','code'=>'401','message'=>'user not login']);
        }
        $id=$request->id;
        $data=Lead::find($id);
        if($request->name){
        $data->name = $request->name;
        }
        if($request->email){
        $data->email=$request->email;
        }
        if($request->phone_number){
        $data->phone_number=$request->phone_number;
        }
        if($request->skype_id){
        $data->skype_id =$request->skype_id;
        }
        if($request->status){
        $data->status=$request->status;
        }
        if($request->category_id){
        $data->category_id=$request->category_id;
        }
        if($request->ip_address){
        $data->ip_address=$request->ip_address;
        }
        if($request->channel_id){
        $data->channel_id=$request->channel_id;
        }
        $data->updated_by=$username;
        $data->save();

        if(is_null($data)){
          return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not updated']);
        }
          return response()->json(['status'=>'success','code'=>'200','data'=>$data]);
    }
    catch(Exception $e){
        return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);
    }
  }

 public function assignLead(Request $request){
    try{
      $user= auth('sanctum')->user();
      $usercheck=auth('sanctum')->check();
      if($usercheck=""){
        return response()->json(['status'=>'error','code'=>'401','message'=>'user not login']);
      }
        $validator = Validator::make($request->all(),[ 
          'lead_id'=> 'required|integer',
          'user_id'=>'required|integer',
          'role_id'=>'required|integer',
      ]);
      if($validator->fails()){ 
          return response()->json(['code'=>'302','error'=>$validator->errors()]);            
      }
      $lead_id=$request->lead_id;
      $user_id=$request->user_id;
      $login_role=$user->role_id;
      $login_name=$user->name;
      $data1=User::find($user_id);

      $role_id= $data1->role_id;
      $name= $data1->name;
      $data=Lead::find($lead_id);
      if($data==""){
        return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
      }
      if($login_role ==1){
        $data->assignee =$user_id;
        $data->assignee_name =$name;
        $data->status='active';
        $data->assigned_by = $login_name;
        }
      if($login_role == 2|| $login_role ==3){
        if($login_role ==3 && $role_id == 3){
          $data->assignee =$user_id;
          $data->assignee_name =$name;
          $data->status='active';
          $data->assigned_by = $login_name;
        }
        elseif($login_role == 2 && ($role_id ==2||$role_id == 3)){
          $data->assignee =$user_id;
          $data->assignee_name =$name;
          $data->status='active';
          $data->assigned_by = $login_name;
        }
        else{
          return response()->json(['status'=>'error', 'code'=>'400','data'=>'cannot assign lead']);
        }
      }
      $data->save();
      $all_lead=NotifyMsg::select('*')->where('lead_id',$lead_id)->first();
      if($all_lead==""){
      return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not  in notification table found']);
      }
      $all_lead->user_id=$user_id;
      $all_lead->is_seen=0;
      $all_lead->user_name=$data1->name;
      $all_lead->save();
      if($data==""){
        return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
      }
      return response()->json(['status'=>'success','code'=>'200','data'=>$data]);
    }
    catch(Exception $e){
      return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);
    }
  }
}
