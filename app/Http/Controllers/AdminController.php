<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendUserCredentialJob;

class AdminController extends Controller
{
    // public function dashboard()
    // {
    //     $staff = User::with('documents')->where('role', 'staff')->get();
    //     // dd($staff);
    //     return view('admin.dashboard', compact('staff'));
    // }


    public function dashboard(Request $request)
    {
        $search = $request->input('search');

        $staffQuery = User::with('documents')
            ->where('role', 'staff')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'email', 'phone', 'role', 'is_active', 'profile_image')
            ->orderBy('id', 'DESC');

        $staff = $staffQuery->paginate(5)->withQueryString();

        return view('admin.dashboard', compact('staff', 'search'));
    }

    public function createUserForm()
    {
        return view('admin.create_user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,staff',
            'profile_image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'documents.*' => 'nullable|mimes:png,jpg,jpeg,webp,pdf|max:3000',
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
                'is_active' => true,
            ]);

            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('profile_images', 'public');
                $user->profile_image = $path;
                $user->save();
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $filePath = $file->store('user_documents', 'public');

                    $user->documents()->create([
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalName(),
                    ]);
                }
            }

            SendUserCredentialJob::dispatch($user, $request->password);

            DB::commit();

            return redirect()->route('admin.dashboard')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }



    public function toggleActive($id)
    {
        $u = User::findOrFail($id);
        $u->is_active = ! $u->is_active;
        $u->save();
        return back()->with('success', 'Status updated.');
    }

    public function editUserForm($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }


    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|unique:users,email,' . $id,
            'phone'         => 'nullable|string|unique:users,phone,' . $id,
            'role'          => 'required|in:admin,staff',
            'password'      => 'nullable|min:6',
            'profile_image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);

        DB::beginTransaction();

        try {

            $user->name  = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role  = $request->role;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            if ($request->hasFile('profile_image')) {

                if ($user->profile_image && file_exists(storage_path('app/public/' . $user->profile_image))) {
                    unlink(storage_path('app/public/' . $user->profile_image));
                }

                $user->profile_image = $request->file('profile_image')
                    ->store('profile_images', 'public');
            }

            $user->save();

            DB::commit();

            return redirect()->route('admin.dashboard')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if ($user->image) {
            $imagePath = public_path('uploads/profile/' . $user->image);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        foreach ($user->documents as $doc) {
            $docPath = storage_path('app/public/' . $doc->file_path);

            if (file_exists($docPath)) {
                unlink($docPath);
            }

            $doc->delete();
        }
        $user->delete();

        return back()->with('success', 'User and profile image deleted.');
    }
}
