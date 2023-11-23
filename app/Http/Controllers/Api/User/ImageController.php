<?php

namespace App\Http\Controllers\Api\User;

use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class ImageController extends Controller
{
     use ResponseTrait;
    public function upload(Request $request)
{
    try {
        $user = Auth::user();

        if ($user->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'You are banned from accessing this feature.',
            ], 403);
        }

         Log::info('Image uploaded start by user: ' . $user->id);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust validation rules as needed
        ]);

         Log::info('Image check successfully by user: ' . $user->id);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('Profile', 'do'); // Store the image in DO Spaces
          //   $image=$request->image->store('Empire/Payment','do');
            // Save image details to the user's record
            $user->profile_picture_path = 'Profile'; // Adjust as needed
            $user->profile_picture_name = basename($image);
            $user->profile_picture_location = $image;
            $user->save();
            Log::info('Image uploaded successfully by user: ' . $user->id);

            return response()->json(['success' => true, 'message' => 'User profile upload successful']);
        } else {
             Log::error('No image provided for user: ' . $user->id);
            return response()->json(['success' => false, 'message' => 'No image provided'], 400);
        }
    } catch (\Throwable $th) {
         Log::error('Server error: ' . $th->getMessage());
        return response()->json(['success' => false, 'message' => $th->getMessage() ?: 'Server error'], 500);
    }
}

}