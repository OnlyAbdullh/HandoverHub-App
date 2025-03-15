<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::all()->map(function ($user) {
            $role = $user->getRoleNames()->first() ?? null;
            return [
                'id' => $user->id,
                'username' => $user->name,
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

        // Optional: Prevent deletion of the authenticated user.
        // $idsToDelete = array_diff($request->ids, [$request->user()->id]);

        User::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Users deleted successfully'], 200);
    }

}
