<?php

namespace App\Http\Controllers\Api;

use App\Models\Keywords;
use App\Models\Photo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KeywordController extends Controller
{
    public function __construct()
    {

    }

    public function getAll()
    {
        logger("KeywordController::getAll - Enter");
        
        $code = 500;
        
        return response()->json([
            'msg' => 'ok',
            'keywords' => Keywords::allKeywordNames()]);
    }

    public function addPhotoKeyword(Request $request, int $photoId)
    {
        logger("KeywordController::addPhotoKeyword - Enter", ["Photo Id" => $photoId]);

        $code = 500;
        $message = "server error.";
        $keywordId = 0;

        if (Auth::check()) {
            $userId = Auth::id();

            $photo = Photo::find($photoId);
            if($photo && $photo->user_id == $userId) {
                $keyword = mb_strtolower($request->keyword);

                if($keyword) {
                    $keywordId = Keywords::findOrCreateId($keyword);
                    logger("KeywordController::addPhotoKeyword - $keywordId.");

                    $exists = Keywords::addKeywordToPhoto($keywordId, $photoId);

                    if($exists) {
                        $message = 'exists';
                        $code = 200;
                    } else {
                        $message = 'ok';
                        $code = 201;
                    }
                } else {
                    $code = 400;
                    $message = "keyword required";
                }
            } else {
                $code = 403;
                $message = "photo does not belong to user.";
            }
        } else {
            $code = 401;
            $message = "not authorized";
        }

        logger("KeywordController::addPhotoKeyword - Leave", ["Message" => $message, "Code" => $code]);

        return response()
            ->json(
                [
                    'msg' => $message,
                    'keyword_id' => $keywordId
                ],
                $code);
    }

    public function removePhotoKeyword(Request $request, int $keywordId, int $photoId)
    {
        logger("KeywordController::removePhotoKeyword - ENTER", ["Keyword Id" => $keywordId, "Photo Id" => $photoId]);

        $message = "ok";
        $code = 200;
        $userId = 0;

        if (Auth::check()) {

            $isOwner = Photo::isUserPhotoOwner(Auth::id(),$photoId);

            if($isOwner) {
                $numDeleted = Keywords::removeKeywordFromPhoto($keywordId, $photoId);

                if($numDeleted == 0) {
                    $code = 404;
                    $message = "keyword or photo not found";
                }
            } else {
                $message = "not authorized";
                $code = 401;
            }
        } else {
            $message = "not authorized";
            $code = 401;
        }

        logger("KeywordController::removePhotoKeyword - LEAVE", ["Message" => $message, "Return code" => $code]);

        return response()
            ->json(
                [
                    'msg' => $message
                ],
                $code);
    }
    
    public function store(Request $request)
    {
        $this->saveKeyword(mb_strtolower($request->name));
    }

    private function saveKeyword(string $keyword)
    {
        $lowercase_name = mb_strtolower($keyword);

        if(!Keywords::exists($lowercase_name))
        {
            $keyword = new Keywords;
            $keyword->name = $lowercase_name;
            $keyword->save();
        }
    }
}
