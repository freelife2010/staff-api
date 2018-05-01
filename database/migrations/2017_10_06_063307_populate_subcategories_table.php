<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PopulateSubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('subcategories')->insert([
            ['category_id' => 1, 'name' => 'Equipment', 'type' => 0],
            ['category_id' => 1, 'name' => 'Musikrichtung', 'type' => 1],
            ['category_id' => 2, 'name' => 'Geldbeutel wird gestellt?', 'type' => 0],
            ['category_id' => 2, 'name' => 'Drei Teller tragen?', 'type' => 0],
            ['category_id' => 2, 'name' => 'Dienstkleidung wird gestellt?', 'type' => 0],
            ['category_id' => 2, 'name' => 'Gewünschter Kleidungsstil', 'type' => 1],
            ['category_id' => 2, 'name' => 'Gesundheitszeugnis benötigt', 'type' => 0],
            ['category_id' => 2, 'name' => 'Tablett / Schlitten Erfahrung notwendig', 'type' => 0],
            ['category_id' => 2, 'name' => 'Benötigte Sprachen', 'type' => 1],
            ['category_id' => 3, 'name' => 'Ausrichtung', 'type' => 1],
            ['category_id' => 3, 'name' => 'Benötigte Sprachen', 'type' => 1],
            ['category_id' => 4, 'name' => 'Bereich', 'type' => 1],
            ['category_id' => 5, 'name' => 'Bereich', 'type' => 1],
            ['category_id' => 5, 'name' => 'Benötigte Sprachen', 'type' => 1],
            ['category_id' => 6, 'name' => 'Erfahrungen mit Cocktails', 'type' => 0],
            ['category_id' => 6, 'name' => 'Erfahrung mit Siebträger', 'type' => 0],
            ['category_id' => 6, 'name' => 'Umgang mit Zapfanlage', 'type' => 0],
            ['category_id' => 6, 'name' => 'Dienstkleidung wird gestellt?', 'type' => 0],
            ['category_id' => 6, 'name' => 'Gewünschter Kleidungsstil', 'type' => 1],
            ['category_id' => 6, 'name' => 'Geldbeutel wird gestellt?', 'type' => 0],
            ['category_id' => 6, 'name' => 'Benötigte Sprachen', 'type' => 1],
            ['category_id' => 6, 'name' => 'Gesundheitszeugnis benötigt', 'type' => 0],
            ['category_id' => 7, 'name' => 'Bereich', 'type' => 1],
            ['category_id' => 7, 'name' => 'Benötigte Sprachen', 'type' => 1],
            ['category_id' => 8, 'name' => 'Benötigte', 'type' => 1],
            ['category_id' => 8, 'name' => 'Benötigte Sprachen', 'type' => 1],
            ['category_id' => 9, 'name' => 'Bereich', 'type' => 1],
            ['category_id' => 9, 'name' => 'Benötigte Sprachen', 'type' => 1],
            ['category_id' => 9, 'name' => '34a vorhanden?', 'type' => 0],
            ['category_id' => 9, 'name' => 'Dienstkleidung wird gestellt?', 'type' => 0],
            ['category_id' => 9, 'name' => 'Gewünschter Kleidungsstil', 'type' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('subcategories')->delete();
    }
}
