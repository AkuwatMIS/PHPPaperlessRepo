<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 14/09/17
 * Time: 11:13 AM
 */

namespace common\components\Helpers\ReportsHelper;


use DateTime;
use DateTimeZone;

class StringHelper
{
    static function getRandom($length = 38)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    static function getMealShareUrl($id)
    {
        return 'http://eatwithalfred.com/guest/?id=' . $id;
    }

    static function getFbProfile($user)
    {

        $user_details = "https://graph.facebook.com/me?fields=id,name,cover&access_token=" . $user->fb_token;

        $response = file_get_contents($user_details);
        $response = json_decode($response, true);

        if (isset($response['id'])) {
            $user->fb_id = isset($response['id']) ? ($response['id']) : 0;
            $user->fb_username = isset($response['name']) ? ($response['name']) : '';
            $user->fb_cover_url = isset($response['cover']['source']) ? ($response['cover']['source']) : '';
        }

        return $user;
    }

    static function getGmtTime($meal, $type = "to")
    {
        $location = !empty($meal->location_address)? GeoHelper::geoCode($meal->location_address) : '';

        if ($location) {
            $meal->location_latitude = $location['lat'];
            $meal->location_longitude = $location['long'];
        }

        if ($meal->meal_timezone) {
            $local = new DateTimeZone($meal->meal_timezone);
            if ($type == "from") {
                if ($date = new DateTime($meal->meal_date . ' ' . $meal->time_from, $local)) {
                    $date->setTimezone(new DateTimeZone("GMT"));
                    return $date->format('Ymd\\THi00\\Z');
                }
            } else {
                if ($date = new DateTime($meal->meal_date . ' ' . $meal->time_to, $local)) {
                    $date->setTimezone(new DateTimeZone("GMT"));
                    return $date->format('Ymd\\THi00\\Z');
                }
            }
        }

        return true;
    }

    static public function getYoutubeVimeoLink($videoLink)
    {
        $embed = '';
        if (strpos($videoLink, 'youtube') !== false) {
            if (preg_match('/https:\/\/(?:www.)?(youtube).com\/watch\\?v=(.*?)/', $videoLink)) {
                $embed = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "http://www.youtube.com/embed/$1", $videoLink);
            }
        } else if (strpos($videoLink, 'vimeo') !== false) {
            if (preg_match('/https:\/\/vimeo.com\/(\\d+)/', $videoLink, $regs)) {
                $embed = 'http://player.vimeo.com/video/' . $regs[1];
            }
        } else {
            return false;
        }
        return $embed;
    }

}