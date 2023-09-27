<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WorkerController extends Controller
{
    public function WorkerDashboard(){

        return view('worker.worker_index');

    } // End Mehtod 

    public function UserInformation(){

        return view('worker.user.user_information');

    } // End Mehtod 


    public function WorkerLogin(){
        return view('worker.worker_login');
    } // End Mehtod 


    public function WorkerDestroy(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/worker/login');
    } // End Mehtod 


    public function AdminProfile(){

        $id = Auth::user()->id;
        $adminData = User::find($id);
        return view('worker.worker_profile_view',compact('adminData'));

    } // End Mehtod 

    public function WorkerProfileStore(Request $request){

        $id = Auth::user()->id;
        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address; 


        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/worker_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/worker_images'),$filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Worker Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    } // End Mehtod 


    public function WorkerChangePassword(){
        return view('worker.worker_change_password');
    } // End Mehtod 


    public function WorkerUpdatePassword(Request $request){
        // Validation 
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed', 
        ]);

        // Match The Old Password
        if (!Hash::check($request->old_password, auth::user()->password)) {
            return back()->with("error", "Old Password Doesn't Match!!");
        }

        // Update The new password 
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)

        ]);
        return back()->with("status", " Password Changed Successfully");

    } // End Mehtod 



    public function AllWorker(){
        $alladminuser = User::where('role','admin')->latest()->get();
        return view('backend.admin.all_admin',compact('alladminuser'));
    }// End Mehtod 
}
