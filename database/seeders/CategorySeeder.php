<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            // Expense
            [
                'name' => 'Food',
                'type' => 'Expense',
                'icon' => 'utensils-crossed',
                'bg_color' => 'bg-orange-100 dark:bg-orange-900/30',
                'text_color' => 'text-orange-600'
            ],
            [
                'name' => 'Transportation',
                'type' => 'Expense',
                'icon' => 'car',
                'bg_color' => 'bg-blue-100 dark:bg-blue-900/30',
                'text_color' => 'text-blue-600'
            ],
            [
                'name' => 'Shopping',
                'type' => 'Expense',
                'icon' => 'shopping-cart',
                'bg_color' => 'bg-pink-100 dark:bg-pink-900/30',
                'text_color' => 'text-pink-600'
            ],
            [
                'name' => 'Bills',
                'type' => 'Expense',
                'icon' => 'receipt-text',
                'bg_color' => 'bg-red-100 dark:bg-red-900/30',
                'text_color' => 'text-red-600'
            ],
            [
                'name' => 'Rent',
                'type' => 'Expense',
                'icon' => 'house',
                'bg_color' => 'bg-amber-100 dark:bg-amber-900/30',
                'text_color' => 'text-amber-600'
            ],
            [
                'name' => 'Utilities',
                'type' => 'Expense',
                'icon' => 'lightbulb',
                'bg_color' => 'bg-yellow-100 dark:bg-yellow-900/30',
                'text_color' => 'text-yellow-600'
            ],
            [
                'name' => 'Health',
                'type' => 'Expense',
                'icon' => 'heart-pulse',
                'bg_color' => 'bg-emerald-100 dark:bg-emerald-900/30',
                'text_color' => 'text-emerald-600'
            ],
            [
                'name' => 'Medicine',
                'type' => 'Expense',
                'icon' => 'pill',
                'bg_color' => 'bg-lime-100 dark:bg-lime-900/30',
                'text_color' => 'text-lime-600'
            ],
            [
                'name' => 'Education',
                'type' => 'Expense',
                'icon' => 'school',
                'bg_color' => 'bg-indigo-100 dark:bg-indigo-900/30',
                'text_color' => 'text-indigo-600'
            ],
            [
                'name' => 'Entertainment',
                'type' => 'Expense',
                'icon' => 'clapperboard',
                'bg_color' => 'bg-purple-100 dark:bg-purple-900/30',
                'text_color' => 'text-purple-600'
            ],
            [
                'name' => 'Travel',
                'type' => 'Expense',
                'icon' => 'plane',
                'bg_color' => 'bg-sky-100 dark:bg-sky-900/30',
                'text_color' => 'text-sky-600'
            ],
            [
                'name' => 'Fuel',
                'type' => 'Expense',
                'icon' => 'fuel',
                'bg_color' => 'bg-slate-100 dark:bg-slate-900/30',
                'text_color' => 'text-slate-600'
            ],
            [
                'name' => 'Pets',
                'type' => 'Expense',
                'icon' => 'paw-print',
                'bg_color' => 'bg-teal-100 dark:bg-teal-900/30',
                'text_color' => 'text-teal-600'
            ],
            [
                'name' => 'Insurance',
                'type' => 'Expense',
                'icon' => 'shield-check',
                'bg_color' => 'bg-cyan-100 dark:bg-cyan-900/30',
                'text_color' => 'text-cyan-600'
            ],
            [
                'name' => 'Clothing',
                'type' => 'Expense',
                'icon' => 'shirt',
                'bg_color' => 'bg-rose-100 dark:bg-rose-900/30',
                'text_color' => 'text-rose-600'
            ],
            [
                'name' => 'Miscellaneous',
                'type' => 'Expense',
                'icon' => 'circle-question-mark',
                'bg_color' => 'bg-gray-100 dark:bg-gray-900/30',
                'text_color' => 'text-gray-600'
            ],
            [
                'name' => 'Savings',
                'type' => 'Expense',
                'icon' => 'piggy-bank',
                'bg_color' => 'bg-emerald-100 dark:bg-emerald-900/30',
                'text_color' => 'text-emerald-600'
            ],

            // Income
            [
                'name' => 'Salary',
                'type' => 'Income',
                'icon' => 'banknote-check',
                'bg_color' => 'bg-green-100 dark:bg-green-900/30',
                'text_color' => 'text-green-600'
            ],
            [
                'name' => 'Freelance',
                'type' => 'Income',
                'icon' => 'briefcase-business',
                'bg_color' => 'bg-violet-100 dark:bg-violet-900/30',
                'text_color' => 'text-violet-600'
            ],
            [
                'name' => 'Business',
                'type' => 'Income',
                'icon' => 'store',
                'bg_color' => 'bg-orange-100 dark:bg-orange-900/30',
                'text_color' => 'text-orange-600'
            ],
            [
                'name' => 'Investments',
                'type' => 'Income',
                'icon' => 'chart-spline',
                'bg_color' => 'bg-blue-100 dark:bg-amber-blue/30',
                'text_color' => 'text-blue-600'
            ],
            [
                'name' => 'Other Income',
                'type' => 'Income',
                'icon' => 'wallet-cards',
                'bg_color' => 'bg-zinc-100 dark:bg-zinc-900/30',
                'text_color' => 'text-zinc-600'
            ],

        ]);
    }
}
