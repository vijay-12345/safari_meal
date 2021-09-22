<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use \Mail;

class OtpVerification extends AppModel
{

	public $timestamps = true;

	protected $table = 'otp_verification';

    protected $primaryKey = 'id';

    protected $fillable = [
        'mobile', 'email', 'otp', 'status'
    ];

    public function sendOtp($request) {

        try {
            
            $otp = mt_rand(100000, 999999);
            
            $otp = 123456;

            $otpVerification = self::where('mobile', $request->contact_number)->orWhere('email', $request->email)->first();

            if ($otpVerification) {
                // $otp = $otpVerification->otp;
                $otpVerification->delete();
            }

            $this->sendMailOrOTP($request, $otp);

            $data = ['mobile' => $request->contact_number, 'email' => $request->email, 'otp' => $otp];

            $this->fill($data)->save();

        } catch(\Exception $e) {
            // print_r($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }


    public function sendMailOrOTP($request, $otp) {

        $data  = $request->all();
        $data['otp'] = $otp;

        $subject = 'Your Verification Code';

        try {
            \Mail::send('emails.otp-verify', $data, function ($mess) use ($data, $subject) {
                $admin_email = \Config::get('constants.administrator.email');
                $mess->from($admin_email, \Config::get('constants.site_name'));
                $mess->to($data['email'], ucfirst($data['first_name']))->subject($subject);
            });
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function verifyOTP($request) {
        $arguments = [];
        $current = \Carbon\Carbon::now();
        $otp = $request->otp;

        $query  = self::where(function ($query) use ($request) {
                        $query->where('mobile', $request->contact_number);
                        $query->orWhere('email', $request->email);
                    });
        $otpObj = $query->where('otp', $otp)->first();

        if ($otpObj) {
            $dt = new \DateTime(date("Y-m-d H:i:s"));
            $dt->modify('- 90 second');
            $created = new \DateTime($otpObj->created_at);

            if ($created > $dt) {
                $otpObj->status = 1;
                $otpObj->save();
                return ['status' => true, 'message' => 'Otp is vetified.'];
            } else {
                $otpObj->delete();
                throw new Exception('Otp is expired.');
            }
        }
        throw new Exception('Otp is not valid.');
    }

}