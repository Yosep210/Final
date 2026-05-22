<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

// class RolePermission extends Model
// {
//     protected $table = 'role_has_permissions';

//     public $timestamps = false;

//     protected $fillable = [
//         'role_id',
//         'permission_id',
//     ];

//     protected $casts = [
//         'role_id' => 'integer',
//         'permission_id' => 'integer',
//     ];

//     protected $primaryKey = null;

//     public $incrementing = false;

//     protected function setKeysForSaveQuery($query)
//     {
//         $query->where('role_id', $this->getOriginal('role_id') ?? $this->role_id)
//             ->where('permission_id', $this->getOriginal('permission_id') ?? $this->permission_id);

//         return $query;
//     }

//     public function role(): BelongsTo
//     {
//         return $this->belongsTo(Role::class);
//     }

//     public function permission(): BelongsTo
//     {
//         return $this->belongsTo(Permission::class);
//     }

//     public function delete(): ?bool
//     {
//         if (! $this->exists) {
//             return null;
//         }

//         $deleted = static::query()
//             ->where('role_id', $this->role_id)
//             ->where('permission_id', $this->permission_id)
//             ->delete();

//         $this->exists = false;

//         return $deleted > 0;
//     }
// }
