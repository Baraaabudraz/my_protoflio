<?php

namespace Tests\Feature;

use App\Mail\ContactMessageConfirmation;
use App\Mail\ContactMessageReceived;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Runs against the in-memory test database with a fake mailer — nothing is really sent.
 */
class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected $seeder = PortfolioSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        RateLimiter::clear('contact');
        config(['mail.contact_to' => 'owner@example.com']);
    }

    /**
     * @return array<string, string>
     */
    private function validInquiry(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Sara Client',
            'email' => 'sara@example.com',
            'phone' => '+970 59 111 2222',
            'service' => 'Custom System Development',
            'budget' => '$1,500 – $5,000',
            'message' => 'We need a booking system for our clinic with an admin dashboard.',
        ], $overrides);
    }

    public function test_inquiry_is_stored_and_emailed_to_the_owner_and_the_client(): void
    {
        $this->postJson(route('contact.store'), $this->validInquiry())
            ->assertOk()
            ->assertJsonStructure(['message']);

        $stored = DB::table('contact_messages')->first();
        $this->assertSame('sara@example.com', $stored->email);
        $this->assertNotNull($stored->mailed_at);
        $this->assertNull($stored->mail_error);

        Mail::assertSent(ContactMessageReceived::class, fn ($mail) => $mail->hasTo('owner@example.com') && $mail->hasReplyTo('sara@example.com'));
        Mail::assertSent(ContactMessageConfirmation::class, fn ($mail) => $mail->hasTo('sara@example.com'));
    }

    public function test_the_owner_email_falls_back_to_the_address_in_settings(): void
    {
        config(['mail.contact_to' => null]);
        $settingsEmail = DB::table('settings')->where('key', 'email')->value('value');

        $this->postJson(route('contact.store'), $this->validInquiry())->assertOk();

        Mail::assertSent(ContactMessageReceived::class, fn ($mail) => $mail->hasTo($settingsEmail));
    }

    public function test_arabic_visitors_get_the_confirmation_in_arabic(): void
    {
        $this->withSession(['locale' => 'ar'])
            ->postJson(route('contact.store'), $this->validInquiry(['name' => 'سارة']))
            ->assertOk();

        $this->assertSame('ar', DB::table('contact_messages')->value('locale'));
        Mail::assertSent(ContactMessageConfirmation::class, fn ($mail) => $mail->locale === 'ar');
    }

    public function test_invalid_input_returns_field_errors_and_stores_nothing(): void
    {
        $this->postJson(route('contact.store'), $this->validInquiry(['email' => 'not-an-email', 'message' => 'short', 'name' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        $this->assertSame(0, DB::table('contact_messages')->count());
        Mail::assertNothingSent();
    }

    public function test_bots_filling_the_honeypot_get_a_fake_success(): void
    {
        $this->postJson(route('contact.store'), $this->validInquiry(['website' => 'https://spam.example']))
            ->assertOk();

        $this->assertSame(0, DB::table('contact_messages')->count());
        Mail::assertNothingSent();
    }

    public function test_message_is_kept_when_mail_delivery_fails(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP connection refused'));

        $this->postJson(route('contact.store'), $this->validInquiry())->assertOk();

        $stored = DB::table('contact_messages')->first();
        $this->assertNotNull($stored);
        $this->assertNull($stored->mailed_at);
        $this->assertStringContainsString('SMTP connection refused', $stored->mail_error);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('contact.store'), $this->validInquiry())->assertOk();
        }

        $this->postJson(route('contact.store'), $this->validInquiry())->assertTooManyRequests();
        $this->assertSame(5, DB::table('contact_messages')->count());
    }

    public function test_non_javascript_submission_redirects_back_with_a_success_message(): void
    {
        $this->from('/')
            ->post(route('contact.store'), $this->validInquiry())
            ->assertRedirect(url('/').'#contact')
            ->assertSessionHas('contact_success');
    }

    public function test_admin_inbox_lists_messages_and_can_mark_them_read(): void
    {
        $this->postJson(route('contact.store'), $this->validInquiry())->assertOk();
        $id = DB::table('contact_messages')->value('id');

        $this->withSession(['admin_logged_in' => true])
            ->get(route('admin.messages'))
            ->assertOk()
            ->assertSee('Sara Client')
            ->assertSee('We need a booking system');

        $this->withSession(['admin_logged_in' => true])
            ->patch(route('admin.messages.read', $id))
            ->assertRedirect();

        $this->assertNotNull(DB::table('contact_messages')->where('id', $id)->value('read_at'));
    }

    public function test_admin_inbox_requires_login(): void
    {
        $this->get(route('admin.messages'))->assertRedirect(route('admin.login'));
    }
}
