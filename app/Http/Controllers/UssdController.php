<?php

namespace App\Http\Controllers;

use App\Models\UssdSession;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Attendance;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UssdController extends Controller
{
    protected $serviceCode = '*123#';

    /**
     * Handle USSD requests
     */
    public function handle(Request $request)
    {
        $sessionId = $request->input('sessionId');
        $phoneNumber = $request->input('phoneNumber');
        $serviceCode = $request->input('serviceCode');
        $text = $request->input('text', '');

        // Clean up expired sessions
        UssdSession::cleanupExpiredSessions();

        // Find or create session
        $session = UssdSession::findActiveSession($sessionId);
        
        if (!$session) {
            $session = UssdSession::createSession($sessionId, $phoneNumber, $serviceCode);
        }

        // Update session activity
        $session->updateActivity();
        $session->setInput($text);

        // Process the request
        $response = $this->processRequest($session, $text);

        // Update session with response
        $session->setMenu($response['menu']);
        $session->nextStep();

        return response($response['response'], 200)
               ->header('Content-Type', 'text/plain');
    }

    /**
     * Process USSD request based on current step and input
     */
    protected function processRequest(UssdSession $session, $text)
    {
        $step = $session->step;
        $input = $text;

        switch ($step) {
            case 1:
                return $this->showMainMenu($session);
            
            case 2:
                return $this->processMainMenuSelection($session, $input);
            
            case 3:
                return $this->processAuthentication($session, $input);
            
            case 4:
                return $this->processStudentSelection($session, $input);
            
            case 5:
                return $this->processServiceSelection($session, $input);
            
            case 6:
                return $this->processServiceAction($session, $input);
            
            default:
                return $this->showMainMenu($session);
        }
    }

    /**
     * Show main menu
     */
    protected function showMainMenu(UssdSession $session)
    {
        $menu = "Welcome to School Management System\n\n";
        $menu .= "1. Check Student Information\n";
        $menu .= "2. Check Attendance\n";
        $menu .= "3. Check Grades\n";
        $menu .= "4. Check Fees\n";
        $menu .= "5. Contact School\n";
        $menu .= "0. Exit\n\n";
        $menu .= "Please select an option:";

        return [
            'response' => "CON " . $menu,
            'menu' => 'main_menu'
        ];
    }

    /**
     * Process main menu selection
     */
    protected function processMainMenuSelection(UssdSession $session, $input)
    {
        switch ($input) {
            case '1':
                $session->setData('selected_service', 'student_info');
                return $this->showAuthenticationMenu($session);
            
            case '2':
                $session->setData('selected_service', 'attendance');
                return $this->showAuthenticationMenu($session);
            
            case '3':
                $session->setData('selected_service', 'grades');
                return $this->showAuthenticationMenu($session);
            
            case '4':
                $session->setData('selected_service', 'fees');
                return $this->showAuthenticationMenu($session);
            
            case '5':
                return $this->showContactInfo($session);
            
            case '0':
                $session->complete();
                return [
                    'response' => "END Thank you for using School Management System. Goodbye!",
                    'menu' => 'exit'
                ];
            
            default:
                return $this->showMainMenu($session);
        }
    }

    /**
     * Show authentication menu
     */
    protected function showAuthenticationMenu(UssdSession $session)
    {
        $menu = "Authentication Required\n\n";
        $menu .= "1. Enter Student ID\n";
        $menu .= "2. Enter Parent Phone Number\n";
        $menu .= "0. Back to Main Menu\n\n";
        $menu .= "Please select an option:";

        return [
            'response' => "CON " . $menu,
            'menu' => 'authentication_menu'
        ];
    }

    /**
     * Process authentication
     */
    protected function processAuthentication(UssdSession $session, $input)
    {
        switch ($input) {
            case '1':
                $session->setData('auth_method', 'student_id');
                return [
                    'response' => "CON Please enter Student ID:",
                    'menu' => 'enter_student_id'
                ];
            
            case '2':
                $session->setData('auth_method', 'parent_phone');
                return [
                    'response' => "CON Please enter Parent Phone Number:",
                    'menu' => 'enter_parent_phone'
                ];
            
            case '0':
                return $this->showMainMenu($session);
            
            default:
                return $this->showAuthenticationMenu($session);
        }
    }

    /**
     * Process student selection
     */
    protected function processStudentSelection(UssdSession $session, $input)
    {
        $authMethod = $session->getData('auth_method');
        
        if ($authMethod === 'student_id') {
            $student = Student::where('student_id', $input)->first();
            
            if (!$student) {
                return [
                    'response' => "CON Student ID not found. Please try again or enter 0 to go back:",
                    'menu' => 'student_not_found'
                ];
            }
            
            $session->setData('student_id', $student->id);
            $session->setData('authenticated_student', $student);
            
        } elseif ($authMethod === 'parent_phone') {
            $guardian = Guardian::where('phone', $input)->first();
            
            if (!$guardian) {
                return [
                    'response' => "CON Parent phone number not found. Please try again or enter 0 to go back:",
                    'menu' => 'parent_not_found'
                ];
            }
            
            $students = $guardian->students;
            
            if ($students->isEmpty()) {
                return [
                    'response' => "CON No students found for this parent. Please contact the school.",
                    'menu' => 'no_students'
                ];
            }
            
            if ($students->count() === 1) {
                $session->setData('student_id', $students->first()->id);
                $session->setData('authenticated_student', $students->first());
            } else {
                $session->setData('guardian_id', $guardian->id);
                return $this->showStudentSelectionMenu($session, $students);
            }
        }
        
        return $this->showServiceMenu($session);
    }

    /**
     * Show student selection menu for multiple students
     */
    protected function showStudentSelectionMenu(UssdSession $session, $students)
    {
        $menu = "Select Student:\n\n";
        
        foreach ($students as $index => $student) {
            $menu .= ($index + 1) . ". {$student->first_name} {$student->last_name} - {$student->class->name}\n";
        }
        
        $menu .= "0. Back to Main Menu\n\n";
        $menu .= "Please select a student:";

        return [
            'response' => "CON " . $menu,
            'menu' => 'student_selection'
        ];
    }

    /**
     * Show service menu
     */
    protected function showServiceMenu(UssdSession $session)
    {
        $selectedService = $session->getData('selected_service');
        $student = $session->getData('authenticated_student');
        
        if (!$student) {
            return $this->showMainMenu($session);
        }

        switch ($selectedService) {
            case 'student_info':
                return $this->showStudentInfo($session, $student);
            
            case 'attendance':
                return $this->showAttendanceInfo($session, $student);
            
            case 'grades':
                return $this->showGradesInfo($session, $student);
            
            case 'fees':
                return $this->showFeesInfo($session, $student);
            
            default:
                return $this->showMainMenu($session);
        }
    }

    /**
     * Show student information
     */
    protected function showStudentInfo(UssdSession $session, $student)
    {
        $info = "STUDENT INFORMATION\n\n";
        $info .= "Name: {$student->first_name} {$student->last_name}\n";
        $info .= "Student ID: {$student->student_id}\n";
        $info .= "Class: {$student->class->name}\n";
        $info .= "Admission Date: {$student->admission_date}\n";
        $info .= "Status: {$student->status}\n\n";
        $info .= "0. Back to Main Menu\n";
        $info .= "1. Check Another Service";

        $session->complete();

        return [
            'response' => "END " . $info,
            'menu' => 'student_info_displayed'
        ];
    }

    /**
     * Show attendance information
     */
    protected function showAttendanceInfo(UssdSession $session, $student)
    {
        $today = now()->toDateString();
        $attendance = Attendance::where('student_id', $student->id)
                               ->whereDate('date', $today)
                               ->first();

        $info = "ATTENDANCE INFORMATION\n\n";
        $info .= "Student: {$student->first_name} {$student->last_name}\n";
        $info .= "Class: {$student->class->name}\n";
        $info .= "Date: {$today}\n\n";

        if ($attendance) {
            $info .= "Status: {$attendance->status}\n";
            $info .= "Time: {$attendance->time}\n";
        } else {
            $info .= "Status: Not recorded yet\n";
        }

        $info .= "\n0. Back to Main Menu\n";
        $info .= "1. Check Another Service";

        $session->complete();

        return [
            'response' => "END " . $info,
            'menu' => 'attendance_displayed'
        ];
    }

    /**
     * Show grades information
     */
    protected function showGradesInfo(UssdSession $session, $student)
    {
        $latestGrades = Grade::where('student_id', $student->id)
                            ->where('status', 'approved')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();

        $info = "GRADES INFORMATION\n\n";
        $info .= "Student: {$student->first_name} {$student->last_name}\n";
        $info .= "Class: {$student->class->name}\n\n";

        if ($latestGrades->isNotEmpty()) {
            $info .= "Latest Grades:\n";
            foreach ($latestGrades as $grade) {
                $info .= "{$grade->subject->name}: {$grade->grade}\n";
            }
        } else {
            $info .= "No grades available yet.\n";
        }

        $info .= "\n0. Back to Main Menu\n";
        $info .= "1. Check Another Service";

        $session->complete();

        return [
            'response' => "END " . $info,
            'menu' => 'grades_displayed'
        ];
    }

    /**
     * Show fees information
     */
    protected function showFeesInfo(UssdSession $session, $student)
    {
        $info = "FEES INFORMATION\n\n";
        $info .= "Student: {$student->first_name} {$student->last_name}\n";
        $info .= "Class: {$student->class->name}\n\n";
        $info .= "For detailed fee information, please contact the school office.\n\n";
        $info .= "0. Back to Main Menu\n";
        $info .= "1. Check Another Service";

        $session->complete();

        return [
            'response' => "END " . $info,
            'menu' => 'fees_displayed'
        ];
    }

    /**
     * Show contact information
     */
    protected function showContactInfo(UssdSession $session)
    {
        $info = "SCHOOL CONTACT INFORMATION\n\n";
        $info .= "Phone: +231-XXX-XXXX\n";
        $info .= "Email: info@school.edu.lr\n";
        $info .= "Address: Monrovia, Liberia\n\n";
        $info .= "Office Hours: 8:00 AM - 4:00 PM\n";
        $info .= "Monday - Friday\n\n";
        $info .= "0. Back to Main Menu";

        $session->complete();

        return [
            'response' => "END " . $info,
            'menu' => 'contact_displayed'
        ];
    }

    /**
     * Handle webhook for delivery status
     */
    public function deliveryStatus(Request $request)
    {
        // Handle delivery status webhook from SMS provider
        $sessionId = $request->input('sessionId');
        $status = $request->input('status');
        $messageId = $request->input('messageId');

        // Update SMS notification status
        // This would typically update the SmsNotification model
        
        return response()->json(['status' => 'received']);
    }

    /**
     * Handle incoming SMS
     */
    public function incomingSms(Request $request)
    {
        // Handle incoming SMS messages
        $phoneNumber = $request->input('from');
        $message = $request->input('text');
        $messageId = $request->input('messageId');

        // Process incoming SMS if needed
        
        return response()->json(['status' => 'received']);
    }
}
