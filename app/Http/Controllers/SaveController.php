<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Events\UploadEvent;
class SaveController extends Controller
{
    public function saveVideos(Request $request)
    {
        // Logic to save videos here
        // Example: Save each video file and associated data

        // Retrieve videos from request
        $videos = $request->file('videos');

        // Save each video
        foreach ($videos as $key => $value) {
            // Example: Save the video to storage
            $originalFilename = $value->getClientOriginalName();
            $extension = $value->getClientOriginalExtension();

            // Generate a unique filename to avoid conflicts
            $filename = pathinfo($originalFilename, PATHINFO_FILENAME);

            $filenameToStore = $filename . '_' . time() . '.' . $extension;

            // Save the video to storage with the original filename and extension
            $videoPath = $value->storeAs('videos', $filenameToStore, 'public');


            // Example: Save additional data related to the video
            $size = $request->input('size.'.$key); // Retrieve input at index $key
            $playtime = $request->input('playtime.'.$key); // Retrieve input at index $key

            $video = new Video;
            $video->name = $videoPath;
            $video->size = $size;
            $video->playtime = $playtime;
            $video->save();


            event(new UploadEvent($video));


            // Save $videoPath, $size, $playtime to database or any other storage
        }



        // Redirect or return a response
        return redirect()->back()->with('success', 'Videos uploaded successfully.');
    }
}
