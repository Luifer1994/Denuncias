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

        $roles = ["Admin", "Denunciante", "Funcionario"];
        foreach ($roles as $rol) {
            DB::table('rols')->insert([
                "name" => $rol,
                "state" => 1
            ]);
        }
        $professions = ["Admin", "Ténico", "Abogado", "Secretaria"];
        foreach ($professions as $value) {
            DB::table('professions')->insert([
                "name" => $value,
                "state" => 1
            ]);
        }
        $complaintType = ["Tala de árboles", "Aguas residuales", "Extracción de arena", "Descapote", "Ocupación de borde costero"];
        foreach ($complaintType as $value) {
            DB::table('complaint_types')->insert([
                "name" => $value,
                "state" => 1
            ]);
        }

        $complaintState = ["INICIADA", "EN PROCESO", "INDAGACIÓN", "NOTIFICACIÓN", "RETRASADA", "FINALIZADA", "CANCELADA"];
        foreach ($complaintState as $value) {
            DB::table('state_complaints')->insert([
                "name" => $value,
                "state" => 1
            ]);
        }
        $typesDocuments = ["Cédula de ciudadanía", "Cédula de extranjería", "Nit"];
        foreach ($typesDocuments as $type) {
            DB::table('type_documents')->insert([
                "name" => $type
            ]);
        }

        $typesPeople = ["Natural", "Jurídica"];
        foreach ($typesPeople as $type) {
            DB::table('type_people')->insert([
                "name" => $type
            ]);
        }
        User::factory(1)->create();
        Infringement::factory(1)->create();
        ComplaintTypeInfringement::factory(1)->create();
    }
}