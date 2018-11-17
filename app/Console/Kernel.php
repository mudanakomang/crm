<?php

namespace App\Console;


use App\Birthday;
use App\Contact;
use App\Http\Controllers\EmailTemplateController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;
use App\PostStay;
use App\MailEditor;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\PostStayCommand::class,
        Commands\BirthDayCommand::class,
        Commands\TripadvisorSync::class,
        Commands\BookingSync::class,
        Commands\HotelSync::class,
        Commands\SyncEmailResponse::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
       /*
        * POST STAY & BIRTHDAY EMAIL Schedule
        */
      // $schedule->call(function (){
          //POSTSTAY Email

      // })->daily();

//       $schedule->command('poststay')->dailyAt('11:59');
//       $schedule->command('birthdaymail')->dailyAt('11:59');
//       $schedule->command('campaign')->everyMinute();
//       $schedule->command('missyou')->dailyAt('11:59');
//       $schedule->command('tripadvisor');



    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
