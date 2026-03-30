<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = [
            ['loc' => url('/'),             'priority' => '1.0',  'changefreq' => 'weekly'],
            ['loc' => url('/register'),      'priority' => '0.9',  'changefreq' => 'monthly'],
            ['loc' => url('/login'),         'priority' => '0.7',  'changefreq' => 'monthly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url['loc']}</loc>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "    <lastmod>" . now()->toAtomString() . "</lastmod>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /admin/',
            'Disallow: /api/',
            'Disallow: /dashboard',
            'Disallow: /dashboard/',
            'Disallow: /gym',
            'Disallow: /gym/',
            'Disallow: /dev/',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
