<?php

namespace App\Traits;

use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

trait HasOtp
{
    /**
     * Generate and send OTP
     */
    public function generateOtp($type = 'verification')
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->otp = $otp;
        $this->otp_expires_at = Carbon::now()->addMinutes(10);
        $this->save();
        
        Mail::to($this->email)->send(new OtpMail($otp, $type));
        
        return $otp;
    }
    
    /**
     * Verify OTP
     */
    public function verifyOtp($otp)
    {
        if (!$this->otp || !$this->otp_expires_at) {
            return false;
        }
        
        if (Carbon::now()->greaterThan($this->otp_expires_at)) {
            return false;
        }
        
        if ($this->otp !== $otp) {
            return false;
        }
        
        $this->otp = null;
        $this->otp_expires_at = null;
        $this->email_verified_at = Carbon::now();
        $this->save();
        
        return true;
    }
    
    /**
     * Clear OTP
     */
    public function clearOtp()
    {
        $this->otp = null;
        $this->otp_expires_at = null;
        $this->save();
    }
}

