<?php

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run account factory
        if (config('app.env') === 'local') {
            factory(Account::class, 10)->create();
        }
    }
}
