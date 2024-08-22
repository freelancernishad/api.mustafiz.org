<?php

namespace App\Http\Controllers\api;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Get all Admins.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $role = $request->input('role');

        // Query to get admins based on role
        $adminsQuery = Admin::with('roles');

        if ($role) {
            $adminsQuery->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            });
        }

        $admins = $adminsQuery->get();

        return response()->json([
            'admins' => $admins
        ], 200);
    }

    /**
     * Get a single Admin by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById($id)
    {
        $admin = Admin::findOrFail($id);

        return response()->json([
            'admin' => $admin
        ], 200);
    }

    /**
     * Update Admin information.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'location' => 'string|max:255',
            'role' => 'string|max:255',
            'email' => 'string|email|max:255|unique:admins,email,' . $admin->id,
            'password' => 'string|min:8|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $admin->update($request->only(['name', 'location', 'role', 'email']));

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
            $admin->save();
        }

        return response()->json([
            'message' => 'Admin updated successfully',
            'admin' => $admin
        ], 200);
    }

    /**
     * Delete an Admin.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return response()->json([
            'message' => 'Admin deleted successfully'
        ], 200);
    }


}
