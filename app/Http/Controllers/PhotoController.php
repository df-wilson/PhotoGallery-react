<?php
namespace App\Http\Controllers;

use App\Keywords;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoController extends Controller
{
    public function __construct()
    {
    }

    public function home()
    {
        logger("PhotoController::home");

        if (Auth::check()) {
            return view('photos.photo-manager');
        } else {
            return redirect('/login');
        }
    }

    public function react()
    {
        logger("PhotoController::react");

        if (Auth::check()) {
            return view('photos.photo-manager');
        } else {
            return redirect('/login');
        }
    }
    
    public function search()
    {
        return view('photos.search');
    }

    public function searchSubmit(Request $request)
    {
        logger("PhotoController::searchSubmit ENTER", ["Data" => $request]);

        $keywordId = $request->keyword_id;

        $viewPublic = $request->public_checkbox;
        $viewPublic = $viewPublic ? true : false;

        $viewPrivate = $request->private_checkbox;
        $viewPrivate = $viewPrivate ? true : false;

        $text = $request->text_search;
        if($text == null) {
            $text="";
        }

        $fromDate = $request->from_date ? $request->from_date : "";
        $toDate = $request->to_date ? $request->to_date : "";

        return view('photos.search-results',
                    [
                        'keywordId' => $keywordId,
                        'text' => $text,
                        'publicPhotos' => $viewPublic,
                        'privatePhotos' => $viewPrivate,
                        'fromDate' => $fromDate,
                        'toDate'   => $toDate
                    ]);
    }
    
    public function explore()
    {
        return view('photos.explore');
    }

    public function show($id)
    {
        logger("PhotoController::show ENTER", ["ID" => $id]);

        if (Auth::check()) {
            $userId = Auth::id();
            $photo = Photo::getForUser($userId, $id);

            if($photo) {
                $keywords = Keywords::findKeywordsForPhoto($id);
                logger("Keywords are ", ["Keywords" => $keywords]);
                return view('photos.single',
                            [
                                'name' => $photo->name,
                                'description' => $photo->description,
                                'src' => $photo->filepath,
                                'id' => $photo->id,
                                'is_public' => $photo->is_public,
                                'keywords' => $keywords
                            ]);

            } else {
                return "Photo not found.";
            }
        } else {
            return redirect('/login');
        }
    }

    public function showNext($photoId)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $photo = Photo::getNextForUser($userId, $photoId);

            // If the next photo doesn't exist, show the current one
            if(!$photo) {
                $photo = Photo::getForUser($userId, $photoId);
            }

            $keywords = Keywords::findKeywordsForPhoto($photo->id);
            logger("Keywords are ", ["Keywords" => $keywords]);
            return view('photos.single',
                        [
                            'name' => $photo->name,
                            'description' => $photo->description,
                            'src' => $photo->filepath,
                            'id' => $photo->id,
                            'is_public' => $photo->is_public,
                            'keywords' => $keywords
                        ]);
        } else {
            return redirect('/login');
        }
    }

    public function showPhotosWithKeyword(int $keywordId)
    {
        if (Auth::check()) {
            $userId = Auth::id();

            return view('photos.search-results',
                [
                    'keywordId' => $keywordId,
                    'text' => "",
                    'publicPhotos' => false,
                    'privatePhotos' => true,
                    'fromDate' => "",
                    'toDate'   => ""
                ]);
        } else {
            return redirect('/login');
        }
    }

    public function showPrev($photoId)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $photo = Photo::getPreviousForUser($userId, $photoId);

            // If the previous photo doesn't exist, show the current one
            if(!$photo) {
                $photo = Photo::getForUser($userId, $photoId);
            }

            $keywords = Keywords::findKeywordsForPhoto($photo->id);
            logger("Keywords are ", ["Keywords" => $keywords]);
            return view('photos.single',
                        ['name' => $photo->name,
                            'description' => $photo->description,
                            'src' => $photo->filepath,
                            'id' => $photo->id,
                            'is_public' => $photo->is_public,
                            'keywords' => $keywords
                        ]);
        } else {
            return redirect('/login');
        }
    }

    public function showPublicPhoto($id)
    {
        $id = intval($id);
        $photo = Photo::getPublic($id);

        if($photo) {
            logger("PhotoController::showPublicPhoto - ENTER", ["Photo" => $photo]);
            $keywords = Keywords::findKeywordsForPhoto($id);
            return view('photos.public-single',
                        ['name' => $photo->name, 'description' => $photo->description, 'src' => $photo->filepath, 'id' => $photo->id, 'keywords' => $keywords]);
        } else {
            return "Photo not found.";
        }
    }
}

