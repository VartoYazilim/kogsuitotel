<?php

namespace App\Http\Controllers;

use App\Filament\Resources\GalleryImages\GalleryImageResource;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->string('kategori')->toString() ?: null;

        $query = GalleryImage::ordered();

        if ($category && array_key_exists($category, GalleryImageResource::CATEGORIES)) {
            $query->inCategory($category);
        }

        return view('gallery.index', [
            'images' => $query->get(),
            'categories' => GalleryImageResource::CATEGORIES,
            'activeCategory' => $category,
        ]);
    }
}
