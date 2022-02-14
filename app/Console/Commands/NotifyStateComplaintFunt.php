<?php

namespace App\Console\Commands;

use App\Http\Controllers\ComplaintController;
use App\Mail\NotifyStateFunt;
use App\Models\Complaint;
use App\Models\StateComplaint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyStateComplaintFunt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:notifystatecomplaintyfunt';

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
        $complaints = Complaint::withCount('ResponseComplaint')->get();

        foreach ($complaints as $complaint) {

            if ($complaint->id_user_asigne && $complaint->response_complaint_count <= 2) {

                foreach ($complaint->ResponseComplaint as $value) {

                    /*  echo Carbon::parse($value->created_at) . "<br/>"; */
                    /*  echo Carbon::now()->addHour(24) . "<br/>"; */

                    if (Carbon::parse($value->created_at) <= Carbon::now()->addHour(24) && $value->id_state_complaint > 1) {
                        //return $value;
                        $user = User::find($complaint->id_user_asigne);
                        $state = StateComplaint::find($complaint->id_state);
                        $msg = [
                            "name" => $user->name . " " . $user->last_name,
                            "cod" => $complaint->cod,
                            "state" => $state->name
                        ];
                        Mail::to($user->email)->send(new NotifyStateFunt($msg));
                    }
                }
            }
        }
    }
}