<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    // ─── Sitemap ───────────────────────────────────────────────────────────

    #[Test]
    public function sitemap_returns_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'application/xml');

        $this->assertStringContainsString('<?xml', $response->getContent());
        $this->assertStringContainsString('<urlset', $response->getContent());
        $this->assertStringContainsString('<url>', $response->getContent());
    }

    #[Test]
    public function sitemap_contains_homepage_url(): void
    {
        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString(url('/'), $response->getContent());
        $this->assertStringContainsString('<priority>1.0</priority>', $response->getContent());
    }

    #[Test]
    public function sitemap_contains_register_url(): void
    {
        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString(url('/register'), $response->getContent());
    }

    // ─── Robots.txt ────────────────────────────────────────────────────────

    #[Test]
    public function robots_txt_returns_correct_response(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Allow: /', $content);
    }

    #[Test]
    public function robots_txt_disallows_admin_and_dashboard(): void
    {
        $response = $this->get('/robots.txt');
        $content  = $response->getContent();

        $this->assertStringContainsString('Disallow: /admin', $content);
        $this->assertStringContainsString('Disallow: /dashboard', $content);
        $this->assertStringContainsString('Disallow: /api/', $content);
    }

    #[Test]
    public function robots_txt_references_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $this->assertStringContainsString('Sitemap:', $response->getContent());
        $this->assertStringContainsString('sitemap.xml', $response->getContent());
    }

    // ─── Landing page ──────────────────────────────────────────────────────

    #[Test]
    public function landing_page_is_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    #[Test]
    public function landing_page_contains_og_tags(): void
    {
        $response = $this->get('/');
        $content  = $response->getContent();

        $this->assertStringContainsString('property="og:title"', $content);
        $this->assertStringContainsString('property="og:description"', $content);
        $this->assertStringContainsString('property="og:type"', $content);
        $this->assertStringContainsString('property="og:url"', $content);
    }

    #[Test]
    public function landing_page_contains_canonical_url(): void
    {
        $response = $this->get('/');

        $this->assertStringContainsString('rel="canonical"', $response->getContent());
    }
}
