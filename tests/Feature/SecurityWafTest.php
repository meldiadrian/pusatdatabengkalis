<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityWafTest extends TestCase
{
    public function test_blocks_malicious_user_agents(): void
    {
        $blockedAgents = [
            'sqlmap/1.5#stable',
            'Mozilla/5.0 (compatible; Nikto 2.1.6)',
            'acunetix-wvs',
            'masscan/1.0',
            'DirBuster-1.0-RC1',
            'gobuster/3.1.0',
            'libwww-perl/6.08',
            'Wfuzz/3.1.0',
        ];

        foreach ($blockedAgents as $agent) {
            $response = $this->withHeaders(['User-Agent' => $agent])->get('/bulubabi');
            $response->assertStatus(403);
        }
    }

    public function test_blocks_oversized_user_agent(): void
    {
        $oversizedAgent = str_repeat('A', 501);
        $response = $this->withHeaders(['User-Agent' => $oversizedAgent])->get('/bulubabi');
        $response->assertStatus(403);
    }

    public function test_blocks_crlf_in_user_agent(): void
    {
        $response = $this->withHeaders(['User-Agent' => "Mozilla/5.0\r\nX-Injected: true"])->get('/bulubabi');
        $response->assertStatus(403);
    }

    public function test_blocks_sql_injection_in_query_string(): void
    {
        $payloads = [
            '1 UNION SELECT null, username, password FROM users',
            '1\' OR \'1\'=\'1',
            '1\' OR 1=1--',
            '1; DROP TABLE users',
            '1 AND (SELECT 1 FROM (SELECT SLEEP(5))a)',
            '1\' AND 1=1#',
            '1\' UNION ALL SELECT NULL, version()--',
        ];

        foreach ($payloads as $payload) {
            $response = $this->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
            ])->get('/bulubabi?search=' . urlencode($payload));

            $response->assertStatus(403);
        }
    }

    public function test_blocks_double_encoded_sql_injection(): void
    {
        // %2527 is double-encoded '
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ])->get('/bulubabi?q=%2527%20OR%201%3D1--');

        $response->assertStatus(403);
    }

    public function test_blocks_nested_post_sql_injection(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ])->post('/bulubabi', [
            'filter' => [
                'field' => 'name',
                'value' => "admin' UNION SELECT password FROM users--",
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_returns_json_forbidden_when_accept_json_requested(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'sqlmap/1.4',
            'Accept'     => 'application/json',
        ])->get('/bulubabi');

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Forbidden',
                'code'  => 403,
            ]);
    }

    public function test_bypasses_health_check(): void
    {
        $response = $this->get('/up');
        $response->assertStatus(200);
    }

    public function test_allows_passwords_with_special_characters(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ])->post('/bulubabi', [
            'username'              => 'user_bengkalis',
            'password'              => "P@ssw0rd' OR '1'='1!",
            'password_confirmation' => "P@ssw0rd' OR '1'='1!",
        ]);

        // Should NOT be blocked with 403 because password fields are excluded from SQLi scanning
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_allows_normal_legitimate_requests(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ])->get('/bulubabi');

        // /bulubabi is the Filament login/dashboard route
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_sets_security_headers_including_clickjacking_protection(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ])->get('/bulubabi');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $this->assertStringContainsString("frame-ancestors 'self'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("base-uri 'self'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("object-src 'none'", $response->headers->get('Content-Security-Policy'));
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');
        $response->assertHeader('X-Download-Options', 'noopen');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertFalse($response->headers->has('X-Powered-By'));
    }
}

