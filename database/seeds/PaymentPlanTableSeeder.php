<?php

use App\Models\PaymentPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PaymentPlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create new payment plan
        DB::table('payment_plans')->insert([
            [
                'name' => 'starter',
                'level' => 1,
                'amount' => 10000,
                'discount' => 1000,
                'currency' => 'NGN',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'basic',
                'level' => 2,
                'amount' => 20000,
                'discount' => 1000,
                'currency' => 'NGN',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'premium',
                'level' => 3,
                'amount' => 30000,
                'discount' => 1000,
                'currency' => 'NGN',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ]);

        // Run payment plan factory
        if (config('app.env') === 'local') {
            // factory(PaymentPlan::class, 10)->create();
        }
    }
}
