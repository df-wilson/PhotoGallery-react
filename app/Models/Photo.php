<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Photo extends Model
{
    public static function getAllForUser($userId)
    {
        logger("Enter Photo::getAllForUser");
        return DB::select('select photos.id, photos.name, photos.description, photos.thumbnail_filepath, photos.filepath from photos, users where user_id=? and users.id = photos.user_id order by photos.created_at',[$userId]);
    }

    public static function getAllPublic()
    {
        logger("Photo::getAllPublic - ENTER");
        return DB::select('select photos.id, photos.name, photos.description, photos.thumbnail_filepath, photos.filepath from photos where is_public=1 order by photos.created_at');
    }

    public static function getForUser(int $userId, int $photoId)
    {
        logger("Photo::getForUser - ENTER", ["User Id" => $userId, "PhotoId" => $photoId]);
        $photo = [];
        $result = DB::select('select photos.id, photos.name, photos.description, photos.filepath, photos.is_public from photos, users where photos.id=? and (photos.user_id =? or photos.is_public = 1) and users.id = photos.user_id order by photos.created_at',[$photoId, $userId]);

        if(count($result)) {
            $photo = $result[0];
        }
        return $photo;
    }
    
    public static function getNextForUser(int $userId, int $photoId)
    {
        $photo = [];
        $result = DB::select('select photos.id, photos.name, photos.description, photos.filepath, photos.is_public from photos, users where photos.id>? and photos.user_id =? and users.id = photos.user_id order by photos.id', [$photoId, $userId]);

        if(count($result)) {
            $photo = $result[0];
        }

        return $photo;
    }

    public static function getPreviousForUser(int $userId, int $photoId)
    {
        $photo = [];
        $result = DB::select('select photos.id, photos.name, photos.description, photos.filepath, photos.is_public from photos, users where photos.id<? and photos.user_id =? and users.id = photos.user_id order by photos.id DESC', [$photoId, $userId]);

        if(count($result)) {
            $photo = $result[0];
        }

        return $photo;
    }

    public static function getForUserAndKeyword(int $userId, int $keywordId)
    {
        $photo = [];
        $result = DB::select('SELECT photos.id, photos.name, photos.description, photos.thumbnail_filepath
                              FROM photos, users, photo_keywords
                              WHERE users.id = photos.user_id AND photos.id = photo_keywords.photo_id AND users.id = ? AND photo_keywords.keyword_id = ?
                              ORDER BY photos.created_at',
                              [$userId, $keywordId]);

        if(count($result)) {
            $photo = $result;
        }
        return $photo;
    }

    public static function getPublic($photoId)
    {
        $photo = null;

        $photoId = intval($photoId);

        $result = DB::select('SELECT photos.id, photos.name, photos.description, photos.filepath
                              FROM photos
                              WHERE photos.id = ? AND is_public=1',
                              [$photoId]);
        if(count($result)) {
            $photo = $result[0];
        }

        return $photo;
    }

    public static function isUserPhotoOwner(int $userId, int $photoId)
    {
        $isOwner = false;

        $result = DB::select("SELECT 1 FROM photos WHERE id = :photo_id AND user_id = :user_id",
                             ["photo_id" => $photoId, "user_id" => $userId]);

        if($result) {
            $isOwner = true;
        } else {
            $isOwner = false;
        }

        return $isOwner;
    }

    public static function search(int $userId, bool $publicPhotos, bool $ownPhotos, string $startDate, string $endDate, int $keywordId, string $text)
    {
        logger("Photos::search - ENTER", ["userId" => $userId, "Public Photos" => $publicPhotos, "Private Photos" => $ownPhotos, "Start Date" => $startDate, "End Date" => $endDate, "Keyword Id" => $keywordId, "Text" => $text]);

        $photos = [];
        $inputs = [];
        $whereClause = "";

        if($keywordId) {
            logger("Photos::search - Searching for keywords.");
            $whereClause = "photo_keywords.keyword_id = :keyword_id";
            $inputs += ['keyword_id' => $keywordId];
        }

        if($text && strlen($text) > 1) {
            logger("Photos::search - Searching for text.");
            $existingWhereClause = false;

            if($whereClause) {
                $whereClause = "(".$whereClause;
                $whereClause .= " OR ";
                $existingWhereClause = true;
            }
            $whereClause .= "(description LIKE :text OR name = :text)";

            if($existingWhereClause) {
                $whereClause .= ")";
            }
            $inputs += ['text' => "%$text%"];
        }

        if($startDate || $endDate) {
            logger("Photos::search - Add date range to where clause.");
            if($whereClause) {
                $whereClause .= " AND ";
            }

            if($startDate && $endDate) {
                $whereClause .= "photos.created_at BETWEEN :start_date AND :end_date";
                $inputs += ['start_date' => $startDate, 'end_date' => $endDate];

            } else if($startDate) {
                $whereClause .= "photos.created_at >= :start_date";
                $inputs += ['start_date' => $startDate];
            } else if($endDate) {
                $whereClause .= "photos.created_at <= :end_date";
                $inputs += ['end_date' => $endDate];
            } else {
                Log::error("Photos::search - Error determining start and end dates.");
            }
        }

        if($publicPhotos == true && $ownPhotos == true) {
            // Get public and private photos
            if($whereClause) {
                $whereClause .= " AND ";
            }
            $whereClause .= "(is_public = :is_public OR user_id = :user_id)";
            $inputs += ['is_public' => true, 'user_id' => $userId];
        } else if ($publicPhotos == true || $userId == 0) {
            // Public photos only
            logger("Photos::search - Public photos only.");
            if($whereClause) {
                $whereClause .= " AND ";
            }
            $whereClause .= "is_public = :is_public";
            $inputs += ['is_public' => true];
        }
        else if ($ownPhotos === true) {
            logger("Photos::search - Own photos only.");
            if($whereClause) {
                $whereClause .= " AND ";
            }
            $whereClause .= "user_id = :user_id";
            $inputs += ['user_id' => $userId];
        } else {
            Log::error("Photos::search - Error determining public or own photos.");
            return [];
        }

        $sql = "SELECT DISTINCT photos.id, photos.name, photos.description, photos.thumbnail_filepath
                FROM photos
                JOIN photo_keywords ON photos.id = photo_keywords.photo_id
                WHERE $whereClause";

        logger("Photo::search - ", ["sql" => $sql, "inputs" => $inputs]);

        $photos = DB::select($sql, $inputs);

        logger("Photo::search - LEAVE", ["photos" => $photos]);

        return $photos;
    }

    public static function getPublicForKeyword(int $keywordId)
    {
        $photo = [];
        $result = DB::select('SELECT photos.id, photos.name, photos.description, photos.thumbnail_filepath
                              FROM photos, photo_keywords
                              WHERE photos.id = photo_keywords.photo_id AND photos.is_public=1 AND photo_keywords.keyword_id = ?
                              ORDER BY photos.created_at',
            [$keywordId]);

        if(count($result)) {
            $photo = $result;
        }
        return $photo;
    }
}
