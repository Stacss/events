<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array<string>
     */
    protected $fillable = [
        'login',
        'email',
        'password',
        'first_name',
        'last_name',
        'date_of_birth',
    ];

    /**
     * Атрибуты, которые скрыты при сериализации.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Атрибуты, которые приводятся к определенному типу данных.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Accessor для получения отформатированной даты рождения.
     *
     * @param  mixed  $value
     * @return string|null
     */
    public function getDateOfBirthAttribute($value) {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null; // Форматируем дату при обращении к атрибуту
    }

    /**
     * Mutator для установки значения атрибута даты рождения.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setDateOfBirthAttribute($value) {
        $this->attributes['date_of_birth'] = $value ? Carbon::parse($value) : null; // Устанавливаем значение атрибута в формате Carbon
    }
}
