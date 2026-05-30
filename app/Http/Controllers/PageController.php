<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\GalleryImage;
use App\Models\LegalPage;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'rooms' => Room::active()->ordered()->limit(4)->get(),
            'galleryPreview' => GalleryImage::ordered()->limit(5)->get(),
            'settings' => Setting::many([
                'phone', 'whatsapp', 'email', 'address',
                'checkin_time', 'checkout_time',
            ]),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'settings' => Setting::many([
                'about_intro', 'about_vision', 'about_varto_region',
                'about_stat_1_value', 'about_stat_1_label',
                'about_stat_2_value', 'about_stat_2_label',
                'about_stat_3_value', 'about_stat_3_label',
            ]),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact', [
            'settings' => Setting::many([
                'phone', 'whatsapp', 'email', 'address',
                'checkin_time', 'checkout_time',
                'instagram_url', 'facebook_url', 'google_maps_url',
            ]),
        ]);
    }

    public function faq(): View
    {
        return view('pages.faq', [
            'faqs' => Faq::active()->ordered()->get(),
        ]);
    }

    public function kvkk(): View
    {
        return $this->renderLegalPage('kvkk', 'pages.kvkk');
    }

    public function privacy(): View
    {
        return $this->renderLegalPage('privacy', 'pages.privacy');
    }

    public function cookiePolicy(): View
    {
        return $this->renderLegalPage('cookie-policy', 'pages.cookie-policy');
    }

    /**
     * Hukuki sayfa render — DB'de varsa DB içerik (admin RichEditor düzenlemesi),
     * yoksa fallback olarak statik blade. Geçiş döneminde regression riski yok.
     * Settings placeholder'ları ({{ phone }}, {{ email }}, vb.) inject edilir.
     */
    private function renderLegalPage(string $slug, string $fallbackView): View
    {
        $page = LegalPage::forSlug($slug);

        if (! $page || empty($page->content_html)) {
            return view($fallbackView);
        }

        $content = $this->injectLegalPlaceholders($page->content_html, $page);

        return view('pages.legal-page', [
            'page' => $page,
            'content' => $content,
        ]);
    }

    /** {{ phone }}, {{ email }}, {{ address }}, {{ last_reviewed_at }} placeholder'larını doldur. */
    private function injectLegalPlaceholders(string $html, LegalPage $page): string
    {
        $replacements = [
            '{{ phone }}' => Setting::get('phone', ''),
            '{{ email }}' => Setting::get('email', ''),
            '{{ address }}' => Setting::get('address', 'Varto, Muş, Türkiye'),
            '{{ last_reviewed_at }}' => $page->last_reviewed_at
                ? $page->last_reviewed_at->translatedFormat('d F Y')
                : '',
        ];

        return strtr($html, $replacements);
    }
}
