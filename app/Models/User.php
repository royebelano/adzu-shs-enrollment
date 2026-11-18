<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Support\Access;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /* ---------------------------------------------------------------
     | Roles and access
     * --------------------------------------------------------------- */

    /** Accounts made before roles existed have no role and count as Administrator. */
    public function roleKey(): string
    {
        return $this->role ?: Access::ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->roleKey() === Access::ADMIN;
    }

    public function roleLabel(): string
    {
        return Access::roles()[$this->roleKey()] ?? ucfirst($this->roleKey());
    }

    /** none, view or edit for a module. Disabled accounts get none. */
    public function accessLevel(string $module): string
    {
        if ($this->is_active === false) {
            return 'none';
        }

        return Access::level($this->roleKey(), $module);
    }

    public function canView(string $module): bool
    {
        return in_array($this->accessLevel($module), ['view', 'edit'], true);
    }

    public function canEdit(string $module): bool
    {
        return $this->accessLevel($module) === 'edit';
    }

    /** First page this person is allowed to open. */
    public function homeUrl(): string
    {
        foreach (Access::LANDING as $module => $routeName) {
            if ($this->canView($module)) {
                return route($routeName);
            }
        }

        return route('admin.profile');
    }
}
