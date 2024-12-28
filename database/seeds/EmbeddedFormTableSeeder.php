<?php

use App\Models\EmbeddedForm;
use Illuminate\Database\Seeder;

class EmbeddedFormTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run embedded form factory
        if (config('app.env') === 'local') {
            factory(EmbeddedForm::class, 10)->create();
        }
    }
}
