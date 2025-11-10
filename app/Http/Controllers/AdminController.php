<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['auth','role:admin']);
    // }

    public function dashboard()
    {
        $staff = User::where('role','staff')->get();
        return view('admin.dashboard', compact('staff'));
    }

    public function createUserForm()
    {
        return view('admin.create_user');
    }

    // Admin can create staff or admin
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email'=> 'nullable|email|unique:users,email',
            'phone'=> 'nullable|string|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,staff',
        ]);

        User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'phone'=> $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('admin.dashboard')->with('success','User created.');
    }

    // Optional: toggle active
    public function toggleActive($id)
    {
        $u = User::findOrFail($id);
        $u->is_active = ! $u->is_active;
        $u->save();
        return back()->with('success','Status updated.');
    }
}
