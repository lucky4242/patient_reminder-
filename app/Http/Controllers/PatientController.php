<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class PatientController extends Controller
{
    public function addPatientForm()
    {
        return view('add-patient');
    }

    public function addPatient(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'drug_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            // 'reminder_time' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create a new patient
        $patient = new Patient();
        $patient->name = $request->name;
        $patient->phone_number = $request->phone_number;
        $patient->email = $request->email;
        $patient->gender = $request->gender;
        $patient->drug_name = $request->drug_name; // Corrected typo
        $patient->dosage = $request->dosage;
        $patient->reminder_time = $request->reminder_time;
        $patient->save();

        return redirect()->back()->with('success', 'Patient added successfully.');
    }
    public function sendMessageToPatients()
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
            // Log or handle the exception appropriately
            // \Log::error('Twilio SMS sending failed: ' . $e->getMessage());
            $this->error('Twilio SMS sending failed: ' . $e->getMessage());
        }
    }
}
