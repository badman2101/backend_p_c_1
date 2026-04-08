<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $name = $request->name;
        $email = $request->email;
        $users = User::query();

        $response = $users->when(!empty($name), function ($q) use ($name) {
                return $q->where('fullname', 'LIKE', "%{$name}%");
        })->when(!empty($email), function ($q) use ($email) {
            return $q->where('email', 'LIKE', "%{$email}%");
        })->orderBy("id","DESC")->get();

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'data' => $response
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'role' => 'required|string',
            'password' => 'required|string|min:8',
            'phone' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            //code..
            User::create([
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'email' => $request->email,
                'username' => $request->username, 
                'name' => $request->name,
                'don_vi'=>$request->don_vi,
            ]);
            return response()->json([
                'status' => HttpResponse::HTTP_OK,
                'message' => 'Tạo tài khoản thành công',
            ], HttpResponse::HTTP_OK);
        } catch (\Exception $error) {
            return response()->json([
                'status' => HttpResponse::HTTP_BAD_REQUEST,
                'message' => 'Tạo tài khoản thất bại',
                'error' => $error,
            ], HttpResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::findOrFail($id);
        $userResource = new UserResource($user);

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'data' => $userResource,
        ], HttpResponse::HTTP_OK);
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
        //
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => 'required|string',
            'phone' => 'required|string',
            'username' => ['required', 'string', Rule::unique('users', 'username')->ignore($user->id)],
            'name' => 'required|string',
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'The email address is already in use.',
            'role.required' => 'The role field is required.',
            'phone.required' => 'The phone field is required.',
            'username.required' => 'The username field is required.',
            'username.unique' => 'The username is already in use.',
            'name.required' => 'The name field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            //code...
            $user->update($request->all());

            return response()->json([
                'status' => HttpResponse::HTTP_OK,
                'message' => 'Update User successfull',
            ]);
        } catch (\Exception $error) {
            //throw $th;
            return response()->json([
                'status' => HttpResponse::HTTP_BAD_REQUEST,
                'message' => $error
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => HttpResponse::HTTP_OK,
                'message' => 'Delete User Successfull',
            ], HttpResponse::HTTP_OK);
        } catch (\Exception $error) {
            return response()->json([
                'status' => HttpResponse::HTTP_BAD_REQUEST,
                'message' => 'Delete User Successfull',
                'error' => $error,
            ], HttpResponse::HTTP_BAD_REQUEST);
        }
    }

    public function getProfile($id)
    {
        try {

            $user = User::findOrFail($id);

            return response()->json([
                'status' => HttpResponse::HTTP_OK,
                'data' => $user,
            ], HttpResponse::HTTP_OK);
        } catch (\Exception $error) {
            return response()->json([
                'status' => HttpResponse::HTTP_BAD_REQUEST,
                'error' => $error
            ], HttpResponse::HTTP_BAD_REQUEST);
        }
    }

    public function changePassword(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        try {
            if (!Hash::check($request->password_old, $user->password, [])) {
                return response()->json([
                    'status_code' => 403,
                    'message' => "Mật khẩu cũ không chính xác"
                ]);
            }
            if (Hash::check($request->password, $user->password, [])) {
                return response()->json([
                    'status_code' => 403,
                    'message' => "Mật khẩu cũ và mật khẩu mới đang trùng nhau"
                ]);
            }
            if(!empty($request->user_id)) {
                $user_id = $request->user_id;
    
                $userUpdatePassword = User::findOrFail($user_id);
                
                if($userUpdatePassword) {
                    $userUpdatePassword->update([
                        "password" => Hash::make($request->password)
                    ]);
                }

                return response()->json([
                    'status' => HttpResponse::HTTP_OK,
                    'message' => 'Đổi mật khẩu thành công',
                ], HttpResponse::HTTP_OK);
            }
            
            $password = Hash::make($request->password);

            $user->update([
                'password' => $password,
            ]);

            return response()->json([
                'status' => HttpResponse::HTTP_OK,
                'message' => 'Update password successfull',
            ], HttpResponse::HTTP_OK);
        } catch (\Exception $error) {
            return response()->json([
                'status' => HttpResponse::HTTP_BAD_REQUEST,
                'error' => $error,
            ], HttpResponse::HTTP_BAD_REQUEST);
        }
    }

    public function getUserWithRoleNotUser()  {
        $users = User::where('role', '!=', 'user')->get();

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'data' => $users,
        ], HttpResponse::HTTP_OK);
        
    }
}
