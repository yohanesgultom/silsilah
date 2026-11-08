<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Couple extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'husband_id',
        'wife_id',
        'marriage_date',
        'divorce_date',
        'manager_id',
    ];

    protected $casts = [
        'marriage_date' => 'date',
        'divorce_date' => 'date',
    ];

    public function childs()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function husband()
    {
        return $this->belongsTo(User::class, 'husband_id');
    }

    public function wife()
    {
        return $this->belongsTo(User::class, 'wife_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function addChild(User $user)
    {
        $user->parent_id = $this->id;
        $user->father_id = $this->husband_id;
        $user->mother_id = $this->wife_id;
        $user->manager_id = $this->manager_id;
        $user->save();

        return $user;
    }
}