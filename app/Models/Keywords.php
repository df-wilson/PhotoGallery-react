<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Keywords extends Model
{
    public static function allKeywordNames()
    {
        logger("Keywords::allKeywordNames - Enter");

        return DB::select('SELECT id, name FROM keywords ORDER BY name');
    }

    public static function findOrCreateId(string $keyword)
    {
        logger("Keywords::findOrCreateId", ["Keyword" => $keyword]);

        $id = 0;

        $result = DB::select('select id from keywords where name = ?', [$keyword]);

        logger("Keywords::findOrCreateId", ["Result" => $result]);

        if(count($result)) {
            $id = $result[0]->id;
            logger("Keywords::findOrCreateId", ["Keyword ID is" => $id]);
        }

        if($id == 0) {
            logger("Adding new keyword to database");
            $keywordModel = new Keywords;
            $keywordModel->name = $keyword;
            $keywordModel->save();
            $id = $keywordModel->id;
        }

        logger("Keywords::findOrCreateId:", ["Returning ID" => $id]);
        return $id;
    }


    public static function findKeywordsForPhoto(int $photoId)
    {
        $keywords = DB::select('select keywords.id, keywords.name from keywords, photo_keywords where keywords.id = photo_keywords.keyword_id and photo_keywords.photo_id = ?', [$photoId]);
        return $keywords;
    }

    public static function addKeywordToPhoto(int $keywordId, int $photoId)
    {
        $exists = false;
        $currentDate = \Carbon\Carbon::now();

        try {
            DB::insert("INSERT INTO photo_keywords VALUES (?,?,?,?)", [$photoId, $keywordId, $currentDate, $currentDate]);
            $exists = false;
        }
        catch(\Exception $e) {
            logger("Keywords::addKeywordToPhoto", ["Exception" => $e->getMessage()]);
            $exists = true;
        }

        logger('Keywords::findKeywordsForPhoto - LEAVE', ["Exists" => $exists]);
        return $exists;
    }

    public static function removeKeywordFromPhoto(int $keywordId, int $photoId)
    {
        logger('Keywords::removeKeywordFromPhoto - ENTER', ["Photo Id" => $photoId, "Keyword Id" => $keywordId]);

        $deleted = DB::delete("DELETE FROM photo_keywords WHERE photo_id = :photo_id AND keyword_id = :keyword_id",
                              ["keyword_id" => $keywordId, "photo_id" => $photoId]);

        logger('Keywords::removeKeywordFromPhoto - LEAVE', ["Deleted" => $deleted]);
        return $deleted;
    }
}
