<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'mobile',
        'adult_family_members',
        'applicant_name',
        'applicant_signature',
        'application_preparer_name',
        'arrival_legality',
        'arriving_date',
        'category',
        'country_of_birth',
        'country_of_conflict',
        'current_address',
        'current_institution',
        'current_living',
        'dob',
        'education_level',
        'father_name',
        'gender',
        'head_name',
        'head_phone',
        'highest_education',
        'institution_address',
        'marital_status',
        'minor_family_members',
        'mother_name',
        'national_id_or_ssn',
        'nationality',
        'perjury_declaration',
        'permanent_address',
        'phone',
        'preparer_address',
        'preparer_email',
        'preparer_phone',
        'race',
        'recent_exam_grade',
        'reference1_address',
        'reference1_email',
        'reference1_name',
        'reference1_phone',
        'reference1_relationship',
        'reference2_address',
        'reference2_email',
        'reference2_name',
        'reference2_phone',
        'reference2_relationship',
        'religion',
        'sheltering_country',
        'situation',
        'terms_agreement',
        'total_family_members',
        'status',
        'otp',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_expires_at' => 'datetime',
    ];


    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org');
    }




 // Required method from JWTSubject
 public function getJWTIdentifier()
 {
     return $this->getKey();
 }

 // Required method from JWTSubject
 public function getJWTCustomClaims()
 {
     return [];
 }

 public function roles()
 {
     return $this->belongsTo(Role::class, 'role_id');
 }

public function permissions()
{
    return $this->hasManyThrough(
        Permission::class,
        'role_permission', // Pivot table name
        'user_id',         // Foreign key on the pivot table related to the User model
        'role_id',         // Foreign key on the pivot table related to the Permission model
        'id',              // Local key on the User model
        'role_id'          // Local key on the pivot table related to the Permission model
    );
}

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // public function hasPermission($permission)
    // {
    //     foreach ($this->roles as $role) {
    //         if ($role->permissions->contains('name', $permission)) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }


    public function hasPermission($routeName)
    {
        // Get the user's roles with eager loaded permissions
        $permissions = $this->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten();




        // Check if any of the user's permissions match the provided route name and permission name
        $checkPermission =  $permissions->contains(function ($permission) use ($routeName) {

            return true;

            // Log:info($permission->name === $routeName && $permission->permission);
            // return $permission->path === $routeName && $permission->permission;
        });



        return $checkPermission;

    }


}
