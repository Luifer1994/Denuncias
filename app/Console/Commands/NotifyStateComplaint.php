<?php

namespace App\Console\Commands;

use App\Http\Controllers\ComplaintController;
use App\Mail\NotifyStateAdmin;
use App\Models\Complaint;
use App\Models\StateComplaint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyStateComplaint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:notifystatecomplainty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $complaints = Complaint::where('id_user_asigne', null)->get();
        foreach ($complaints as $complaint) {
            $userAdmin = User::where('id_rol', 1)->get();

            if (Carbon::parse($complaint->created_at) >= Carbon::now()->addHour(12)) {
                $state = StateComplaint::find($complaint->id_state);
                foreach ($userAdmin as $user) {
                    $msg = [
                        "name" => $user->name . " " . $user->last_name,
                        "cod" => $complaint->cod,
                        "state" => $state->name
                    ];

                    Mail::to($user->email)->send(new NotifyStateAdmin($msg));
                }
            }
        }
    }
}