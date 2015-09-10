<?php

/*
 * Utility
 */

class Util {

    const KEY_COMMAND = 'bytemeup!';

    public static function formatCurrency($number) {
        return Yii::app()->numberFormatter->formatCurrency($number, "IDR");
    }

    /**
     * @param string $emailAddress
     * @return mixed if true integer otherwise null
     */
    public static function saveEmailAddress($emailAddress) {
        $emailID = null;
        $parseEmail = explode('@', strtolower(trim($emailAddress)));
        if (count($parseEmail) == 2) {
            $domain = Domain::model()->findByAttributes(array('name' => $parseEmail[1]));
            if (!$domain) {
                $domain = new Domain;
                $domain->name = $parseEmail[1];
            }
            if ($domain->save()) {
                $email = new Email;
                $email->username = $parseEmail[0];
                $email->domainID = $domain->ID;
                if ($email->save()) {
                    $emailID = $email->ID;
                } else {
                    if ($domain->isNewRecord) {
                        Domain::model()->deleteByPk($domain->ID);
                    }
                }
            }
        }
        return $emailID;
    }

    /**
     * Set PHP Configuration
     */
    public static function setPHPConfig() {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '600');
    }

    /**
     * Convert text to readable URL
     * @param string $text
     * @return string
     */
    public static function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        return $text;
    }

    /**
     * Strip BBCode to text plain
     * @param string $text
     * @return string
     */
    public static function stripBBCode($text) {
        $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
        $replace = '';
        return preg_replace($pattern, $replace, $text);
    }

    /**
     * Get formatted date
     * @param string $time A date/time string. Valid formats are explained in Date and Time Formats.
     * @return string date label
     */
    public static function formatDate($time) {
        $label = '';
        $formatDate = date('Y-m-d', strtotime($time));
        if ($formatDate == date('Y-m-d')) {
            $label = sprintf('%s %s', Yii::t('app', 'Today'), date('H:i', strtotime($time)));
        } elseif ($formatDate == date('Y-m-d', strtotime('-1 day'))) {
            $label = sprintf('%s %s', Yii::t('app', 'Yesterday'), date('H:i', strtotime($time)));
        } else {
            $label = date('d-m-Y H:i', strtotime($time));
        }
        return $label;
    }

    /**
     * Count twitter, facebook, google+
     * @param string $url
     * @return integer
     */
    public static function totalSocialShare($url) {
        $totalShare = 0;
        if (!YII_DEBUG) {
            $totalShare += Util::totalFacebookShare($url);
            $totalShare += Util::totalTwitterShare($url);
            $totalShare += Util::totalGooglePlusOne($url);
        }
        return $totalShare;
    }

    /**
     * Count facebook like
     * @param string $url
     * @return integer
     */
    public static function totalFacebookLike($url) {
        $statusURLInFacebook = YII_DEBUG ? 0 : Util::statusURLInFacebook($url);
        return isset($statusURLInFacebook['like_count']) ? intval($statusURLInFacebook['like_count']) : 0;
    }

    /**
     * Count Google+ +1's
     * @param string $url
     * @return integer
     */
    public static function totalGooglePlusOne($url) {
        $statusURLInGooglePlus = YII_DEBUG ? 0 : Util::statusURLInGooglePlus($url);
        return isset($statusURLInGooglePlus['result']['metadata']['globalCounts']['count']) ? intval($statusURLInGooglePlus['result']['metadata']['globalCounts']['count']) : 0;
    }

    /**
     * Count facebook share
     * @param string $url
     * @return integer
     */
    public static function totalFacebookShare($url) {
        $statusURLInFacebook = YII_DEBUG ? 0 : Util::statusURLInFacebook($url);
        return isset($statusURLInFacebook['share_count']) ? $statusURLInFacebook['share_count'] : 0;
    }

    /**
     * Count twitter share
     * @param string $url
     * @return integer
     */
    public static function totalTwitterShare($url) {
        $tweetShare = YII_DEBUG ? 0 : Util::statusURLInTwitter($url);
        return isset($tweetShare['count']) ? $tweetShare['count'] : 0;
    }

    /**
     * Count google+ share
     * @param string $url
     * @return integer
     */
    public static function totalGoogleShare($url) {
//        $simpleHTML = SimpleHTMLDOM::file_get_html("https://plus.google.com/ripple/details?url=$url", false, null, 0, 10000);
//        $wrapperElement = $simpleHTML->find('table.Ezc td.wJb div.rMa > div');
//        $count = isset($wrapperElement[0]) ? preg_replace('/\D/', '', $wrapperElement[0]->text()) : 0;
//        return is_numeric($count) ? intval($count) : 0;
        return 0;
    }

    /**
     * get tweet player
     * @param integer $tweetID
     * @param string[] $params
     * @return string[]
     */
    public static function getTwitterTweet($tweetID, $params = array()) {
        $rest = new RESTClient;
        $rest->initialize(array('server' => 'https://api.twitter.com/'));
        $rest->option(CURLOPT_VERBOSE, true);
        $rest->option(CURLOPT_RETURNTRANSFER, true);
        $rest->option(CURLOPT_SSL_VERIFYPEER, false);
        $rest->option(CURLOPT_SSL_VERIFYHOST, false);
        $tweet = $rest->get('1/statuses/oembed.json', array_merge($params, array('id' => $tweetID)));
        $tweetRest = CJSON::decode($tweet);
        return $tweetRest;
    }

    /**
     * get user timeline
     * @param string $screenName
     * @param integer $count
     * @param string[] $params
     * @return Object
     */
    public static function getTwitterTimeline($screenName, $count = 1, $params = array()) {
        Yii::import('application.extensions.twitteroauth.TwitterOAuth.*');

        $consumerKey = Yii::app()->params['oauth']['twitter']['id'];
        $consumerKeySecret = Yii::app()->params['oauth']['twitter']['key'];
        $accessToken = Yii::app()->params['oauth']['twitter']['accessToken'];
        $accessTokenSecret = Yii::app()->params['oauth']['twitter']['accessTokenSecret'];

        $connection = new TwitterOAuth($consumerKey, $consumerKeySecret, $accessToken, $accessTokenSecret);
        $params = array_merge($params, array(
            'screen_name' => $screenName,
            'count' => $count
        ));
        $timelineRest = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json", $params);
        return !is_object($timelineRest) && isset($timelineRest[0]) ? $timelineRest[0] : false;
    }

    /**
     * Get Twitter rest API status
     * @param string $url shared URL 
     * @return string[] response data
     */
    public static function statusURLInTwitter($url) {
        $rest = new RESTClient;
        $rest->initialize(array('server' => 'https://cdn.api.twitter.com/'));
        $rest->option(CURLOPT_VERBOSE, true);
        $rest->option(CURLOPT_RETURNTRANSFER, true);
        $rest->option(CURLOPT_SSL_VERIFYPEER, false);
        $rest->option(CURLOPT_SSL_VERIFYHOST, false);
        $twitter = $rest->get('1/urls/count.json', array('url' => $url));
        $twitterStatus = CJSON::decode($twitter);
        return $twitterStatus;
    }

    /**
     * Get Facebook rest API status
     * @param string $url
     * @return string[]
     */
    public static function statusURLInFacebook($url) {
        $rest = new RESTClient;
        $rest->initialize(array('server' => 'https://api.facebook.com/'));
        $rest->option(CURLOPT_SSL_VERIFYPEER, false);
        $rest->option(CURLOPT_SSL_VERIFYHOST, false);
        $facebook = $rest->get('method/links.getStats', array('urls' => $url, 'format' => 'json'));
        $facebookStatus = CJSON::decode($facebook);
        return isset($facebookStatus[0]) ? $facebookStatus[0] : array();
    }

    /**
     * Get Google Plus rest API status
     * @param string $url
     * @return string[]
     */
    public static function statusURLInGooglePlus($url) {
        $rest = new RESTClient;
        $rest->initialize(array('server' => 'https://clients6.google.com/'));
        $rest->set_header('Content-type', 'application/json');
        $rest->option('RETURNTRANSFER', true);
        $rest->option(CURLOPT_SSL_VERIFYPEER, false);
        $rest->option(CURLOPT_SSL_VERIFYHOST, false);
        $googlePlus = $rest->post('rpc', '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode($url) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
        $googlePlusStatus = CJSON::decode($googlePlus);
        return isset($googlePlusStatus[0]) ? $googlePlusStatus[0] : array();
    }

    /**
     * Get Google Analytics page views rest API status
     * @param string $url make sure the url is not absolute. it just path url
     *                    based on your hostname.
     * @return integer
     */
    public static function viewsURLInAnalytics($url) {
        $rest = new RESTClient;
        $rest->initialize(array('server' => 'https://content.googleapis.com/analytics/v3/data/'));
        $rest->set_header('Content-type', 'application/json');
        $rest->option('RETURNTRANSFER', true);
        $rest->option(CURLOPT_SSL_VERIFYPEER, false);
        $rest->option(CURLOPT_SSL_VERIFYHOST, false);
        $response = $rest->get('ga', [
            'ids' => ('ga:94751193'),
            'dimensions' => ('ga:pagePath'),
            'metrics' => ('ga:pageviews'),
            'filters' => ("ga:pagePath==$url"),
            'start-date' => '2014-11-01',
            'end-date' => date('Y-m-d'),
            'access_token' => 'ya29.4AD5ChEwzkLc8TBe3pmlKERuXJtQlcttN7WB_Bao95UeUxLeslkFjFoDIJkHAlvmeWTUU6r1pj71YQ',
            'max-results' => '50',
            '_src' => 'explorer',
        ]);
        $json = CJSON::decode($response);
        return $json['totalsForAllResults']['ga:pageviews'];
    }

    /**
     * Get Youtube rest API status
     * @param string $youtubeID
     * @return string[]
     */
    public static function youtubeRest($youtubeID) {
        $rest = new RESTClient;
        $rest->initialize(array('server' => 'http://gdata.youtube.com/'));
        $youtube = $rest->get("feeds/api/videos/$youtubeID", array('v' => '2', 'alt' => 'jsonc'));
        $youtubeInformation = CJSON::decode($youtube);
        return isset($youtubeInformation['data']) ? $youtubeInformation['data'] : array();
    }

    /**
     * Get soundcloud rest API from URL
     * @param type $url
     * @return type
     */
    public static function soundcloudRest($url) {
        $rest = new RESTClient;
        $rest->initialize(array('server' => 'http://api.soundcloud.com/'));
        $rest->option('SSL_VERIFYPEER', false);
        $soundcloud = $rest->get('resolve.json', array('url' => $url, 'client_id' => Yii::app()->params['soundcloud']['clientID']));
        return CJSON::decode($soundcloud);
    }

    /**
     * Generate random string
     * @param integer $length
     * @return string
     */
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * Get youtube video ID
     * @param type $id
     * @return youtube video ID
     */
    public static function getYoutubeVideoID($url) {
        $url_string = parse_url($url, PHP_URL_QUERY);
        parse_str($url_string, $args);
        return isset($args['v']) ? $args['v'] : false;
    }

    /**
     * Character Limiter
     *
     * Limits the string based on the character count.  Preserves complete words
     * so the character count may not be exactly as specified.
     *
     * @access   public
     * @param    string
     * @param    integer
     * @param    string  the end character. Usually an ellipsis
     * @return   string
     */
    public static function characterLimiter($string, $n = 500, $end_char = '&#8230;') {
        $shorten = strip_tags(Util::stripBBCode($string));
        if (strlen($shorten) > $n) {
            $shorten = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $shorten));

            if (strlen($shorten) > $n) {
                $out = "";
                foreach (explode(' ', trim($shorten)) as $val) {
                    $out .= $val . ' ';

                    if (strlen($out) >= $n) {
                        $out = trim($out);
                        if (strpos($out, ' ') === false) {
                            $out = substr($out, 0, $n);
                        }
                        $shorten = (strlen($out) == strlen($shorten)) ? $out : $out . $end_char;
                        break;
                    }
                }
            }
        }

        return $shorten;
    }

    /**
     * Generate Random Password
     */
    public static function generatePassword($length = 12) {
        $string = '';
        $chars = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $countChar = count($chars);
        for ($i = 1; $i <= $length; $i++) {
            shuffle($chars);
            $index = rand(0, $countChar - 1);
            $string .= isset($chars[$index]) ? $chars[$index] : '.';
        }
        return $string;
    }

}

?>