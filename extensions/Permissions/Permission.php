<?php

namespace Extensions\Permissions;


use Core\Exceptions\DataBaseException;
use Core\Models\Model;
use Core\System\Traits\Singleton;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Permission extends Model
{
    use Singleton;

    protected $fillable = ['name', 'description'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param array $perm (name, description)
     * @throws DataBaseException
     */
    public function createPermission(array $perm)
    {
        if (!$perm['name']) {
            throw new DataBaseException('Нет обязательного поля "name"', 404);
        }
        $res = Capsule::table('permissions')->select('name')->where('name', '=', $perm['name'])->get();
        if ($res->count() > 0) {
            throw new DataBaseException('Разрешение ' . $perm['name'] . ' уже существует.', 404);
        }
//        Capsule::table('permissions')->insert($perm);
        return self::create($perm);
    }


    /**
     * @param $perm
     * Удаляет разрешение по имени и связи с ролями (роли не трогает)
     */
    public function dropPermission($perm)
    {
        $id = self::where(['name' => $perm])->first();
        $this->roles()->detach($id);
        Capsule::table('permissions')->where('name', '=', $perm)->delete();
    }


    /**
     * @param array $ids
     * Удаляет разрешения по id
     */
    public function dropIds(array $ids)
    {
        self::destroy($ids);
    }

    /**
     * @param string $role
     * @return array
     * Это разрешение добавить к роли $role если роль существует
     */
    public function giveRoleTo(string $role)
    {
        $id = Role::where(['name' => $role])->first();
        if (isset($id->id)) {
            return $this->roles()->syncWithoutDetaching([$id->id]);
        }
    }


    /**
     * @param string $role
     * Это разрешение удаляет связь с ролью $role
     */
    public function revokeRoleTo(string $role)
    {
        $id = Role::where(['name' => $role])->first();
        return $this->roles()->detach($id);
    }


    /**
     * @param array $roles
     * @return array
     * Это разрешение добавляется к ролям $roles
     */
    public function syncRoles(array $roles)
    {
        $ids = [];
        foreach ($roles as $role){
            $ids[] = Role::where(['name'=>$role])->first('id')->id;
        }
        if (count($ids) > 0){
            return $this->roles()->syncWithoutDetaching($ids);
        }
    }


    public function getPermissionById($id)
    {
        return self::find($id);
    }


    public function getPermissionByName(string $name)
    {
        return self::where(['name' => $name])->first();
    }


    /**
     * Все существующие разрешения
     */
    public function allPermissions()
    {
        return self::all();
    }


    /**
     * @param $perm
     * @return bool
     * Существует ли вообще такое разрешение?
     */
    public function has($perm)
    {
        return 0 < Capsule::table('permissions')->where('name', '=', $perm)->get()->count();
    }



    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_has_permissions',
            'permission_id',
            'role_id'
        );
    }


    public function toArray()
    {
        return parent::toArray();
    }
}