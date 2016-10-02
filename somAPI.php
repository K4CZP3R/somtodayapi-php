<?php
/**
 * @author Kacper Serewis <k4czp3r.dev@gmail.com>
 * @version 1.1.0
 * @date 02-Okt-2016
 *
 * Working at 02-10-2016
 */

class somtodayapi
{
    /**
     * @param $string - Converts String to Hex (needed for password encoding)
     * @return string - Converted string (hex)
     */
    private function strToHex($string)
    {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $ord = ord($string[$i]);
            $hexCode = dechex($ord);
            $hex .= substr('0' . $hexCode, -2);
        }
        return strToUpper($hex);
    }

    /**
     * @param $password - password as plain text
     * @return string - encoded password for somtoday servers
     */
    private function encodePassword($password)
    {
        return strtolower($this->strToHex(base64_encode(sha1($password, true))));
    }

    public $username;
    public $password;
    public $ePassword;
    public $leerlingId;
    public $brin;
    public $afkorting;
    public $baseURL;

    /**
     * @param $username - your email/username
     * @param $password - your password for somtoday (plain-text)
     * @param $schoolafkorting - you can find it at https://servers.somtoday.nl
     * @param $brin - you can find it at https://servers.somtoday.nl
     */
    function __construct($username, $password, $schoolafkorting, $brin)
    {
        $this->username = $username;
        $this->password = $password;
        $this->ePassword = $this->encodePassword($password);
        $this->brin = $brin;
        $this->afkorting = $schoolafkorting;
        $this->login($username, $this->ePassword, $brin, $schoolafkorting);

    }

    /**
     * You don't need to call this function
     */
    private function login()
    {
        $this->baseURL = $this->createBaseURL();
        $valURL = $this->getValidationURL();
        $resp = file_get_contents($valURL);
        $jsonresp = json_decode($resp, true);
        if ($jsonresp["error"] == "FAILED_AUTHENTICATION") {
            die("Entered credentials may be wrong!");
        } else if ($jsonresp["error"] != "SUCCESS") {
            die("Undefined error occurred " . $jsonresp["error"]);
        } else {
            $this->leerlingId = $jsonresp["leerlingen"][0]["leerlingId"];
        }
    }

    /**
     * @return array (json) or raw respond from somtoday servers
     */
    public function getGrades()
    {
        $gradesURL = $this->getResultatenURL();
        $resp = file_get_contents($gradesURL);
        $jsonresp = json_decode($resp, true);
        return array("json" => $jsonresp,
            "raw" => $resp);
    }

    /**
     * @param $daysahead - How much days ahead
     * @return array (json) or raw respond from somtoday servers
     */
    public function getHomework($daysahead)
    {
        $homeworkURL = $this->getHomeworkURL($daysahead);
        $resp = file_get_contents($homeworkURL);
        $jsonresp = json_decode($resp,true);
        return array("json" => $jsonresp,
            "raw" => $resp);
    }

    /**
     * @return string - base of all somtoday requests
     */
    private function createBaseURL(){
        $servicesSuffix="services/mobile/v10/";
        return "https://www.somtoday.nl/".$this->afkorting.'/'.$servicesSuffix;
    }

    /**
     * Don't need to call this function
     * @param $timestamp - how much times ahead
     * @return string - url which contains request to get homework from somtoday servers
     */
    private function getHomeworkURL($timestamp)
    {
        $afsprakenServiceSuffix = "Agenda/";
        $getAgendaServiceCall = "GetMultiStudentAgendaHuiswerkMetMaxB64";
        return $this->baseURL.$afsprakenServiceSuffix.$getAgendaServiceCall."/".base64_encode($this->username)."/".$this->ePassword."/".$this->brin."/".$timestamp."/".$this->leerlingId;

    }

    /**
     * Don't need to call this function
     * @return string - url which contains request to login into somtoday servers
     */
    private function getValidationURL(){
        $validateLoginServiceSuffix="Login/";
        $checkLoginServiceCall="CheckMultiLoginB64";
        return $this->baseURL.$validateLoginServiceSuffix.$checkLoginServiceCall."/".base64_encode($this->username)."/".$this->ePassword."/".$this->brin;
    }

    /**
     * @return string - url which contains request to get grades from somtoday servers
     */
    private function getResultatenURL(){

        $resultatenServiceSuffix="Cijfers/";
        $getGemiddeldenServiceCall="GetMultiCijfersRecentB64";
        return $this->baseURL.$resultatenServiceSuffix.$getGemiddeldenServiceCall."/".base64_encode($this->username)."/".$this->ePassword."/".$this->brin."/".$this->leerlingId;
    }

}