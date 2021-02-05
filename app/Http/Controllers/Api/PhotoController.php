<?php

namespace App\Http\Controllers\Api;

use App\Models\Keywords;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image;

class PhotoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $userId = Auth::id();

        logger("Api/PhotoController::index ENTER", ["User Id" => $userId]);

        $photos = Photo::getAllForUser($userId);

        logger("Api/PhotoController::index LEAVE");

        return $photos;
    }

    public function delete(int $photoId)
    {
        $userId = Auth::id();

        logger("Api/PhotoController::delete - Enter", ["User Id" => $userId, "Photo Id" => $photoId]);

        $msg = "There was a server error.";
        $code = 500;

        if($userId)
        {
            if(Photo::isUserPhotoOwner($userId, $photoId))
            {
                Keywords::removeAllPhotoKeywords($photoId);
                $photo = Photo::find($photoId);
                if($photo)
                {
                    logger("Api/PhotoController::delete - Photo filepath ", ["Filepath" => $photo->filepath]);

                    // Delete file and thumbnail.
                    $filepath = str_replace("/storage", "/public", $photo->filepath);
                    if(Storage::exists($filepath))
                    {
                        Storage::delete($filepath);
                    }
                    else
                    {
                        logger()->error("Photo does not exist.", ["Photo" => $filepath]);
                    }

                    $filepath = str_replace("/storage", "/public", $photo->thumbnail_filepath);
                    if(Storage::exists($filepath))
                    {
                        Storage::delete($filepath);
                    }

                    // Delete database record of photo
                    $photo->delete();

                    $msg = "Photo deleted.";
                    $code = 200;
                }
                else
                {
                    $msg = "Photo not found.";
                    $code = 404;
                }
            }
            else
            {
                $msg = "You don't have permission to delete that photo.";
                $code = 400;
            }
        }
        else
        {
            $msg = "You must be logged in to delete photos.";
            $code = 400;
        }

        logger("Api/PhotoController::delete - Leave", ["HTTP Code" => $code, "Message" => $msg]);

        return response()->json(['msg' => 'photo deleted'], $code);
    }

    public function getAllPublic()
    {
        logger("Api/PhotoController::getAllPublic - ENTER");

        $photos = Photo::getAllPublic();

        return $photos;
    }

    public function getKeywordsForPhoto(int $photoId)
    {
        logger("Api/PhotoController::getKeywordsForPhoto - ENTER",
            ["Photo Id" => $photoId]);

        $keywords = [];

        if(Auth::id()) {
            $keywords = Keywords::findKeywordsForPhoto($photoId);
        }

        return response()->json(['msg' => 'ok','keywords' => $keywords], 200);
    }

    public function getNextPhoto(int $photoId)
    {
        logger("Api/PhotoController::getNextPhoto - ENTER",
            ["Photo Id" => $photoId]);

        $photo = null;
        $returnCode = 500;

        if (Auth::check()) {
            $photo = Photo::getNextForUser(Auth::id(), $photoId);
            $returnCode = 200;
        } else {
            logger("Api/PhotoController::getNextPhoto - User not authorized");
            $returnCode = 401;
        }

        logger("Api/PhotoController::getNextPhoto - LEAVE",
               ["Photo Data" => $photo]);

        return response()->json($photo, $returnCode);
    }

    public function getPreviousPhoto(int $photoId)
    {
        logger("Api/PhotoController::getPreviousPhoto - ENTER",
            ["Photo Id" => $photoId]);

        $photo = null;
        $returnCode = 500;

        if (Auth::check()) {
            $photo = Photo::getPreviousForUser(Auth::id(), $photoId);
            $returnCode = 200;
        } else {
            logger("Api/PhotoController::getNextPhoto - User not authorized");
            $returnCode = 401;
        }

        logger("Api/PhotoController::getPreviousPhoto - LEAVE",
               ["Photo Data" => $photo]);

        return response()->json($photo, $returnCode);
    }

    public function show(int $photoId)
    {
        $userId = Auth::id();

        logger("Api/PhotoController::show - ENTER",
               ["User Id" => $userId, "Photo Id" => $photoId]);

        $photo = Photo::getforUser($userId, $photoId);

        logger("Api/PhotoController::show - LEAVE");

        return response()->json($photo, 200);
    }

    public function search(Request $request)
    {
        logger("Api/PhotoController::search - ENTER", ["Data" => $request->all()]);

        $userId = Auth::id();

        if($userId == null) {
            $userId = 0;
        }

        $viewPublic = $request->public_checkbox;
        $viewPublic = $viewPublic ? true : false;

        $viewPrivate = $request->private_checkbox;
        $viewPrivate = $viewPrivate ? true : false;

        $keywordId = intval($request->keyword_id);
        logger("Keyword ID: $keywordId");

        $text = $request->text;
        if($text == null) {
            $text="";
        }

        $fromDate = $request->from_date ? $request->from_date : "";
        $toDate = $request->to_date ? $request->to_date : "";

        $photos = Photo::search($userId, $viewPublic, $viewPrivate, $fromDate, $toDate, $keywordId, $text);

        logger("Api/PhotoController::search - LEAVE", ["Photos" => $photos]);

        return response()->json(['msg' => 'ok','photos' => $photos]);
    }

    public function showForKeyword(Request $request, $keywordId)
    {
        logger("Api/PhotoController::showForKeyword: ENTER $keywordId");

        $userId = Auth::id();
        $keywordId = intval($keywordId);

        if($userId) {
            if($keywordId == 0) {
                $photos = Photo::getAllForUser($userId);
            } else {
                $photos = Photo::getforUserAndKeyword($userId, $keywordId);
            }
        } else {
            $photos = $this->showPublicForKeyword($keywordId);
        }

        return $photos;
    }

    public function showPublicForKeyword($keywordId)
    {
        logger("Api/PhotoController::showPublicForKeyword: ENTER $keywordId");

        $keywordId = intval($keywordId);

        if($keywordId == 0) {
            $photos = Photo::getAllPublic();
        } else {
            $photos = Photo::getPublicForKeyword($keywordId);
        }

        return $photos;
    }

    public function updateDescription(Request $request, $photoId)
    {
        logger("Api/PhotoController::updateDescription: ENTER $photoId");

        $code = 500;
        $message = "Server Error";
        $userId = Auth::id();
        $photo = Photo::find($photoId);

        if($photo->user_id == $userId) {
            $photo->description = $request->input("description");
            $photo->save();
            $code = 200;
            $message = "updated";
        } else {
            $code = 403;
            $message = "photo does not belong to user.";
        }

        logger("Api/PhotoController::updateDescription. LEAVE",
            ["Message" => $message, "Status Code" => $code]);

        return response($message, $code);
    }

    public function updateIsPublic(Request $request, $photoId)
    {
        logger("Api/PhotoController::updateIsPublic. ENTER", ["Photo Id" => $photoId]);

        $code = 500;
        $message = "Server Error";
        $userId = Auth::id();
        $photo = Photo::find($photoId);

        if($photo && $photo->user_id == $userId) {
            $photo->is_public = $request->input("checked");
            $photo->save();
            $code = 200;
            $message = "updated";
        } else {
            $code = 403;
            $message = "photo does not belong to user.";
        }

        logger("Api/PhotoController::updateIsPublic. LEAVE",
               ["Message" => $message, "Status Code" => $code]);

        return response($message, $code);
    }

    public function updateTitle(Request $request, $id)
    {
        logger("Api/PhotoController::updateTitle. ENTER",
               ["Photo Id" => $id]);

        $code = 500;
        $message = "Server Error";
        $userId = Auth::id();
        $photo = Photo::find($id);

        if($photo->user_id == $userId) {
            if($request->input("title")) {
                $photo->name = $request->input("title");
                $photo->save();
                $code = 200;
                $message = "updated";
            } else {
                $code = 400;
                $message = "Invalid title. Not saved.";
            }
        } else {
            $code = 403;
            $message = "photo does not belong to user.";
        }

        logger("Api/PhotoController::updateTitle. LEAVE",
            ["Message" => $message, "Status Code" => $code]);

        return response($message, $code);
    }

    public function upload(Request $request)
    {
        logger("Api/PhotoController::upload. ENTER");

        $message = "Server error.";
        $code = 500;
        $returnData = [];

        $userId = Auth::id();

        $files = $request->file('photos');
        if ($files) {
            foreach($files as $file) {
                if($file->isValid()) {
                    $name = $file->getClientOriginalName();
                    if($name) {
                        $extension = $file->extension();
                        $path = $file->storeAs('public/images', $name);
                        $path = "/storage/images/".$name;
                        $thumbnailPath = "/storage/images/thumb_".$name;

                        Image::make("./".$path)
                            ->orientate()
                            ->fit(200, 150)
                            ->save("./".$thumbnailPath);
                        Image::make("./".$path)
                            ->orientate()
                            ->save("./".$path);

                        $photo = new Photo;
                        $photo->user_id = $userId;
                        $photo->name = $name;
                        $photo->is_public = false;
                        $photo->filepath = $path;
                        $photo->thumbnail_filepath = $thumbnailPath;
                        $photo->description = "";
                        $photo->save();

                        logger("Api/PhotoController::upload. File uploaded",
                            ["User Id"=>$userId, "photo Id" => $photo->id, "Photo name" => $name]);
                        $content = ["id" => $photo->id, "fileName" => "thumb_$name", "originalName" => $name];
                        array_push($returnData, $content);
                    } else {
                        logger()->error("Api/PhotoController::upload - Photo does not have a name.");
                        $message = "Photo does not have a name.";
                        $code = 400;
                        break;
                    }
                } else {
                    logger()->error("Api/PhotoController::upload - File is not valid.");
                    $message = "Unable to upload photo. The file size is either too big, or the photo type is not supported.";
                    $code = 400;
                    break;
                }
            }

            $message = "ok";
            $code = 200;
        } else {
            logger()->error("Api/PhotoController::upload - No file in request.");
            $message = "No photos uploaded.";
            $code = 400;
        }

        logger("Api/PhotoController::upload. LEAVE", ["Message" => $message]);
        return response(["msg" => $message, "data" => $returnData], $code);
    }
}
