<?php


namespace Modules\CommonModels;


use Core\Models\Model;
use Extensions\Permissions\HasRoles;

class User extends Model
{
    use HasRoles;

    protected $fillable = ['name', 'email', 'password', 'created_at', 'updated_at', 'attempt', 'email_verified_at', 'remember_token'];

}