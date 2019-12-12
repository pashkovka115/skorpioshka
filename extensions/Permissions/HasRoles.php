<?php


namespace Extensions\Permissions;


use Core\System\Traits\Singleton;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

/**
 * Trait HasRoles
 * @package Extensions\Permissions
 * 1 Помошники https://laravel.com/docs/5.8/helpers
 * 2 Коллекции https://softroot.ru/12-polieznykh-mietodov-v-kolliektsiiakh-laravel/
 * 3 Коллекции https://softroot.ru/tryuki-s-kollekciyami-v-laravel/
 * 4 Коллекции https://laravel.com/docs/5.8/eloquent-collections
 * 5 Коллекции https://laravel.com/docs/5.8/collections
 */
trait HasRoles
{
    use Singleton;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
///////////////////////////////////////////// PERMISSIONS //////////////////////////////////////////////



    /**
     * @param string $role
     * @return bool
     * У этого пользователя есть разрешение $permission ?
     * или этому пользователю можно $permission ?
     */
    public function can(string $permission)
    {
        return self::permissions()->contains('name', $permission);
    }

    /**
     * @param mixed ...$permissions
     * @return bool
     * Есть у пользователя какое либо из разрешений $permissions
     */
    public function hasAnyPermission(...$permissions)
    {
        if (is_array($permissions[0])) {
            $permissions = $permissions[0];
        }
        $perms = self::permissions();

        foreach ($permissions as $permission) {
            if ($perms->contains('name', $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed ...$permissions
     * @return bool
     * У пользователя есть все $permissions разрешения
     */
    public function hasAllPermissions(...$permissions)
    {
        if (is_array($permissions[0])) {
            $permissions = $permissions[0];
        }
        $perms = self::permissions();

        foreach ($permissions as $permission) {
            if (!$perms->contains('name', $permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $role
     * @return bool
     * Все разрешения этого пользователя
     * тоже самое, что и $this->permissions()
     */
    public function getAllPermissions()
    {
        return self::permissions();
    }

    /**
     * @return \Illuminate\Support\Collection
     * получить список всех разрешений, непосредственно назначенных пользователю
     */
    public function getPermissionNames()
    {
        return self::permissions()->pluck('name');
    }


    /**
     * @return \Illuminate\Support\Collection
     * Разрешения этого пользователя
     */
    public function permissions()
    {
        $perms = [];
        $roles = self::roles()->get();
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $perms[] = $permission;
            }
        }
        return collect($perms);
    }
//////////////////////////////////// ROLES ///////////////////////////////////////


    // получить имена ролей пользователя
    public function getRoleNames()
    {
        return self::roles()->get()->pluck('name');
    }

    /**
     * @param $roles
     * @return bool
     * Есть роль $role у этого пользователя?
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if (is_int($roles)) {
            return $this->roles->contains('id', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }

    /**
     * @param $roles
     * @return bool
     * есть ли у пользователя какой-либо из заданных списков ролей
     */
    public function hasAnyRole($roles): bool
    {
        return $this->hasRole($roles);
    }

    /**
     * @param $roles
     * @return bool
     * У этого пользователя есть все роли $roles!
     */
    public function hasAllRoles($roles)
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        $roles = collect()->make($roles)->map(function ($role) {
            return $role instanceof Role ? $role->name : $role;
        });

        return $roles->intersect($this->getRoleNames()) == $roles;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     * Все роли этого пользователя
     */
    public function getAllRoles()
    {
        return self::roles()->get();
    }

    /**
     * @param string $role
     * @return array
     * Дать роль $role этому пользователю
     */
    public function giveRole(string $role)
    {
        $id = Role::where(['name' => $role])->first();
        if (isset($id->id)) {
            return $this->roles()->syncWithoutDetaching([$id->id]);
        }

    }

    /**
     * @param $roles string|Collection
     * @return array
     * Добавить роли $roles этому пользователю
     */
    public function giveRoles($roles)
    {
        if (is_string($roles)) {
            $id = Role::where(['name' => $roles])->first();
            if (isset($id->id)) {
                return $this->roles()->syncWithoutDetaching([$id->id]);
            }
        }
        if ($roles instanceof Collection) {
            $arr = [];
            foreach ($roles as $role) {
                $arr[] = $role->name;
            }
            $roles = $arr;
        }
        $arr = [];
        foreach ($roles as $role) {
            $id = Role::where(['name' => $role])->first();
            // если нашёлся объект
            if (isset($id->id)) {
                $arr[] = $this->roles()->syncWithoutDetaching([$id->id]);
            }
        }
        return $arr;
    }

    /**
     * @param string $role
     * @return int
     * Отзывает роль $role у пользователя
     */
    public function revokeRole(string $role)
    {
        $id = Role::where(['name' => $role])->first();
        return $this->roles()->detach($id);
    }

    /**
     * @param $roles
     * @return array|int
     * Отзывает роли у пользователя
     */
    public function revokeRoles($roles)
    {
        if (is_string($roles)){
            return $this->revokeRole($roles);
        }
        if ($roles instanceof Collection) {
            $arr = [];
            foreach ($roles as $role) {
                $arr[] = $role->name;
            }
            $roles = $arr;
        }
        $arr = [];
        foreach ($roles as $role){
            $arr[] = $this->revokeRole($role);
        }
        return $arr;
    }

    /**
     * @param $roles
     * @return array
     * Синхронизирует роли $roles
     * Удаляет все и присваивает $roles
     */
    public function syncRoles($roles)
    {
        if (is_string($roles)){
            $id = Role::where(['name' => $roles])->first()->id;
            return $this->roles()->sync($id);
        }

        if ($roles instanceof Collection) {
            $arr = [];
            foreach ($roles as $role) {
                $arr[] = $role->name;
            }
            $roles = $arr;
        }

        $ids = [];
        foreach ($roles as $role) {
            $ids[] = Role::where(['name' => $role])->first()->id;
        }
        return $this->roles()->sync($ids);
    }


    /**
     * @return BelongsToMany
     * Роли этого пользователя
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'user_has_roles',
            'user_id',
            'role_id'
        );
    }

    public function toArray()
    {
        return parent::toArray();
    }

    protected function convertPipeToArray(string $pipeString)
    {
        $pipeString = trim($pipeString);

        if (strlen($pipeString) <= 2) {
            return $pipeString;
        }

        $quoteCharacter = substr($pipeString, 0, 1);
        $endCharacter = substr($quoteCharacter, -1, 1);

        if ($quoteCharacter !== $endCharacter) {
            return explode('|', $pipeString);
        }

        if (!in_array($quoteCharacter, ["'", '"'])) {
            return explode('|', $pipeString);
        }

        return explode('|', trim($pipeString, $quoteCharacter));
    }
}