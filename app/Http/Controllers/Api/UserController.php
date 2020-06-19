<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;
use App\Profile;
use Auth;
use Illuminate\Support\Str;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource. 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $per_page=$request->per_page;
        return response()->json(['users'=> new UserCollection(User::paginate($per_page)),'roles'=>Role::pluck('name')->all()], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $role=Role::where('name',$request->role)->first();
        $user=new User([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            
        ]);
        $user->role()->associate($role);
        $user->save();
        $user->profile()->save(new Profile());
        return response()->json(['user' =>new UserResource($user)],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=User::where('name','LIKE',"%$id%")->get();
        return response()->json(['user' =>$user],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user=User::find($id);
        $user->name=$request->name;
        $user->save();
        return response()->json(['user' =>$user],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=User::find($id)->delete();
        return response()->json(['user' =>$user],200);
    }


    public function deleteMultiple(Request $request){
        $user=User::whereIn('id',$request->user_id)->delete();
        return response()->json(['message'=>'Deleted'],200);
    }



    public function login(Request $request)
    {
    	//echo json_encode($request->all());exit;
        $credentials=$request->only('email','password');
        if(Auth::attempt($credentials)){
            $token=Str::random(80);
            Auth::user()->api_token=$token;
            Auth::user()->save();
            $role=Auth::user()->hasAccess();
            return response()->json(['token'=>$token,'isAdmin'=>$role], 200);
        }
        return response()->json(['status' => 'Email or Password is Wrong'], 403);

    	/*$username=$request->email;
    	$password=becrypt($request->password);
    	$user=User::where('email',$username)->where('password',$password)->first();
        echo json_encode($user);exit;
    	if($user){
    	$token=Hash::make($request->password);
    	$user->api_token=$token;
    	$user->save();
    	return response()->json(['token'=>$token], 200);
    	}
    	return response()->json(['status' => 'Email or Password is Wrong'], 403); */

    }

    public function varify(Request $request){
        return $request->user()->only('name','email');
    }
}
