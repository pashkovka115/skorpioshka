<?php


namespace Extensions\Permissions;


use Core\Exceptions\DataBaseException;
use Core\Models\Model;
use Core\System\Traits\Singleton;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Role extends Model
{
    protected $fillable = ['name', 'description'];
//    protected $fillable = ['*'];
    use Singleton;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param array $perm (name, description)
     * @return Role
     * @throws DataBaseException
     */
    public function createRole(array $role)
    {
        if (!$role['name']) {
            throw new DataBaseException('Нет обязательного поля "name"', 404);
        }
        $res = Capsule::table('roles')->select('name')->where('name', '=', $role['name'])->get();
        if ($res->count() > 0) {
            throw new DataBaseException('Роль ' . $role['name'] . ' уже существует.', 404);
        }

        return self::create($role);
    }


    /**
     * @param $role string
     * Удаляет роль по имени и связи с разрешениями (разрешения не трогает)
     */
    public function dropRole($role)
    {
        $id = self::where(['name' => $role])->first();
        $this->permissions()->detach($id);
        Capsule::table('roles')->where('name', '=', $role)->delete();
    }

    /**
     * @param $id
     * @return array
     * Возвращает разрешения по id роли
     */
    public function getPermissionsByIdRole($id)
    {
        $perms = self::find($id);
        if (!is_null($perms))
            return $perms->permissions()->get();
        return collect();
    }


    /**
     * @param $name
     * @return array
     * Возвращает разрешения по имени роли
     */
    public function getPermissionsByNameRole($name)
    {
        $perms = self::with('permissions')->where(['roles.name' => $name])->first();
        if (!is_null($perms))
            return $perms->permissions;
        return collect();
    }


    /**
     * @param $perm array|string|Collection
     * @return array Pivot-attached
     * Назначить этой роли разрешения $perms
     * если разрешения $perms существуют
     */
    public function givePermissionTo($perms)
    {
        if (is_string($perms)) {
            $id = Permission::where(['name' => $perms])->first();
            if (isset($id->id)) {
                return $this->permissions()->syncWithoutDetaching([$id->id]);
            }
        }
        if ($perms instanceof Collection) {
            $arr = [];
            foreach ($perms as $perm) {
                $arr[] = $perm->name;
            }
            $perms = $arr;
        }
        $arr = [];
        foreach ($perms as $perm) {
            $id = Permission::where(['name' => $perm])->first();
            // если нашёлся объект
            if (isset($id->id)) {
                $arr[] = $this->permissions()->syncWithoutDetaching([$id->id]);
            }
        }
        return $arr;
    }

    /**
     * @param $perm string
     * @return int
     * Отвязывает разрешение $perm от этой роли
     */
    public function revokePermissionTo(string $perm)
    {
        $id = Permission::where(['name' => $perm])->first();
        return $this->permissions()->detach($id);
    }


    /**
     * @param array $perms
     * @return array
     * Синхронизирует роль с разрешениями $perms (['editor', 'moderator'])
     * удалит все разрешения этой роли и назначит $perms разрешения
     * если разрешения существуют
     */
    public function syncPermissions(array $perms)
    {
        $ids = [];
        foreach ($perms as $perm) {
            $ids[] = Permission::where(['name' => $perm])->first()->id;
        }
        return $this->permissions()->sync($ids);
    }

    /**
     * @param $role
     * @return bool
     * Существует ли вообще такая роль?
     */
    public static function hasRole($role)
    {
        return 0 < Capsule::table('roles')->where('name', '=', $role)->get()->count();
    }

    /**
     * @param $perm
     * У этой роли есть разрешение $perm ?
     */
    public function hasPermissionTo($perm)
    {
        return 0 < $this->permissions()->where('name', '=', $perm)->count();
    }


    public static function isSuperAdmin()
    {
// TODO: реализовать
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_has_permissions',
            'role_id',
            'permission_id'
        );
    }


    public function toArray()
    {
        parent::toArray();
    }

}





















