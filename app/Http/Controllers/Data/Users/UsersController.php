<?php

namespace App\Http\Controllers\Data\Users;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                return response('Unauthorized', 403);
            }
            return $next($request);
        })->only('destroy', 'index');
    }

    public function index()
    {
        return response()->json(['results' => User::latest()->paginate(20)]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->only('name', 'role', 'email') + ['password' => Hash::make($request->password)]);

        return response()->json(['user' => $user]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return response()->json(['user' => $user->only('id', 'name', 'role', 'email')]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => "Successfully deleted user"]);
    }
}
