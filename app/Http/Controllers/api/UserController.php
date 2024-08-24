<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

     // User update
     public function update(Request $request, $id)
     {
         $user = User::find($id);

         if (!$user) {
             return response()->json(['message' => 'User not found'], 404);
         }

         $validator = Validator::make($request->all(), [
             'name' => 'required|string|max:255',
             'mobile' => [
                 'required',
                 'string',
                 'max:15',
                 Rule::unique('users')->ignore($user->id),
             ],
             // Add validation rules for other fields as needed
         ]);

         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 400);
         }


         $user->name = $request->name;
         $user->mobile = $request->mobile;
         $user->blood_group = $request->blood_group;
         $user->email = $request->email;
         $user->gander = $request->gander;
         $user->gardiant_phone = $request->gardiant_phone;
         $user->last_donate_date = $request->last_donate_date;
         $user->whatsapp_number = $request->whatsapp_number;
         $user->division = $request->division;
         $user->district = $request->district;
         $user->thana = $request->thana;
         $user->union = $request->union;
         $user->org = $request->org;

         $user->save();

         return response()->json(['message' => 'User updated successfully'], 200);
     }

     // User delete
     public function delete($id)
     {
         $user = User::find($id);

         if (!$user) {
             return response()->json(['message' => 'User not found'], 404);
         }

         $user->delete();

         return response()->json(['message' => 'User deleted successfully'], 200);
     }

     // Show user details




     public function allUserList(Request $request)
     {
         $query = User::query();

         // Filter by category if provided
         if ($request->has('category')) {
             $query->where('category', $request->input('category'));
         }

         // Filter by status if provided
         if ($request->has('status')) {
             $status = $request->input('status', 'pending');
             $query->where('status', $status);
         }

         // Filter by religion if provided
         if ($request->has('religion')) {
             $query->where('religion', $request->input('religion'));
         }

         // Filter by education level if provided
         if ($request->has('education')) {
             $query->where('education_level', $request->input('education'));
         }

         // Search by name, mobile, or current_address if provided
         if ($request->has('searchText')) {
             $searchText = $request->input('searchText');
             $query->where(function ($q) use ($searchText) {
                 $q->where('name', 'LIKE', "%{$searchText}%")
                   ->orWhere('phone', 'LIKE', "%{$searchText}%")
                   ->orWhere('current_address', 'LIKE', "%{$searchText}%");
             });
         }

         // Order by id and get the results
         $users = $query->with('decisions')->orderBy('id', 'desc')->get();

         return response()->json($users);
     }




     function getUser(Request $request, $id){

        $user = User::find($id);
        return response()->json($user);
     }
     function updateUserStatus(Request $request) {

        $id = $request->id;
        $status = $request->status;
        $user = User::find($id);
        if($user){
            $user->update(['status'=>$status]);
        }else{
            return response()->json('User Not Found');
        }
        return response()->json($user);
     }

     function DeleteUser(Request $request,$id) {
        $user = User::find($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
     }


}
