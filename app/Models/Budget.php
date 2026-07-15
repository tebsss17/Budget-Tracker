<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount_limit',
        'month',
        'year'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // public function spent()
    // {
    //     return Transaction::where('user_id', $this->user_id)
    //             ->where('category_id', $this->category_id)
    //             ->where('type', 'Expense')
    //             ->whereMonth('transaction_date', $this->month)
    //             ->whereYear('transaction_date', $this->year)
    //             ->sum('amount');
    // }

    public function remaining()
    {
        return max ($this->amount_limit - $this->spent, 0);
    }

    public function progress()
    {
        if($this->amount_limit == 0){
            return 0;
        }
        return min($this->spent / $this->amount_limit * 100, 100);
    }

    public function isOverBudget()
    {
        return $this->spent > $this->amount_limit;
    }

    public function progressColor()
    {
        return match(true) {
            $this->progress() <= 50 => 'progress-success',
            $this->progress() <= 75 => 'progress-info',
            $this->progress() <= 90 => 'progress-warning',
            default => 'progress-error'
        };
    }

    public function textColor()
    {
        return match(true) {
            $this->progress() <= 50 => 'text-emerald-500',
            $this->progress() <= 75 => 'text-sky-500',
            $this->progress() <= 90 => 'text-amber-500',
            default => 'text-red-500'
        };
    }

    public function progressBarrColor()
    {
        return match(true) {
            $this->progress() <= 30 => 'bg-emerald-500',
            $this->progress() <= 50 => 'bg-lime-500',
            $this->progress() <= 75 => 'bg-amber-500',
            default => 'bg-red-500'
        };
    }
}
