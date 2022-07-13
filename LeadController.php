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
use Validator;
use DB;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLead()
    { 
        try{
            $user= auth('sanctum')->user();
            $usercheck = auth('sanctum')->check();
            if($usercheck == ""){
            return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']);    
            }  
             $channel_id = ""; 
            if($user->role_id == "2"){
              $channel_id = json_decode($user->channel_id);
            } 
            if($user->role_id == "3"){
                $channel_id = json_decode($user->channel_id);
              } 
            $data=Lead::when($channel_id, function ($query,$channel_id) { 
                return $query->whereIn("channel_id",  $channel_id);
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
    public function getleadAllChannel(){
        try{
                $user= auth('sanctum')->user();
                $usercheck = auth('sanctum')->check();         
                if($usercheck == ""){
                    return response()->json(['status'=>'error','code'=>'401','message'=>'User is not login']); 
                } 
                $allchannel=Channels::get();
                $post=[];
                foreach($allchannel as $channls){
                $data=[];
                $id=$channls->id;
                $data['channel_id']=$id;
                $data['channel_name']=$channls->name;
                $totalleads=Lead::SELECT('*')->where('channel_id','=',$id)->get();
                $data['total'] = $totalleads->count();
                $active=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','active')->get();
                $data['active'] = $active->count();
                $dead=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','dead')->get();
                $data['dead'] = $dead->count();
                $complete=Lead::SELECT('*')->where('channel_id','=', $id)->where('status','complete')->get();
                $data['complete'] = $complete->count();
                $post[]= $data;
            }
            return response()->json(['status'=>'Success','code'=>'200', 'data'=>$post]);
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
                'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'channel_id' =>'required|integer'
            ]);
            if($validator->fails()) { 
                 return response()->json(['code'=>'302','error'=>$validator->errors()]);            
                }
        $lead = new Lead;
        $lead->name = $request->name;
        $email=$request->email;
        $existence = Lead::where('email','=',$email)->exists();
        if($existence){
            return response()->json(['status'=>'Error','code'=>400, 'data'=>'Record already exits']); 
        }
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
        $lead->datetime = date('Y-m-d H:i:s');;
        $lead->created_by=$user->name;
        $lead->save(); 
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
                //dd( $channel_id);
                $where='';
                foreach($channel_id as $key=>$val){
                    // dd($key);
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
            if(count($complete)==0){
             return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
            }
             return response()->json(['status'=>'success', 'code'=>'200','data'=>$complete]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }   
    public function addComment(Request $request){
        try{
            $user_check=auth('sanctum')->check();
            $user=auth('sanctum')->user();  
           // dd($user);
            if( $user_check==""){
                return response()->json(['status'=>'Error','code'=>400, 'data'=>'']);
            }
            $validator = Validator::make($request->all(),[ 
                'lead_id '=> 'required|integer',
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
              //dd($user->role_id);
            $data=Lead::when($channel, function ($query,$channel) { 
                return $query->whereIn("channel_id",  $channel);
            })->get();
             //dd($data);
              $channel_id=$request->channel_id;
              $status=$request->status;
              $owner_id=$request->assignee;
              //dd($request);
                if($user->role_id == "2"||$user->role_id == "3"){
                    $detail=$data->when($channel_id, function ($query,$channel_id){ 
                        return $query->where('channel_id',$channel_id);
                    })
                    ->when($status, function ($query,$status) { 
                        return $query->where('status',$status);
                    })
                    ->when($owner_id, function ($query,$owner_id) { 
                        return $query->where('assignee',$owner_id);
                    })->all();
                }
              if($user->role_id == "1"){
                $detail=Lead::when($channel_id, function ($query,$channel_id){ 
                            return $query->where('channel_id',$channel_id);
                        })
                        ->when($status, function ($query,$status) { 
                            return $query->where('status',$status);
                        })
                        ->when($owner_id, function ($query,$owner_id) { 
                            return $query->where('assignee',$owner_id);
                        })->get();
                }  
                    //->dd();
                    //dd($detail);
            //$project->where('title', 'Like', '%' . request('term') . '%')->orderBy('id', 'DESC')->paginate(15);
                if(empty($detail))
                {
                    return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
                }
                return response()->json(['status'=>'success', 'code'=>'200','data'=>$detail]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
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
            ->where('lead_id','=',$id)->get();
            //$getcomment = $getcomment->makeHidden(['password','created_at','updated_at','deleted_at']);
            if($getcomment->count()==0){
                return response()->json(['status'=>'error', 'code'=>'400','data'=>'data not found']);
            }
            return response()->json(['status'=>'success', 'code'=>'200','data'=>$getcomment]);
        }
        catch(Exception $e){
            return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
        }
    }

    public function editUser(Request $request){

        // return response()->json($request);
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
            //dd($user->password);
            //$user=$request->all();
            $image=[];
            if($request->file('image')){
            $path = $request->file('image')->store('image','public');
            $name=$request->file('image')->getClientOriginalName();
            $image=array("imagename"=>$name, "path"=>$path);
            $user->image=$image;
             }
            $user->updated_by = $username;
            //User::find($id)->update($user);  
            $user->save();
           // $user->update();
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
          $role_id=$request->role_id;
          $channel_id=$request->channel_id;
          $where='';
          if($role_id && $channel_id){
             $where.="WHERE role_id = ".$role_id." AND channel_id LIKE '%".$channel_id."%'";
          }
          if($role_id && (!$channel_id)){
             $where.="WHERE role_id ='$role_id'";
         }
          if($channel_id &&(!$role_id)){
              $where.="WHERE channel_id LIKE '%".$channel_id."%'";
          }
         $detail=DB::select("SELECT * from users ".$where);
         return response()->json(['status'=>'success', 'code'=>'200','data'=>$detail]);
     }
 
 catch(Exception $e){
     return response()->json(['status'=>'error','code'=>'500','meassage'=>$e->getmessage()]);
 }
 }
}
