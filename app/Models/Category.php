<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'icon',
        'color',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function bgColor()
    {
        return match ($this->color) {
            'red' => 'bg-red-100 dark:bg-red-900/30',
            'orange' => 'bg-orange-100 dark:bg-orange-900/30',
            'amber' => 'bg-amber-100 dark:bg-amber-900/30',
            'yellow' => 'bg-yellow-100 dark:bg-yellow-900/30',
            'lime' => 'bg-lime-100 dark:bg-lime-900/30',
            'green' => 'bg-green-100 dark:bg-green-900/30',
            'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30',
            'teal' => 'bg-teal-100 dark:bg-teal-900/30',
            'cyan' => 'bg-cyan-100 dark:bg-cyan-900/30',
            'sky' => 'bg-sky-100 dark:bg-sky-900/30',
            'blue' => 'bg-blue-100 dark:bg-blue-900/30',
            'indigo' => 'bg-indigo-100 dark:bg-indigo-900/30',
            'violet' => 'bg-violet-100 dark:bg-violet-900/30',
            'purple' => 'bg-purple-100 dark:bg-purple-900/30',
            'pink' => 'bg-pink-100 dark:bg-pink-900/30',
            default => 'bg-zinc-100 dark:bg-zinc-900/30',
        };

    }

    public function textColor()
    {
        return match ($this->color) {
            'red' => 'text-red-600',
            'orange' => 'text-orange-600',
            'amber' => 'text-amber-600',
            'yellow' => 'text-yellow-600',
            'lime' => 'text-lime-600',
            'green' => 'text-green-600',
            'emerald' => 'text-emerald-600',
            'teal' => 'text-teal-600',
            'cyan' => 'text-cyan-600',
            'sky' => 'text-sky-600',
            'blue' => 'text-blue-600',
            'indigo' => 'text-indigo-600',
            'violet' => 'text-violet-600',
            'purple' => 'text-purple-600',
            'pink' => 'text-pink-600',
            default => 'text-zinc-600',
        };
    }
}
