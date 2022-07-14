<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lead;
use App\Models\LeadsComment;
use App\Models\User;
use App\Models\Channels;
use App\Models\UserChannel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\NotifyMsg;
use Validator;
use DB;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLeadById($id)
    { 
        try{
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
            return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            }  
            //  $channel_id = ""; 
            // if($user->role_id == "2"){
            //   $channel_id = json_decode($user->channel_id);
            // } 
            // if($user->role_id == "3"){
            //     $channel_id = json_decode($user->channel_id);
            //   } 
            $data=Lead::when($id, function ($query,$id){ 
                return $query->where("id",  $id);
            })->get();
            if($data->count()==0){
            return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
            }
            return response()->json(['status'=>'success', 'code'=>'200','data'=>$data]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
    public function getleadbychannel($id){
        try{
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
            return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            }    
            $data=[];
            $alllead=Lead::SELECT('*')->where('channel_id','=',$id)->get();
            $data['total'] = $alllead->count();
            $active=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','active')->get();
            $data['active'] = $active->count();
            $dead=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','dead')->get();
            $data['dead'] = $dead->count();
            $complete=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','dead')->get();
            $data['complete'] = $complete->count();
            return response()->json(['status'=>'Success','code'=>'200', 'data'=>$data]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
    public function getleadAllChannel(Request $request){
        try{
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();         
            if($usercheck == ""){
                return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']); 
            } 
            $new=[];
            $active=0;
            $dead=0;
            $total=0;
            $complete=0;
            $news =0; 
           
            $allchannel=$request->channel_id;
            $userid=$request->assignee;
            if($request->channel_id==""){
                $allchannel="1,2,3,4,5";
            }
            $channls= explode(",", $allchannel);
            foreach($channls as $key=>$channls){
                $id=$channls;
                $channels=Channels::find($id);
                $name=$channels->name;
                // $totalSum=Lead::SELECT('*')->where('channel_id','=',$id)->get()->count();
                // $activeSum=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','active')->get()->count();
                // $deadSum=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','dead')->get()->count();
                // $completeSum=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','complete')->get()->count();
                // $newSum=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','new')->get()->count();
                  
                $totalSum=Lead::when($userid,function($query,$userid){
                    return $query->where('assignee',$userid);
                })->where('channel_id','=',$id)->get()->count();

                $activeSum=Lead::when($userid,function($query,$userid){
                    return $query->where('assignee',$userid);
                })->where('channel_id','=', $id)->where('status','active')->get()->count();

                $deadSum=Lead::when($userid,function($query,$userid){
                    return $query->where('assignee',$userid);
                })->where('channel_id','=', $id)->where('status','dead')->get()->count();

                $completeSum=Lead::when($userid,function($query,$userid){
                    return $query->where('assignee',$userid);
                })->where('channel_id','=', $id)->where('status','complete')->get()->count();

                $newSum=Lead::when($userid,function($query,$userid){
                    return $query->where('assignee',$userid);
                })->where('channel_id','=', $id)->where('status','new')->get()->count();

                $active+= $activeSum;
                $dead+=$deadSum;
                $total+=$totalSum;
                $news+=$newSum;
                $complete+=$completeSum;

                $new[$name]['channel_id']=$id;
                $new[$name]['total']=$totalSum;
                $new[$name]['active']=$activeSum;
                $new[$name]['dead']=$deadSum;
                $new[$name]['complete']=$completeSum;
                $new[$name]['new']=$newSum;
                
            }
             $totalSum=[];
             $totalSum['active']=$active;
             $totalSum['dead']=$dead;
             $totalSum['new']=$news;
             $totalSum['complete']=$complete;
             $totalSum['total']=$total;

        return response()->json(['status'=>'Success','code'=>'200', 'data'=>$new,'totalLead'=> $totalSum]);
    }
    catch(Exception $e){
        return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
     }
    }
  

    public function PostLead(Request $request){
        try{
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();         
            if($usercheck == ""){
                return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']); 
            } 
            $validator = Validator::make($request->all(),[ 
                'name' => 'required|string ',
                'email' => 'required|email|unique:users',
                'assignee' => 'required|integer',
                'category_id' => 'integer',
                'message' => 'required',
                'status' => 'required|string',
                'phone_number' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10'
               
            ]);
            if($validator->fails()) { 
                 return response()->json(['code'=>'302','error'=>$validator->errors()]);            
                }
        $lead = new Lead;
        $lead->name = $request->name;
        $email=$request->email;
        $lead->email = $request->email;
        $lead->assignee = $request->assignee;
        $lead->category_id = $request->category_id;
        $lead->skype_id = $request->skype_id;
        $lead->phone_number = $request->phone_number;
        $lead->message = $request->message;
        $lead->ip_address = $request->ip_address;
        $lead->status = $request->status;
        $lead->channel_id = $request->channel_id;
        //$lead->datetime = $request->datetime;
        $lead->datetime = date('Y-m-d H:i:s');
        $lead->assignee_name = $request->assignee_name;
        $lead->created_by=$user->name;
        $lead->save();

        $data=Lead::latest()->take(1)->first();
        $lead_id=$data->id;
        $data1['lead_id']=$lead_id;
        $data1['user_id']=$request->assignee;
        $data1['is_seen']=0;
        $data1['user_name']=$request->assignee_name;
        $data1['channel_id']=$request->channel_id;
        $user = NotifyMsg::create($data1);
        
        return response()->json(['status'=>'Success','code'=>200, 'data'=>$lead]);
        }
        catch(\Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    } 
    public function getUserList(){
        try {
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
            return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            }  
            // $channel_id=$request->channel_id;
            // $status=$request->role_id;
            $channel_id = ""; 
            if($user->role_id=="2" ||$user->role_id=="3"){
                $complete=User::SELECT('channel_id')->where('id','=',$user->id)->get();
                $channel_id=str_replace('[','',$complete[0]->channel_id);
                $channel_id=str_replace(']','',$channel_id);
                $channel_id=explode(',',$channel_id);
                $where='';
                foreach($channel_id as $key=>$val){
                    if($key=='0'){
                        $where.='WHERE channel_id LIKE "%'.$val.'%"';
                    }else{
                        $where.=' OR channel_id LIKE "%'.$val.'%"';
                    }
                }
                $complete=DB::select('SELECT * from users '.$where);
             }
            else{
            $complete=user::all();
            }
            $complete = collect($complete);
            $com=$complete->map(function($complete,$key){
              
                    return[
                        'id'=>$complete->id,
                        'user_name'=>$complete->name
                    ];
            });
            if(count($com)==0){
             return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
            }
             return response()->json(['status'=>'success', 'code'=>'200','data'=>$com]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }   
    public function addComment(Request $request){
        try{
            $user_check=auth('sanctum')->check();
            $user=auth('sanctum')->user();  
            if( $user_check==""){
                return response()->json(['status'=>'Error','code'=>400, 'data'=>'user not login']);
            }
            $validator = Validator::make($request->all(),[ 
                'lead_id'=> 'required|integer',
                'comment' => 'required',
            ]);
            if($validator->fails()){ 
                return response()->json(['code'=>'302','error'=>$validator->errors()]);            
            }
            $user_id=$user->id;
            $addComment = new LeadsComment;
            $addComment->lead_id =$request->lead_id;
            $addComment->comment = $request->comment;
            $addComment->user_id =$user_id;
            $addComment->save();
        return response()->json(['status'=>'Success','code'=>200, 'data'=>$addComment]);
        } catch (\Throwable $th) {
            return response()->json(['status'=>'Error','code'=>500, 'message'=>$th->getMessage()]);
        }
    } 
    
    public function getComments($id){
        try{
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
                return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            } 
            $getcomment=LeadsComment::Select('lead_comment.id as comment_id','lead_id','users.name','comment','users.email','users.username','users.role_id', 'users.channel_id','lead_comment.created_at as Created_At','lead_comment.updated_at as Updated_At','created_by')->join('users', 'users.id', '=', 'lead_comment.user_id')
            ->where('lead_id','=',$id)->orderBy('lead_comment.created_at', 'DESC')->get();
            //$getcomment = $getcomment->makeHidden(['password','created_at','updated_at','deleted_at']);
            if($getcomment->isEmpty()){
                return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
            }
            return response()->json(['status'=>'success', 'code'=>'200','data'=>$getcomment]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }

    public function editUser(Request $request){
        try {
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            $username=$user->name;
            if($usercheck == ""){
                return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            }  
            $validator = Validator::make($request->all(),[ 
                'name'=>'sometimes|string|max:255',
                'email'=>'sometimes|regex:/(.+)@(.+)\.(.+)/i',
                'phone_number'=>'sometimes|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'role_id'=>'sometimes|integer',
                'channel_id'=>'sometimes',
                'image'=>'sometimes|mimes:jpeg,bmp,png,jpg|image|max:4096'
            ]);
            if($validator->fails()) { 
                return response()->json(['code'=>'302','error'=>$validator->errors()]);            
            }
            $user = Auth::user();
            $data=$request->all();
            //unset($user['password']);
            $user->name = $request->name;
            $user->email = $request->email;
            if($request->password){
                $user->password =Hash::make($user->password);
            }
            if($request->username){
             $user->username = $request->username;
            }
            if($request->phone_number){
                $user->phone_number = $request->phone_number;
             }
            if($request->role_id ){
                $user->role_id  = $request->role_id ;
            }
            if($request->channel_id ){
                $user->channel_id  = $request->channel_id ;
            }
          
            $image=[];
            if($request->file('image')){
            $path = $request->file('image')->store('image','public');
            $name=$request->file('image')->getClientOriginalName();
            $image=array("imagename"=>$name, "path"=>$path);
            $user->image=$image;
             }
            $user->updated_by = $username;
            $user->save();
            $channel_id=str_replace('[','',$user->channel_id);
            $channel_id=str_replace(']','',$channel_id);
            $channel_id=str_replace(' ','',$channel_id);
            $channel_id=explode(',',$channel_id);
            $user->channel_id=$channel_id;
            return response()->json(['status'=>'success', 'code'=>'200','data'=> $user]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }  
    public function getUserdetail(Request $request)
    { 
        try{
         $user= auth('sanctum')->user();
         $usercheck = auth('sanctum')->check();
         if($usercheck == ""){
           return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
          }    
           $channel_id = ""; 
           $name=$request->name;
           $role_id=$request->role_id;
           $channel=$request->channel_id;
         if($user->role_id=="2" ||$user->role_id=="3"){
            $complete=User::SELECT('channel_id')->where('id','=',$user->id)->get();
            //dd($complete[0]);
            $channel_id=str_replace('[','',$complete[0]->channel_id);
            $channel_id=str_replace(']','',$channel_id);
            $channel_id=explode(',',$channel_id);
            }
            $complete1=User::when($channel_id, function($query,$channel_id){ 
                foreach($channel_id as $key=>$val){
                    if($key=='0'){
                        $query->where('channel_id','LIKE', '%'.$val .'%');
                    }else{
                        $query->orWhere('channel_id','LIKE', '%'.$val .'%');
                    }
                }return $query;
            })->get();
            if($name|| $role_id|| $channel){
                $complete1=User::when($name, function ($query,$name){ 
                return $query->where('name','LIKE', '%'.$name .'%');
             
                })->when($channel, function ($query,$channel){ 
                    return $query->where('channel_id','LIKE', '%'.$channel .'%');
                
                })->when($role_id, function ($query,$role_id){ 
                    return $query->where('role_id',  $role_id);
                })->get();
            }
                foreach($complete1 as $complete){
                    $id= $complete->id;
                    $channel_id=str_replace('[','',$complete->channel_id);
                    $channel_id=str_replace(']','',$channel_id);
                    $channel_id=str_replace(' ','',$channel_id);
                    $channel_id=explode(',',$channel_id);
                    $complete->channel_id=$channel_id;
                    $lead_array = [];
                    $totallead=Lead::select('*')->where('assignee','=',$id)->get();
                    foreach($totallead as $key=>$lead){
                        $lead_array[] = $lead->id;
                    }
                    $complete->leads=$lead_array;
                    $complete->total_leads= $totallead->count();
                }
                //dd($complete1);
            // $complete1->map(function($complete1){
            //         $complete1->registered=$complete1->created_at->diffForHumans();
            //     });
                //add( $complete);
                //$complete1 = $complete2;
            
            //->join('leads')->where('assignee ', '=', 'user.id')->count();
            // ->join('leads', 'leads.assignee ', '=', 'user.id')
            //   ->get(['users.*', 'leads.assignee ']);

          
          
        //   if($user->role_id=="1"){
        //   $complete1=user::all();
        //   }
          
          //dd( $channel);
          //dd($complete);
        //   $where1='';
        //   if($role_id && $channel_id){
        //      $where1.="WHERE role_id = ".$role_id." AND channel_id LIKE '%".$channel_id."%'";
        //   }
        //   if($role_id && (!$channel_id)){
        //      $where1.="WHERE role_id ='$role_id'";
        //  }
        //   if($channel_id &&(!$role_id)){
        //       $where1.="WHERE channel_id LIKE '%".$channel_id."%'";;
        //   }
        //      //$detail=DB::select("SELECT * from users ".$where1);
        //     $detail=$complete->select("SELECT * from users ".$where1);
        //     //dd($complete);
            
            //dd($complete1);
            // foreach($complete1 as $com){
            //     $com->channel_id=str_replace('"','',$com->channel_id);
            // }
            // dd($com->channel_id);
           
            // 
            //$detail=$complete1
       
           //dd($detail);
          // if(is_null())
         
            return response()->json(['status'=>'success', 'code'=>'200','data'=>$complete1]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
    public function getLeadByPost(Request $request)
    { 
        try{
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
            return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            }    
            $channel = ""; 
            if($user->role_id == "2"){
              $channel = json_decode($user->channel_id);
            } 
            if($user->role_id == "3"){
                $channel = json_decode($user->channel_id);
              } 
              $channel_id=$request->channel_id;

              $status=$request->status;
              $owner_id=$request->assignee;
              //dd($user->role_id);
            $data=Lead::when($channel, function ($query,$channel) { 
                return $query->whereIn("channel_id",  $channel);
            })
            ->when($channel_id, function ($query,$channel_id){ 
                return $query->where('channel_id',$channel_id);
            })
            ->when($status, function ($query,$status) { 
                return $query->where('status',$status);
            })
            ->when($owner_id, function ($query,$owner_id) { 
                return $query->where('assignee',$owner_id);
             })->orderBy('created_at', 'DESC')->get();
                if(empty($data))
                {
                    return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
                }
                return response()->json(['status'=>'success', 'code'=>'200','data'=>$data]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }
}
