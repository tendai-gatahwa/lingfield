<?php

namespace App\Traits;

use App\Models\User;
use App\SmsTemplate;
use App\SmSmsGateway;
use GuzzleHttp\Client;
use App\SmEmailSetting;
use App\SmNotification;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Models\SmNotificationSetting;
use AfricasTalking\SDK\AfricasTalking;
use App\Notifications\AppNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Notification;

trait NotificationSend
{

    public function sent_notifications($event, $user_ids, $data, $role_names)
    {
        try {
            $notificationData = SmNotificationSetting::where('event', $event)
                ->where('school_id', auth()->user()->school_id)
                ->first();

            foreach ($notificationData->recipient as $roleName => $recipientType) {
                // For Super Admin Start
                if ($recipientType == 1) {
                    foreach ($notificationData->destination as $key => $type) {
                        if ($roleName == 'Super admin') {
                            $admin = User::find(1, ['id', 'full_name', 'email', 'phone_number']);
                            $data['user_id'] = $admin->id;
                            $data['role_id'] = 1;
                            $data['receiver_name'] = $admin->full_name;
                            $data['receiver_email'] = $admin->email;
                            $data['receiver_phone_number'] = $admin->phone_number;
                            $data['admin_name'] = $data['receiver_name'];
                            if ($type == 1) {
                                $function = 'send_' . strtolower($key);
                                $this->$function($notificationData, $roleName, $data);
                            }
                        }
                    }
                }
                // For Super Admin End
                if ($recipientType == 1) {
                    if(!is_null($role_names)){
                        if (in_array($roleName, $role_names)) {
                            foreach ($notificationData->destination as $key => $type) {
                                // For Super Admin Start
                                if ($roleName == 'Super admin') {
                                    $admin = User::find(1, ['id', 'full_name', 'email', 'phone_number']);
                                    $data['user_id'] = $admin->id;
                                    $data['role_id'] = 1;
                                    $data['receiver_name'] = $admin->full_name;
                                    $data['receiver_email'] = $admin->email;
                                    $data['receiver_phone_number'] = $admin->phone_number;
                                    $data['admin_name'] = $data['receiver_name'];
                                    if ($type == 1) {
                                        $function = 'send_' . strtolower($key);
                                        $this->$function($notificationData, $roleName, $data);
                                    }
                                }
                                // For Super Admin End
                                foreach ($user_ids as $user_id) {
                                    $userInfo = User::with('roles')->find($user_id, ['id', 'full_name', 'email', 'phone_number', 'role_id']);
                                    if ($roleName == 'Student') {
                                        $data['user_id'] = $userInfo->id;
                                        $data['role_id'] = $userInfo->roles->id;
                                        $data['receiver_name'] = $userInfo->full_name;
                                        $data['receiver_email'] = $userInfo->email;
                                        $data['receiver_phone_number'] = $userInfo->phone_number;
    
                                        $data['student_name'] = $userInfo->full_name;
                                    } elseif ($roleName == 'Parent') {
                                        $data['role_id'] = 3;
                                        if($userInfo->role_id == 3){
                                            $data['user_id'] = $userInfo->id;
                                            $data['receiver_name'] = $userInfo->full_name;
                                            $data['receiver_email'] = $userInfo->email;
                                            $data['receiver_phone_number'] = $userInfo->phone_number;
        
                                            $data['parent_name'] = $data['receiver_name'];
                                        }else{
                                            $data['user_id'] = $userInfo->student->parents->user_id;
                                            $data['receiver_name'] = $userInfo->student->parents->guardians_name;
                                            $data['receiver_email'] = $userInfo->student->parents->guardians_email;
                                            $data['receiver_phone_number'] = $userInfo->student->parents->guardians_mobile;
        
                                            $data['parent_name'] = $data['receiver_name'];
                                            $data['student_name'] = $userInfo->full_name;
                                        }
                                    } elseif ($roleName == 'Teacher') {
                                        $data['user_id'] = $userInfo->id;
                                        $data['role_id'] = $userInfo->roles->id;
                                        $data['receiver_name'] = $userInfo->full_name;
                                        $data['receiver_email'] = $userInfo->email;
                                        $data['receiver_phone_number'] = $userInfo->phone_number;
                                    }
                                    if ($type == 1) {
                                        $function = 'send_' . strtolower($key);
                                        $this->$function($notificationData, $roleName, $data);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
           Log::info($e);
        }
    }

    public function send_email($notificationData, $role, $data)
    {
        if ($notificationData->recipient[$role] != 1) {
            return;
        }

        $receiver_name = gv($data, 'receiver_name');
        $reciver_email = gv($data, 'receiver_email');

        if (!$reciver_email) {
            return;
        }

        $school_id = auth()->check() && saasSettings('email_settings') ? auth()->user()->school_id : 1;
        $setting = SmEmailSetting::where('school_id', $school_id)->where('active_status', 1)->first();

        if (!$setting) {
            return;
        }
        $sender_email = $setting->from_email;
        $sender_name = $setting->from_name;
        $email_driver = $setting->mail_driver;

        $subject = $notificationData->subject[$role];
        $templete = $notificationData->template[$role]['Email'];

        $body = SmNotificationSetting::templeteData($templete, $data);
        $view = view('backEnd.email.emailBody', compact('body'));

        try {
            if ($email_driver == "smtp") {
                if (Schema::hasTable('sm_email_settings')) {
                    $config = auth()->check() ? DB::table('sm_email_settings')
                        ->where('school_id', auth()->user()->school_id)
                        ->where('mail_driver', 'smtp')
                        ->first() :
                        DB::table('sm_email_settings')
                        ->where('mail_driver', 'smtp')
                        ->first();

                    if ($config) {
                        Config::set('mail.driver', $config->mail_driver);
                        Config::set('mail.from', $config->mail_username);
                        Config::set('mail.name', $config->from_name);
                        Config::set('mail.host', $config->mail_host);
                        Config::set('mail.port', $config->mail_port);
                        Config::set('mail.username', $config->mail_username);
                        Config::set('mail.password', $config->mail_password);
                        Config::set('mail.encryption', $config->mail_encryption);
                    }
                }
                Mail::send('backEnd.email.emailBody', compact('body'), function ($message) use ($reciver_email, $receiver_name, $sender_name, $sender_email, $subject) {
                    $message->to($reciver_email, $receiver_name)->subject($subject);
                    $message->from($sender_email, $sender_name);
                });
            }
            if ($email_driver == "php") {
                $message = (string)$view;
                $headers = "From: <$sender_email> \r\n";
                $headers .= "Reply-To: $receiver_name <$reciver_email> \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=utf-8\r\n";
                @mail($reciver_email, $subject, $message, $headers);
            }
        } catch (\Exception $e) {
            Log::info($e);
        }
    }

    public function send_sms($notificationData, $role, $data)
    {
        return;
        if ($notificationData->recipient[$role] != 1) {
            return;
        }

        $reciver_number = $data['reciver_number'];
        if (!$reciver_number) {
            return;
        }

        $school_id = auth()->check() && saasSettings('sms_settings') ? auth()->user()->school_id : 1;
        $activeSmsGateway = SmSmsGateway::where('school_id', $school_id)->where('active_status', 1)->first();
        if (!$activeSmsGateway) {
            return;
        }

        $templete = $notificationData->template[$role]['SMS'];
        $body = SmsTemplate::smsTempleteToBody($templete, $data);

        try {
            if ($activeSmsGateway->gateway_name == 'Twilio') {
                $account_id = $activeSmsGateway->twilio_account_sid;
                $auth_token = $activeSmsGateway->twilio_authentication_token;
                $from_phone_number = $activeSmsGateway->twilio_registered_no;
                if (!$account_id || $auth_token) {
                    return;
                }

                $client = new Client($account_id, $auth_token);
                $result = $message = $client->messages->create($reciver_number, array('from' => $from_phone_number, 'body' => $body));
            } else if ($activeSmsGateway->gateway_name == 'Msg91') {
                $msg91_authentication_key_sid = $activeSmsGateway->msg91_authentication_key_sid;
                $msg91_sender_id = $activeSmsGateway->msg91_sender_id;
                $msg91_route = $activeSmsGateway->msg91_route;
                $msg91_country_code = $activeSmsGateway->msg91_country_code;

                if ($reciver_number != "") {
                    $curl = curl_init();
                    $url = "https://api.msg91.com/api/sendhttp.php?mobiles=" .
                        $reciver_number . "&authkey=" .
                        $msg91_authentication_key_sid . "&route=" .
                        $msg91_route . "&sender=" .
                        $msg91_sender_id . "&message=" .
                        $body . "&country=91";

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 30, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0,
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                }
            } elseif ($activeSmsGateway->gateway_name == 'TextLocal') {
                // Config variables. Consult http://api.txtlocal.com/docs for more info.
                $url = $activeSmsGateway->type == 'in' ? 'https://api.textlocal.in/send/?' : 'https://api.txtlocal.com/send/?';
                $test = "0";
                $sender = $activeSmsGateway->textlocal_sender; // This is who the message appears to be from.
                $message = urlencode($body);
                $data = "username=" . $activeSmsGateway->textlocal_username .
                    "&hash=" . $activeSmsGateway->textlocal_hash .
                    "&message=" . $message .
                    "&sender=" . $sender .
                    "&numbers=" . $reciver_number .
                    "&test=" . $test;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch); // This is the result from the API
                curl_close($ch);
            } else if ($activeSmsGateway->gateway_name == 'AfricaTalking') {
                $username = $activeSmsGateway->africatalking_username;
                $apiKey = $activeSmsGateway->africatalking_api_key;
                $AT = new AfricasTalking($username, $apiKey);

                $sms_Send = $AT->sms();
                $sms_Send->send(['to' => $reciver_number, 'message' => $body]);
            } else if ($activeSmsGateway->gateway_name == 'Himalayasms') {
                if ($reciver_number != "") {
                    $client = new Http();
                    $request = $client->get("https://sms.techhimalaya.com/base/smsapi/index.php", [
                        'query' => [
                            'key' => $activeSmsGateway->himalayasms_key,
                            'senderid' => $activeSmsGateway->himalayasms_senderId,
                            'campaign' => $activeSmsGateway->himalayasms_campaign,
                            'routeid' => $activeSmsGateway->himalayasms_routeId,
                            'contacts' => $reciver_number,
                            'msg' => $body,
                            'type' => "text"
                        ],
                        'http_errors' => false
                    ]);
                    $request->getBody();
                }
            } elseif ($activeSmsGateway->gateway_type == "custom") {
                @send_custom_sms($reciver_number, $body, $activeSmsGateway);
            }
        } catch (\Exception $e) {
            Log::info($e);
        }
    }

    public function send_web($notificationData, $role, $data)
    {
        if ($notificationData->recipient[$role] != 1) {
            return;
        }

        $templete = $notificationData->template[$role]['Web'];
        $body = SmNotificationSetting::templeteData($templete, $data);

        $notification = new SmNotification;
        $notification->user_id = gv($data, 'user_id');
        $notification->role_id = gv($data, 'role_id');
        $notification->message = $body;
        $notification->url = gv($data, 'url', NULL);
        $notification->date = date('Y-m-d');
        $notification->school_id = auth()->user()->school_id;
        if (moduleStatusCheck('University')) {
            $notification->un_academic_id = getAcademicId();
        } else {
            $notification->academic_id = getAcademicId();
        }
        $notification->save();
    }

    public function send_app($notificationData, $role, $data)
    {
        if ($notificationData->recipient[$role] != 1) {
            return;
        }

        try {
            $templete = $notificationData->template[$role]['App'];                  
            $data['message'] = SmNotificationSetting::templeteData($templete, $data);

            $user = User::find(gv($data, 'user_id'));
            Notification::send($user, new AppNotification($data));
        } catch (\Exception $e) {
            Log::info($e->getMessage());    
        }
    }

    public function studentRecordInfo($class_id = null, $section_id = null)
    {
        return StudentRecord::with('studentDetail', 'class', 'section')
            ->when($class_id, function ($c) use ($class_id) {
                $c->where('class_id', $class_id);
            })
            ->when($section_id, function ($s) use ($section_id) {
                $s->where('section_id', $section_id);
            })
            ->where('is_promote', 0)
            ->where('active_status', 1)
            ->where('school_id', auth()->user()->school_id)
            ->where('academic_id', getAcademicId())
            ->distinct('student_id')
            ->get();
    }
}
