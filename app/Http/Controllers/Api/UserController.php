<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(Request $request)
    {
    	//echo json_encode($request->all());exit;
        $credentials=$request->only('email','password');
        if(Auth::attempt($credentials)){
            $token=Str::random(80);
            Auth::user()->api_token=$token;
            Auth::user()->save();
            return response()->json(['token'=>$token], 200);
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
}
