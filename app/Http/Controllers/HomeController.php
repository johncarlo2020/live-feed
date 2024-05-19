<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('welcome');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     public function welcome()
     {
         $videos = Video::get();
         return view('welcome',compact('videos'));

     }
    public function index()
    {
        $videos = Video::get();
        return view('home',compact('videos'));

    }

    public function settings()
    {
        return view('settings');
    }

    public function uploadVideo()
    {
        return view('uploadVideo');
    }
}
