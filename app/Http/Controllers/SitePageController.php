<?php
namespace App\Http\Controllers;

use Illuminate\View\View;

class SitePageController extends Controller
{

    public function home(): View
    {
        return view('home');
    }

    public function portfolio(): View
    {
        return view('portfolio');
    }

    public function about(): View
    {
        return view('about');
    }

    public function contact(): View
    {
        return view('contact');
    }

}