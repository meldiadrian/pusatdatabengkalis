<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'unit_kerja_id',
        'role',
        'created_by',
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
        ];
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    /**
     * Generate username unik otomatis dari nama depan user.
     * Format: nama_depan (lowercase, tanpa spasi/simbol) + angka jika duplikat.
     * Contoh: "Ahmad Fauzi" → "ahmad", jika sudah ada → "ahmad1", "ahmad2", dst.
     */
    public static function generateUsername(string $name, int $excludeId = null): string
    {
        $base    = preg_replace('/[^a-z0-9]/', '', strtolower(explode(' ', trim($name))[0]));
        $base    = $base ?: 'user';
        $username = $base;
        $counter  = 1;

        while (
            static::where('username', $username)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isOPD(): bool
    {
        return $this->role === 'OPD';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usersCreated()
    {
        return $this->hasMany(User::class, 'created_by');
    }
}
