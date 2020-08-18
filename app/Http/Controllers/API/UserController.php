<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Image;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Gate::allows('isAdmin') || Gate::allows('isAuthor')){
            return User::latest()->paginate(3);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $validation = $request->validate(User::$createRules);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo'],
            'password' => bcrypt($request['password'])
        ]);
    }

    public function profile() {
        return auth('api')->user();
    }

    public function updateProfile(Request $request) {

        $user = auth('api')->user();

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6',
            'type' => 'required'
        ]);


        $currentPhoto = $user->photo;

        if($request->photo != $currentPhoto) {

            $extension = explode('/', mime_content_type($request->photo))[1];
            $name = time().'.'.$extension;
           \Image::make($request->photo)->save(public_path('images/profile/').$name);

            // $request->photo = $name;
            $request->merge(['photo' => $name]);

            $currentPhoto = public_path('images/profile/').$currentPhoto;
            if(file_exists($currentPhoto)){
                @unlink($currentPhoto);
            }
        }



        if(!empty($request->password)){
            $request->merge(['password' => bcrypt($request['password'])]);
        }

        $user->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo'],
            'password' => bcrypt($request['password'])
        ]);



    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $userId = User::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$userId->id,
            'password' => 'sometimes|string|min:6',
            'type' => 'required'
        ]);


        $userId->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo'],
            'password' => bcrypt($request['password'])
        ]);


        return $id;




    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $userId = User::findOrFail($id);
        //delete the user
        $userId->delete();
        //redirect

    }

    public function search() {

        if($search = \Request::get('q')) {
            $users = User::where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                      ->orWhere('email', 'LIKE', "%$search%")
                      ->orWhere('type', 'LIKE', "%$search%");
            })->paginate(20);
        }else {
            $users = User::latest()->paginate(5);
        }
        return $users;
    }
}
