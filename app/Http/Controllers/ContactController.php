<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessageConfirmation;
use App\Mail\ContactMessageReceived;
use App\Services\Database;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class ContactController extends Controller
{
    /**
     * Store an inquiry, email it to the site owner, and send the client a confirmation.
     *
     * The message is saved before any mail is sent, so a mail-server problem never loses an inquiry.
     */
    public function store(StoreContactMessageRequest $request): JsonResponse|RedirectResponse
    {
        // Honeypot filled in: pretend everything worked so bots learn nothing
        if (filled($request->input('website'))) {
            return $this->success($request);
        }

        $inquiry = [
            'name' => trim($request->string('name')),
            'email' => trim($request->string('email')),
            'phone' => $request->filled('phone') ? trim($request->string('phone')) : null,
            'service' => $request->filled('service') ? trim($request->string('service')) : null,
            'budget' => $request->filled('budget') ? trim($request->string('budget')) : null,
            'message' => trim($request->string('message')),
            'locale' => app()->getLocale(),
        ];

        $now = now()->toDateTimeString();
        Database::execute(
            'INSERT INTO contact_messages (name, email, phone, service, budget, message, locale, ip_address, user_agent, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $inquiry['name'], $inquiry['email'], $inquiry['phone'], $inquiry['service'], $inquiry['budget'],
                $inquiry['message'], $inquiry['locale'], $request->ip(), Str::limit((string) $request->userAgent(), 250, ''),
                $now, $now,
            ]
        );
        $messageId = (int) Database::lastInsertId();

        $settings = $this->settings();
        $recipient = config('mail.contact_to') ?: ($settings['email'] ?? null);

        // 1. Notify the owner — this is the delivery that matters
        try {
            if (! $recipient) {
                throw new \RuntimeException('No recipient: set MAIL_CONTACT_TO or the email address in Admin → Settings.');
            }

            Mail::to($recipient)->send(new ContactMessageReceived($inquiry, $messageId));
            Database::execute('UPDATE contact_messages SET mailed_at = ?, mail_error = NULL WHERE id = ?', [now()->toDateTimeString(), $messageId]);
        } catch (Throwable $exception) {
            report($exception);
            Database::execute('UPDATE contact_messages SET mail_error = ? WHERE id = ?', [Str::limit($exception->getMessage(), 500), $messageId]);
        }

        // 2. Confirm to the client — nice to have; a bad address must not affect the owner notification
        try {
            Mail::to($inquiry['email'], $inquiry['name'])->send(new ContactMessageConfirmation($inquiry, [
                'name' => ts($settings, 'hero_name') ?: ($settings['hero_name'] ?? config('app.name')),
                'email' => (string) ($recipient ?? ''),
                'whatsapp' => preg_replace('/\D+/', '', $settings['whatsapp_number'] ?? ''),
                'site_url' => url('/'),
            ]));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $this->success($request);
    }

    private function success(Request $request): JsonResponse|RedirectResponse
    {
        $message = __('Thanks! Your message has been sent. I will reply within 1–2 days.');

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : redirect()->to(url()->previous().'#contact')->with('contact_success', $message);
    }

    /**
     * @return array<string, string>
     */
    private function settings(): array
    {
        $settings = [];
        foreach (Database::query('SELECT key, value FROM settings') as $row) {
            $settings[$row->key] = (string) $row->value;
        }

        return $settings;
    }
}
