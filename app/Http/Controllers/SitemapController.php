<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemap = Sitemap::create();

        // Ana sayfa — en yüksek öncelik
        $sitemap->add(
            Url::create(route('home'))
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
        );

        // Odalar listesi
        $sitemap->add(
            Url::create(route('rooms.index'))
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
        );

        // Her aktif oda detayı
        foreach (Room::active()->ordered()->get() as $room) {
            $sitemap->add(
                Url::create(route('rooms.show', $room))
                    ->setLastModificationDate($room->updated_at)
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        }

        // Galeri
        $sitemap->add(
            Url::create(route('gallery.index'))
                ->setPriority(0.7)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
        );

        // Rezervasyon (yüksek dönüşüm değeri — yüksek priority)
        $sitemap->add(
            Url::create(route('reservations.create'))
                ->setPriority(0.7)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
        );

        // İletişim
        $sitemap->add(
            Url::create(route('contact'))
                ->setPriority(0.6)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
        );

        // SSS — rich snippet potansiyeli
        $sitemap->add(
            Url::create(route('faq'))
                ->setPriority(0.6)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
        );

        // Hakkımızda
        $sitemap->add(
            Url::create(route('about'))
                ->setPriority(0.5)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
        );

        // Yasal sayfalar — düşük öncelik
        $sitemap->add(
            Url::create(route('kvkk'))
                ->setPriority(0.2)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
        );
        $sitemap->add(
            Url::create(route('privacy'))
                ->setPriority(0.2)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
        );
        $sitemap->add(
            Url::create(route('cookie-policy'))
                ->setPriority(0.2)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
        );

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
