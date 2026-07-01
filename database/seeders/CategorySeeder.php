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
            ],
            [
                'name' => 'Transportation',
                'type' => 'Expense',
                'icon' => 'car',
            ],
            [
                'name' => 'Shopping',
                'type' => 'Expense',
                'icon' => 'shopping-cart',
            ],
            [
                'name' => 'Bills',
                'type' => 'Expense',
                'icon' => 'receipt-text',
            ],
            [
                'name' => 'Rent',
                'type' => 'Expense',
                'icon' => 'house',
            ],
            [
                'name' => 'Utilities',
                'type' => 'Expense',
                'icon' => 'lightbulb',
            ],
            [
                'name' => 'Health',
                'type' => 'Expense',
                'icon' => 'heart-pulse',
            ],
            [
                'name' => 'Medicine',
                'type' => 'Expense',
                'icon' => 'pill',
            ],
            [
                'name' => 'Education',
                'type' => 'Expense',
                'icon' => 'school',
            ],
            [
                'name' => 'Entertainment',
                'type' => 'Expense',
                'icon' => 'clapperboard',
            ],
            [
                'name' => 'Travel',
                'type' => 'Expense',
                'icon' => 'plane',
            ],
            [
                'name' => 'Fuel',
                'type' => 'Expense',
                'icon' => 'fuel',
            ],
            [
                'name' => 'Pets',
                'type' => 'Expense',
                'icon' => 'paw-print',
            ],
            [
                'name' => 'Insurance',
                'type' => 'Expense',
                'icon' => 'shield-check',
            ],
            [
                'name' => 'Clothing',
                'type' => 'Expense',
                'icon' => 'shirt',
            ],
            [
                'name' => 'Miscellaneous',
                'type' => 'Expense',
                'icon' => 'circle-question-mark',
            ],
            [
                'name' => 'Savings',
                'type' => 'Expense',
                'icon' => 'piggy-bank',
            ],

            // Income
            [
                'name' => 'Salary',
                'type' => 'Income',
                'icon' => 'banknote-check',
            ],
            [
                'name' => 'Freelance',
                'type' => 'Income',
                'icon' => 'briefcase-business',
            ],
            [
                'name' => 'Business',
                'type' => 'Income',
                'icon' => 'store',
            ],
            [
                'name' => 'Investments',
                'type' => 'Income',
                'icon' => 'chart-spline',
            ],
            [
                'name' => 'Other Income',
                'type' => 'Income',
                'icon' => 'wallet-cards',
            ],

        ]);
    }
}
