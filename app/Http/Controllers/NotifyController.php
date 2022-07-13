<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NotifyMsg;

class NotifyController extends Controller
{  
  public function getnotification(){
  try{
      $all_lead=NotifyMsg::Select('*')->where('is_seen','0')->whereNull('user_id')->get();
      if($all_lead->isEmpty()){
        return response()->json(['status'=>'error','code'=>'400','data' => "no new notification"]);
      }
      return response()->json(['status'=>'Success','code'=>'200','data' => $all_lead]);
    }
    catch(Exception $e){
      return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);
    }
  }
  public function assign_notification(){
  try{
    $all_lead=NotifyMsg::Select('*')->where('user_id','!=','')->where('is_seen','=',0)->get();
    if($all_lead->isEmpty()){
        return response()->json(['status'=>'error','code'=>'400','data' => "no new lead assign notification"]);
      }
        return response()->json(['status'=>'Success','code'=>'200','data' => $all_lead]);
    }
    catch(Exception $e){
      return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);
    }
  }
  public function close_notification(Request $request){
    try{
      $id=$request->lead_id;
      $all_lead=NotifyMsg::Select('*')->where('lead_id',$id)->update(['is_seen'=>1]);
      if($all_lead >0)
        return response()->json(['status'=>'Success','code'=>'200','data' => 1]);
      else{
        return response()->json(['status'=>'Success','code'=>'200','data' => "no notification to close"]);
        } 
    }
    catch(Exception $e){
      return response()->json(['status'=>'error','code'=>'500','message'=>$e->getmessage()]);
    }
  }
}
