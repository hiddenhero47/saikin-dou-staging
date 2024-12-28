<?php

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run payment factory
        if (config('app.env') === 'local') {
            factory(Payment::class, 10)->create();
        }
    }
}
