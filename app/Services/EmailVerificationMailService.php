<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailVerificationMailService
{
    public function send(User $user, ?EmailVerification $verification = null, string $source = 'system'): EmailLog
    {
        $verification = $verification ?: $this->latestOrCreateVerification($user);

        $subject   = 'Verifikasi Email Akun Puwinter';
        $verifyUrl = route('verification.verify', $verification->token);
        $logoUrl   = asset('images/logo.png');
        $mailer    = config('mail.default');
        $transport = config("mail.mailers.{$mailer}.transport");

        $log = EmailLog::create([
            'user_id'               => $user->id,
            'email_verification_id' => $verification->id,
            'type'                  => 'email_verification',
            'source'                => $source,
            'mailer'                => $mailer,
            'transport'             => $transport,
            'from_email'            => config('mail.from.address'),
            'from_name'             => config('mail.from.name'),
            'to_email'              => $user->email,
            'to_name'               => $user->name,
            'subject'               => $subject,
            'status'                => 'processing',
            'payload'               => [
                'app_url'     => config('app.url'),
                'verify_url'  => $verifyUrl,
                'logo_url'    => $logoUrl,
                'expires_at'  => optional($verification->expires_at)->toDateTimeString(),
            ],
        ]);

        try {
            Mail::send('emails.verify-email', [
                'user'       => $user,
                'verifyUrl'  => $verifyUrl,
                'logoUrl'    => $logoUrl,
                'expiresAt'  => $verification->expires_at,
                'emailLogId' => $log->id,
            ], function ($message) use ($user, $subject, $log) {
                $message->to($user->email, $user->name)
                    ->subject($subject);

                // Header pembantu untuk melacak email ini di mail log server/cPanel.
                try {
                    $message->getHeaders()->addTextHeader('X-Puwinter-Email-Log-ID', (string) $log->id);
                } catch (\Throwable $ignored) {
                    // Tidak semua versi message object mendukung header custom.
                }
            });

            $log->markSent(
                'Laravel Mail::send selesai tanpa exception. Artinya email berhasil diserahkan ke mailer/server SMTP. Detail final delivery tetap bisa dicek di cPanel Track Delivery atau log mail server.',
                [
                    'result' => 'no_exception_from_mailer',
                ]
            );
        } catch (\Throwable $exception) {
            $log->markFailed($exception, [
                'result' => 'exception_from_mailer',
            ]);
        }

        return $log->fresh();
    }

    private function latestOrCreateVerification(User $user): EmailVerification
    {
        $verification = EmailVerification::where('user_id', $user->id)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if ($verification) {
            return $verification;
        }

        return EmailVerification::create([
            'user_id'    => $user->id,
            'token'      => Str::random(64),
            'expires_at' => now()->addHours(24),
        ]);
    }
}
