<?php

namespace App\Console;



use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;



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
        Commands\GetMailgunLogs::class,
        Commands\CampaignCommand::class,
       // Commands\MailBlast::class,
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
         $schedule->command('poststay')->dailyAt('15:00');
         $schedule->command('birthdaymail')->dailyAt('11:59');
         $schedule->command('campaign')->everyMinute();
         $schedule->command('missyou')->dailyAt('11:59');
//         $schedule->command('tripadvisor')->dailyAt('11:00');
//         $schedule->command('booking')->dailyAt('11:45');
//         $schedule->command('hotels')->dailyAt('11:30');
         $schedule->command('syncemailresponse')->everyFiveMinutes();
         $schedule->command('getmailgunlogs')->everyFiveMinutes();
        // $schedule->command('campaign')>everyFiveMinutes();
        // $schedule->command('mailblast')->everyTenMinutes();
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
