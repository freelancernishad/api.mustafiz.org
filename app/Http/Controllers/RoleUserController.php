<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RoleUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6',
            'mobile' => 'required|string|max:20',
            'role_id' => 'required|exists:roles,id',
            // Add other validation rules for your fields here
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Get the role based on role_id
        $role = Role::findOrFail($request->role_id);

        $user = User::create(array_merge($request->all(), ['role' => $role->name]));
        return response()->json(['user' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json(['user' => $user], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            // 'email' => 'string|email|max:255|unique:users,email,'.$id,
            'password' => 'string|min:6',
            'mobile' => 'string|max:20',
            'role_id' => 'exists:roles,id',
            'adult_family_members' => 'nullable|string|max:255',
            'applicant_name' => 'nullable|string|max:255',
            'applicant_signature' => 'nullable|string|max:255',
            'application_preparer_name' => 'nullable|string|max:255',
            'arrival_legality' => 'nullable|string|max:255',
            'arriving_date' => 'nullable|date_format:Y-m-d H:i:s',
            'category' => 'nullable|string|max:255',
            'country_of_birth' => 'nullable|string|max:255',
            'country_of_conflict' => 'nullable|string|max:255',
            'current_address' => 'nullable|string|max:255',
            'current_institution' => 'nullable|string|max:255',
            'current_living' => 'nullable|string|max:255',
            'dob' => 'nullable',
            'education_level' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'head_name' => 'nullable|string|max:255',
            'head_phone' => 'nullable|string|max:255',
            'highest_education' => 'nullable|string|max:255',
            'institution_address' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:255',
            'minor_family_members' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'national_id_or_ssn' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'perjury_declaration' => 'nullable|string',
            'permanent_address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'preparer_address' => 'nullable|string|max:255',
            'preparer_email' => 'nullable|string|max:255',
            'preparer_phone' => 'nullable|string|max:255',
            'race' => 'nullable|string|max:255',
            'recent_exam_grade' => 'nullable|string|max:255',
            'reference1_address' => 'nullable|string|max:255',
            'reference1_email' => 'nullable|string|max:255',
            'reference1_name' => 'nullable|string|max:255',
            'reference1_phone' => 'nullable|string|max:255',
            'reference1_relationship' => 'nullable|string|max:255',
            'reference2_address' => 'nullable|string|max:255',
            'reference2_email' => 'nullable|string|max:255',
            'reference2_name' => 'nullable|string|max:255',
            'reference2_phone' => 'nullable|string|max:255',
            'reference2_relationship' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:255',
            'sheltering_country' => 'nullable|string|max:255',
            'situation' => 'nullable|string',
            'terms_agreement' => 'nullable|string|max:255',
            'total_family_members' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Get the role based on role_id
        if ($request->has('role_id')) {
            $role = Role::findOrFail($request->role_id);
            $request->merge(['role' => $role->name]);
        }

        $user = User::findOrFail($id);

        // Update the password separately if it's being changed
        if ($request->has('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }

        $requestdata = $request->except('password');
        // $requestdata['dob']=date('Y-m-d', strtotime($request->dob. ' + 1 days'));
        $requestdata['dob']= date('m-d-Y', strtotime($request->dob));

        $user->update($requestdata);
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json(['user' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if($user){
            $user->delete();
            return ['status'=>'success'];
        }
        return ['status'=>'not found'];
    }
}
