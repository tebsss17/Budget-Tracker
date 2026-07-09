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
                'color' => 'orange'
            ],
            [
                'name' => 'Transportation',
                'type' => 'Expense',
                'icon' => 'car',
                'color' => 'blue'
            ],
            [
                'name' => 'Shopping',
                'type' => 'Expense',
                'icon' => 'shopping-cart',
                'color' => 'pink'
            ],
            [
                'name' => 'Bills',
                'type' => 'Expense',
                'icon' => 'receipt-text',
                'color' => 'red'
            ],
            [
                'name' => 'Rent',
                'type' => 'Expense',
                'icon' => 'house',
                'color' => 'amber'
            ],
            [
                'name' => 'Utilities',
                'type' => 'Expense',
                'icon' => 'lightbulb',
                'color' => 'yellow'
            ],
            [
                'name' => 'Health',
                'type' => 'Expense',
                'icon' => 'heart-pulse',
                'color' => 'emerald'
            ],
            [
                'name' => 'Medicine',
                'type' => 'Expense',
                'icon' => 'pill',
                'color' => 'lime'
            ],
            [
                'name' => 'Education',
                'type' => 'Expense',
                'icon' => 'school',
                'color' => 'indigo'
            ],
            [
                'name' => 'Entertainment',
                'type' => 'Expense',
                'icon' => 'clapperboard',
                'color' => 'purple'
            ],
            [
                'name' => 'Travel',
                'type' => 'Expense',
                'icon' => 'plane',
                'color' => 'sky'
            ],
            [
                'name' => 'Fuel',
                'type' => 'Expense',
                'icon' => 'fuel',
                'color' => 'slate'
            ],
            [
                'name' => 'Pets',
                'type' => 'Expense',
                'icon' => 'paw-print',
                'color' => 'teal'
            ],
            [
                'name' => 'Insurance',
                'type' => 'Expense',
                'icon' => 'shield-check',
                'color' => 'cyan'
            ],
            [
                'name' => 'Clothing',
                'type' => 'Expense',
                'icon' => 'shirt',
                'color' => 'rose'
            ],
            [
                'name' => 'Miscellaneous',
                'type' => 'Expense',
                'icon' => 'circle-question-mark',
                'color' => 'gray'
            ],
            [
                'name' => 'Savings',
                'type' => 'Expense',
                'icon' => 'piggy-bank',
                'color' => 'emerald'
            ],

            // Income
            [
                'name' => 'Salary',
                'type' => 'Income',
                'icon' => 'banknote-check',
                'color' => 'green'
            ],
            [
                'name' => 'Freelance',
                'type' => 'Income',
                'icon' => 'briefcase-business',
                'color' => 'violet'
            ],
            [
                'name' => 'Business',
                'type' => 'Income',
                'icon' => 'store',
                'color' => 'orange'
            ],
            [
                'name' => 'Investments',
                'type' => 'Income',
                'icon' => 'chart-spline',
                'color' => 'blue'
            ],
            [
                'name' => 'Other Income',
                'type' => 'Income',
                'icon' => 'wallet-cards',
                'color' => 'zinc'
            ],

        ]);
    }
}
