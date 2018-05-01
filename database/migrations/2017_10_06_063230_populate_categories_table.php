<?php

use Illuminate\Database\Migrations\Migration;

class PopulateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('categories')->insert([
            ['name' => 'DJ', 'color' => '#6DA8F2'],
            ['name' => 'Service', 'color' => '#FEDF63'],
            ['name' => 'Koch / Beikoch', 'color' => '#50D8A3'],
            ['name' => 'Künstler', 'color' => '#D2D26D'],
            ['name' => 'Küchenhilfe / Spüler', 'color' => '#F26D7D'],
            ['name' => 'Barkeeper / Barister', 'color' => '#FF7F50'],
            ['name' => 'Runner', 'color' => '#8B0000'],
            ['name' => 'Helfer (Kasse, Gaderobe, Auf- und Abbau, Reinigung)', 'color' => '#FAB500'],
            ['name' => 'Security', 'color' => '#173E7D'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('categories')->delete();
    }
}
