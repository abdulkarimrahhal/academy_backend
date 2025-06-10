<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;


class AdminController extends BaseController
{
    public function getUsers(){
        $users=User::latest()->get();
        return $this->sendResponse(BaseController::collection($users),'All Users');
    }

    public function trachedUsers()
    {
        $users = User::onlyTrashed()->latest()->get();
        return $this->sendResponse(BaseController::collection($users), 'All trached users');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins=Admin::latest()->get();
        return $this->sendResponse(BaseController::collection($admins),'All Admins');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $user_type = Auth::user()->type;
    if($user_type == 'admin' ){
        DB::beginTransaction();

            try {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required',
                    'c_password' => 'required|same:password',
                ]);

                if ($validator->fails()) {
                    return $this->sendError('Validation error', $validator->errors());
                }

                $input = $request->only(['name', 'email', 'password']);
                $input['password'] = Hash::make($input['password']);
                $input['type']=2;
                $user = User::create($input);
                $success['token'] = $user->createToken('tokenKey')->accessToken;
                $success['name'] = $user->name;

                // this execute if the above success
                $input = $request->all();
                $input['user_id'] = $user->id; // Use the newly created user's ID
                $validate = Validator::make($input, [
                    'name' => 'required',
                    'address' => 'required',
                    'phone' => 'required',
                ]);

                if ($validate->fails()) {
                    DB::rollBack();
                    return $this->sendError('Validation error', $validate->errors());
                }

                $admin = Admin::create($input);
                DB::commit();

                return $this->sendResponse($admin, 'admin added successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->sendError('An error occurred', ['error' => $e->getMessage()]);
            }
        }else{
            return $this->sendError('You do not have permission to access for this page error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $admin=Admin::find($id)->forceDelete();
        if($admin == true){
            return $this->sendResponse($admin,'admin deleted successfully');
        }
    }
}
