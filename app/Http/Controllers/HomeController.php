<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Settings;
use App\Events\ModeEvent;
use App\Events\PlayingEvent;
use App\Events\StartEvent;


use Carbon\Carbon;

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
         $videos = Video::where('is_favorite',true)->get();
         return view('welcome',compact('videos'));

     }
     public function favorite(Request $request)
     {
        $isFavorite = $request->value === 'true' ? false : true;
        $video = Video::find($request->id);
        $video->is_favorite = $isFavorite;
        $video->save();

        return $video;
     }

     public function favoriteShow(Request $request)
     {
        $video = Video::where('is_favorite',1)->orderBy('updated_at','desc')->get();

        Video::where('status', 2)->update([
            'status' => 3, // Set played_time to current datetime
        ]);

        Video::where('is_favorite', 1)->take(10)->update([
            'status' => 2,
            'played_time' => Carbon::now() // Set played_time to current datetime
        ]);

        return $video;
     }

    public function index()
    {
        $videos = Video::orderBy('created_at','desc')->get();
        $settings = Settings::where('name','mode')->first();

        return view('home',compact('videos','settings'));
    }

    public function queue()
    {
        $videos = Video::where('status',1)->get();
        $settings = Settings::where('name','queue')->first();
        //  dd($settings);

        $queue = $settings->value;

        return view('queue',compact('videos','settings','queue'));
    }

    public function start()
    {
        $data['playing'] = Video::where('is_favorite',1)->take(10)->orderBy('created_at',
        'asc')->get();
        Video::where('is_favorite', 1)->take(10)->orderBy('created_at',
        'asc')->update([
            'status' => 2,
            'played_time' => Carbon::now() // Set played_time to current datetime
        ]);
        $data['pending'] = Video::where('status',1)->get();
        $settings = Settings::where('name','queue')->first();
        $settings->value = true;
        $settings->save();

        event(new StartEvent($data['playing']));


        return $data;
    }

    public function play(Request $request)
    {
        $video = Video::find($request->value);
        $video->status = 2;
        $video->save();
        event(new PlayingEvent($video));

        return $video;
    }

    public function settings()
    {
        return view('settings');
    }

    public function mode(Request $request)
    {
        $value = $request->input('value') === 'true' ? true : false;


        $settings = Settings::where('name','mode')->first();
        $settings->value = $value;
        $settings->save();

        event(new ModeEvent($value));


        return $settings;
    }

    public function uploadVideo()
    {
        return view('uploadVideo');
    }
}
