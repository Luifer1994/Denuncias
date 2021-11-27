<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rol;
use App\Models\Infringement;
use App\Models\ComplaintType;
use App\Models\ComplaintTypeInfringement;
use App\Models\StateComplaint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $roles = ["Admin", "Denunciante", "Abogado"];
        foreach ($roles as $rol) {
            DB::table('rols')->insert([
                "name" => $rol,
                "state" => 1
            ]);
        }
        $professions = ["Admin","Denunciante", "Técnico", "Abogado"];
        foreach ($professions as $value) {
            DB::table('professions')->insert([
                "name" => $value,
                "state" => 1
            ]);
        }
        $complaintType = ["Tala de arboles", "Aguas residuales", "Extracción de arena"];
        foreach ($complaintType as $value) {
            DB::table('complaint_types')->insert([
                "name" => $value,
                "state" => 1
            ]);
        }

        $complaintState = ["Enviada", "En proceso", "En revisión", "Cerrada"];
        foreach ($complaintState as $value) {
            DB::table('state_complaints')->insert([
                "name" => $value,
                "state" => 1
            ]);
        }
        User::factory(1)->create();
        Infringement::factory(1)->create();
        ComplaintTypeInfringement::factory(1)->create();
    }
}
