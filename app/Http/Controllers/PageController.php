<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
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
        return view('pages.about');
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
        return view('pages.faq');
    }

    public function kvkk(): View
    {
        return view('pages.kvkk');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function cookiePolicy(): View
    {
        return view('pages.cookie-policy');
    }
}
