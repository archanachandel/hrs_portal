<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use App\Models\ContactUs;
use App\Models\Lead;
use App\Models\NotifyMsg;

class ContactUsController extends Controller
{
    public function ContactUs(Request $request)
   {
        try{
            $validator = Validator::make($request->all(),[ 
                'name' => 'required|string ',
                'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
                'phone_number' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'company_name' => 'string'
            ]);
            if($validator->fails()){ 
                return response()->json(['code'=>'302','error'=>$validator->errors()]);            
            }
            $data['name'] = $request->name;    
            $data['email'] = $request->email; 
            $data['phone_number'] = $request->phone_number; 
            $data['company_name'] = $request->company_name;
            $data['contacted_website'] = $request->contacted_website;
            if($request->contacted_website=='Woosper'){
                $channel=2;
                $data['channel_id']=$channel;
            }  
            if($request->contacted_website=='MoogleLabs'){
                $channel=4;
                $data['channel_id']=$channel;
            }  
            $data['status']='new';
            $data['message']=$request->message;
            $user = Lead::create($data);
            
            $lead=Lead::latest()->take(1)->first();
            $notify_lead['lead_id']=$lead->id;
            $notify_lead['is_seen']=0;
            $notify_lead['channel_id']=$channel;
            $user1 = NotifyMsg::create($notify_lead);
            return response()->json(['status'=>'Success','code'=>200, 'Data'=>$data]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
    public function ContactUs1(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[ 
                 'name' => 'required|string ',
                 'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
                 'phone_number' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                 'company_name' => 'string'
             ]);
            if($validator->fails()){ 
                 return response()->json(['code'=>'302','error'=>$validator->errors()]);            
             }
             $data['name'] = $request->name;    
             $data['email'] = $request->email; 
             $data['phone_number'] = $request->phone_number; 
             $data['company_name'] = $request->company_name;
             $data['message']=$request->message;
             $data['contacted_website'] = $request->contacted_website; 
            if($request->contacted_website=='Seasia'){
                $channel[]=1;
            }
            if($request->contacted_website=='Woosper'){
                $channel[]=2;
            } 
            if($request->contacted_website=='Bugraptors'){
                $channel[]=3;
            }  
            if($request->contacted_website=='Moogle'){
                $channel[]=4;
            }   
            if($request->contacted_website=='Cerebrum'){
                $channel[]=5;
            }   
            $channelid=json_encode($channel);
            $data['channel_id']=$channelid;                  
            $data['message']=$request->message;
            $user = Lead::create($data);
            return response()->json(['status'=>'Success','code'=>200, 'Data'=>$data]);
        }
         catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
}
