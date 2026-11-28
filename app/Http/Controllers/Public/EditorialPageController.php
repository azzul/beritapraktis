<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\SeoSchemaService;
use Illuminate\View\View;

class EditorialPageController extends Controller
{
    public function about(SeoSchemaService $seo): View
    {
        $schemas = [
            $seo->organizationSchema(),
            $seo->breadcrumbSchema([
                ['name' => 'Beranda', 'url' => route('home')],
                ['name' => 'Tentang Kami', 'url' => route('pages.about')],
            ]),
        ];

        return view('public.pages.about', compact('schemas'));
    }

    public function editorialTeam(SeoSchemaService $seo): View
    {
        $schemas = [
            $seo->organizationSchema(),
            $seo->breadcrumbSchema([
                ['name' => 'Beranda', 'url' => route('home')],
                ['name' => 'Susunan Redaksi', 'url' => route('pages.editorial')],
            ]),
        ];

        return view('public.pages.editorial', compact('schemas'));
    }

    public function contact(SeoSchemaService $seo): View
    {
        $schemas = [
            $seo->organizationSchema(),
            $seo->breadcrumbSchema([
                ['name' => 'Beranda', 'url' => route('home')],
                ['name' => 'Kontak Redaksi', 'url' => route('pages.contact')],
            ]),
        ];

        return view('public.pages.contact', compact('schemas'));
    }

    public function cyberMediaGuidelines(SeoSchemaService $seo): View
    {
        $schemas = [
            $seo->breadcrumbSchema([
                ['name' => 'Beranda', 'url' => route('home')],
                ['name' => 'Pedoman Media Siber', 'url' => route('pages.cyber-guidelines')],
            ]),
        ];

        return view('public.pages.cyber-guidelines', compact('schemas'));
    }

    public function privacyPolicy(SeoSchemaService $seo): View
    {
        $schemas = [
            $seo->breadcrumbSchema([
                ['name' => 'Beranda', 'url' => route('home')],
                ['name' => 'Kebijakan Privasi', 'url' => route('pages.privacy')],
            ]),
        ];

        return view('public.pages.privacy', compact('schemas'));
    }
}
