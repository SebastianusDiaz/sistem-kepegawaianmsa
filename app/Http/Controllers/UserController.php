<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $archiveService;

    public function __construct(\App\Services\ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'profile.division', 'profile.position']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($q) use ($search) {
                        $q->where('nip', 'like', "%{$search}%");
                    });
            });
        }

        // Role Filter
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(10);
        $roles = Role::pluck('name'); // Get all roles for filter dropdown

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name');
        $divisions = Division::all();
        $positions = Position::all();
        return view('users.create', compact('roles', 'divisions', 'positions'));
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('UserStore Request:', $request->all());
        $request->validate([
            // User Data
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',

            // Profile Data
            'nip' => 'nullable|string|unique:user_profiles,nip',
            'phone' => 'nullable|string|max:20',
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'required|exists:positions,id',
            'address' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Create User
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'is_active' => $request->boolean('is_active', true),
                ]);

                // 2. Assign Role
                $user->syncRoles($request->roles);

                // 3. Create Profile
                UserProfile::create([
                    'user_id' => $user->id,
                    'nip' => $request->nip,
                    'phone' => $request->phone,
                    'division_id' => $request->division_id,
                    'position_id' => $request->position_id,
                    'address' => $request->address,
                ]);
            });

            return redirect()->route('users.index')->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function edit(string $id)
    {
        $user = User::with('roles', 'profile')->findOrFail($id);
        $roles = Role::pluck('name');
        $divisions = Division::all();
        $positions = Position::all();

        return view('users.edit', compact('user', 'roles', 'divisions', 'positions'));
    }

    public function update(Request $request, User $user)
    {
        \Illuminate\Support\Facades\Log::info('UserUpdate Request:', $request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',

            // Profile
            'nip' => ['nullable', 'string', Rule::unique('user_profiles')->ignore($user->profile->id ?? null)],
            'phone' => 'nullable|string',
            'division_id' => 'required|exists:divisions,id',
            'position_id' => 'required|exists:positions,id',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                // 1. Update User
                $updateData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'is_active' => $request->boolean('is_active'),
                ];

                if ($request->filled('password')) {
                    $request->validate(['password' => 'min:8|confirmed']);
                    $updateData['password'] = Hash::make($request->password);
                }

                $user->update($updateData);

                // 2. Sync Roles
                $user->syncRoles($request->roles);

                // 3. Update or Create Profile
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip' => $request->nip,
                        'phone' => $request->phone,
                        'division_id' => $request->division_id,
                        'position_id' => $request->position_id,
                        'address' => $request->address,
                    ]
                );
            });

            return redirect()->route('users.index')->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->withErrors(['error' => 'Cannot delete the last admin.']);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }

    /**
     * Display the authenticated user's profile.
     */
    public function profile()
    {
        $user = \Illuminate\Support\Facades\Auth::user()->load(['roles', 'profile.division', 'profile.position']);
        return view('users.profile', compact('user'));
    }

    /**
     * Update authenticated user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        // Use ArchiveService to store the file
        $archive = $this->archiveService->store(
            $request->file('photo'),
            'Profile Photo',
            $user->id
        );

        // Update user profile with the new path
        $user->update(['photo' => $archive->file_path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                // 1. Update User (Name, Email, Password)
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $user->update($userData);

                // 2. Prepare Profile Data
                $profileData = [
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'birth_place' => $request->birth_place,
                    'birth_date' => $request->birth_date,
                    'gender' => $request->gender,
                ];

                // 3. Handle Signature Upload
                if ($request->hasFile('signature')) {
                    $archive = $this->archiveService->store(
                        $request->file('signature'),
                        'Digital Signature',
                        $user->id
                    );
                    $profileData['signature_path'] = $archive->file_path;
                }

                // 4. Update Profile
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );
            });

            return back()->with('success', 'Profil berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui profil: ' . $e->getMessage()]);
        }
    }
}
