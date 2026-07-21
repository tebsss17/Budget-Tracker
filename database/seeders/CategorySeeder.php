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
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'utensils-crossed',
                'color' => 'orange'
            ],
            [
                'name' => 'Transportation',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'car',
                'color' => 'blue'
            ],
            [
                'name' => 'Bills',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'receipt-text',
                'color' => 'red'
            ],
            [
                'name' => 'Shopping',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'shopping-cart',
                'color' => 'pink'
            ],
            [
                'name' => 'Rent',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'house',
                'color' => 'amber'
            ],
            [
                'name' => 'Utilities',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'lightbulb',
                'color' => 'yellow'
            ],
            [
                'name' => 'Health',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'heart-pulse',
                'color' => 'emerald'
            ],
            [
                'name' => 'Education',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'school',
                'color' => 'indigo'
            ],
            [
                'name' => 'Entertainment',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'clapperboard',
                'color' => 'purple'
            ],
            [
                'name' => 'Travel',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'plane',
                'color' => 'sky'
            ],
            [
                'name' => 'Fuel',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'fuel',
                'color' => 'slate'
            ],
             [
                'name' => 'Clothing',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'shirt',
                'color' => 'rose'
            ],
            [
                'name' => 'Pets',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'paw-print',
                'color' => 'teal'
            ],
            [
                'name' => 'Insurance',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'shield-check',
                'color' => 'cyan'
            ],
            [
                'name' => 'Medicine',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'pill',
                'color' => 'lime'
            ],
            [
                'name' => 'Others',
                'user_id' => null,
                'type' => 'Expense',
                'icon' => 'circle-question-mark',
                'color' => 'gray'
            ],


            // Income
            [
                'name' => 'Salary',
                'user_id' => null,
                'type' => 'Income',
                'icon' => 'banknote-check',
                'color' => 'green'
            ],
             [
                'name' => 'Business',
                'user_id' => null,
                'type' => 'Income',
                'icon' => 'store',
                'color' => 'orange'
            ],
            [
                'name' => 'Freelance',
                'user_id' => null,
                'type' => 'Income',
                'icon' => 'briefcase-business',
                'color' => 'violet'
            ],
            [
                'name' => 'Investment',
                'user_id' => null,
                'type' => 'Income',
                'icon' => 'chart-spline',
                'color' => 'blue'
            ],
            [
                'name' => 'Other Income',
                'user_id' => null,
                'type' => 'Income',
                'icon' => 'wallet-cards',
                'color' => 'zinc'
            ],

        ]);
    }
}
