<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Mail\notifyMail;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\Subscribe;
use App\Models\Subscriber;
use DB;

class EmailController extends Controller
{
    /**
     * Ship the given order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendmail(Request $request)
    {
        try{
              $id=$request->id;
              $lead_id=$request->lead_id;
              $sender_id=$request->sender_id;
              $user=User::findorfail($id);
              $lead_deatil=Lead::findorfail($lead_id);
              $sender=User::findorfail($sender_id);
              $user_name= $user->name;
              $user_email=$user->email;
              $data=['name'=> $user_name,'sender'=>$sender,'lead_detail'=>$lead_deatil,'from'=>'woospers@gmail.com', 'data'=>"Thanks ",'subject'=>"Regarding lead assigned"];
              $user['to']=$user->email;
               Mail::send('mail', $data, function($message) use ($user)
              {
                $message->to( $user['to'])->subject('Regarding lead assigned');
             });
             if(Mail::failures()){
                return response()->Fail('Sorry! Please try again latter');
               }
               else{
                return response()->json(['status'=>'Success','code'=>'200','message' => "Thank you, please check your inbox"]);
               } 
            }
         catch (Exception $th) {
            return response()->json(['status'=>'error','code'=>'500','message'=>$th->getMessage()]);
        }
    }
}

?>