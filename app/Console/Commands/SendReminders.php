<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use Twilio\Rest\Client;

class SendReminders extends Command
{
    protected $signature = 'send:reminders';
    protected $description = 'Send reminders to patients';

    public function handle()
    {
        // Fetch patients who need reminders
        $currentTime = now()->format('H:i');
        $patients = Patient::where('reminder_time', 'like', $currentTime . '%')->get();

        // Send reminders
        foreach ($patients as $patient) {
            $this->sendSMS($patient->phone_number, 'It\'s time to take your medication.');
        }
    }
    private function sendSMS($to, $message)
    {
        try {
            $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));

            $twilio->messages->create(
                $to,
                ['from' => config('services.twilio.phone_number'), 'body' => $message]
            );
        } catch (\Exception $e) {
            $this->error('Twilio SMS sending failed: ' . $e->getMessage());
        }
    }
}
