<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;


class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::all()->map(function ($user) {
            $role = $user->getRoleNames()->first() ?? null;
            return [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $role,
            ];
        });

        return response()->json(['users' => $users], 200);
    }

    /**
     * Delete multiple users.
     *
     * Expects a JSON payload like:
     * {
     *   "ids": [1, 2, 3]
     * }
     *
     * Returns a success message upon deletion.
     */
    public function deleteUsers(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                PersonalAccessToken::whereIn('tokenable_id', $request->ids)
                    ->where('tokenable_type', User::class)
                    ->delete();

                User::whereIn('id', $request->ids)->delete();
            });

            return response()->json(['message' => 'Users deleted successfully'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function updateUser(Request $request)
    {

        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->id);

            if ($request->filled('username')) {
                $user->username = $request->username;
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
            DB::commit();

            return response()->json([
                'message' => 'User updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'un Expected Error'
            ], 500);
        }
    }
}
