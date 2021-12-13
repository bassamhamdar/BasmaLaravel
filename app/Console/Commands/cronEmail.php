<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class cronEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email with total number of new registrations';

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
    { $number = Customer::where('created_at',">=",Carbon::now()
        ->subHour(24))->where('created_at',"<",Carbon::now())->count();
        Mail::send("Number of new regitrations is ",$number, function ($mail){
            $mail->from('hamdar.m.bassam@gmail.com');
            $mail->to('hamdar.m.bassam@gmail.com')
                ->subject('Daily New Registrations!');
        });
        return "success!";
    }
}
