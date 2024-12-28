<?php

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run contact factory
        if (config('app.env') === 'local') {
            factory(Contact::class, 10)->create();
        }
    }
}
