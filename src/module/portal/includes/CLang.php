<?php
/* spPortalSystem CLang.php
 * Created on 19.05.2009 from misterice
 *
 * spPortalSystem was written by Daniel Stecker 2009
 * please visit my website www.sploindy.de
 *
 * This file is part of spPortalSystem.
 * spPortalSystem is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or any later version.
 *
 * spPortalSystem is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
final class CLang{
	public static $instance = null;

	private function __clone(){
	}

	protected function __construct(){
	}

	/**
	 * /brief give class instance
	 * @param $g_system
	 * @return object
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new CLang();
		}
		return self::$instance;
	}

	/**
	 * \brief Get installed langs from database
	 * \details 
	 * Load installed language array
	 * 
	 * @param $db
	 * @return langArray or false
	 */
	public function getInstalledLangs($db){
		$sql 	= "SELECT * FROM sp_modul_portal_language ORDER BY `id` ASC";
		$result = $db->query($sql);
		if($result && $db->num_rows($result) > 0){
			while($res = $db->fetch_assoc($result)){
				$backRes[$res['id']] = $res;
			}
			return $backRes;
		}else{
			return false;
		}
	}

	/**
	 * \brief Give avaible langs to install
	 * \details
	 * Check lang flags and check in csv file for avaible languages
	 * 
	 * @return $langArray
	 */
	public function getAvaibleLangs(){
		$contentArray = array();
		if ($handle = opendir(SP_CORE_DOC_ROOT .'/module/portal/template/images/flagicons')) {
			while (false !== ($file = readdir($handle))) {
				if (substr_count($file,".png") > 0) {
					$langArray[substr($file,0,(int)strpos($file,"."))]['value']	= substr($file,0,(int)strpos($file,"."));
					$langArray[substr($file,0,(int)strpos($file,"."))]['select']= "";
					$langArray[substr($file,0,(int)strpos($file,"."))]['text']	= substr($file,0,(int)strpos($file,"."));
				}
			}
			closedir($handle);
		}
		$handle = fopen (SP_CORE_DOC_ROOT .'/module/portal/lang/flag.csv','r');
		while ( ($data = fgetcsv ($handle, 1000, ",")) !== FALSE ) {
			if(isset($langArray[strtolower($data[0])]['text'])){
				$langArray[strtolower($data[0])]['text'] = $data[1];
			}
		}
		fclose ($handle);
		return $langArray;
	}

	/**
	 * \brief Delete language
	 * \detail
	 * Delete language support for selected language. <br>
	 * System Language can not be delete.
	 * @param $db
	 * @param $id
	 */
	public function delLanguage($db, $id){
		$sql = "SELECT lang_lang FROM sp_modul_portal_language WHERE `id` = ".$db->checkValue($id)." LIMIT 1";
		$result = $db->query($sql);
		if($result && $db->num_rows($result) > 0){
			$res = $db->fetch_assoc($result);
			if($res['lang_lang'] != SP_CORE_LANG ){
				$sql = "DELETE FROM `sp_modul_portal_language` WHERE `id` = ".$db->checkValue($id)." LIMIT 1";
				$db->query($sql);
				$sql = "UPDATE `sp_modul_portal_cms_menue_main_side` SET lang_id = 0 WHERE lang_id = ".$db->checkValue($id);
				$db->query($sql);
			}
		}
	}

	/**
	 * \brief Set Language for the Portal
	 * \details 
	 * Write avaible portal lang into database
	 * 
	 * @param $db
	 * @param $lang_lang
	 * @param $lang_name
	 */
	public function setLanguage($db, $lang_lang, $lang_name){
		$sql = "INSERT INTO sp_modul_portal_language (lang_lang, lang_name) VALUES ('".$db->checkValue($lang_lang)."', '".$db->checkValue($lang_name)."')";
		$db->query($sql);
	}

	/**
	 * \brief 
	 * Get user language
	 * \details
	 * Found this function on http://de.php.net/manual/en/function.http-negotiate-language.php
	 * 
	 * @param $available_languages
	 * @param $http_accept_language
	 * @return unknown_type
	 */
	public function prefered_language ($available_languages,$http_accept_language="auto") {
		// if $http_accept_language was left out, read it from the HTTP-Header
		if ($http_accept_language == "auto"){
			$http_accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}

		// standard  for HTTP_ACCEPT_LANGUAGE is defined under
		// http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
		// pattern to find is therefore something like this:
		//    1#( language-range [ ";" "q" "=" qvalue ] )
		// where:
		//    language-range  = ( ( 1*8ALPHA *( "-" 1*8ALPHA ) ) | "*" )
		//    qvalue         = ( "0" [ "." 0*3DIGIT ] )
		//            | ( "1" [ "." 0*3("0") ] )
		preg_match_all("/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?" .
                   "(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i",
		$http_accept_language, $hits, PREG_SET_ORDER);

		// default language (in case of no hits) is the first in the array
		$bestlang = $available_languages[0];
		$bestqval = 0;

		foreach ($hits as $arr) {
			// read data from the array of this hit
			$langprefix = strtolower ($arr[1]);
			if (!empty($arr[3])) {
				$langrange = strtolower ($arr[3]);
				$language = $langprefix . "-" . $langrange;
			}
			else $language = $langprefix;
			$qvalue = 1.0;
			if (!empty($arr[5])) $qvalue = floatval($arr[5]);
			 
			// find q-maximal language
			//$languageprefix = null;
			if (is_array($available_languages) && in_array($language,$available_languages) && ($qvalue > $bestqval)) {
				$bestlang = $language;
				$bestqval = $qvalue;
			}
			// if no direct hit, try the prefix only but decrease q-value by 10% (as http_negotiate_language does)
			else if (is_array($available_languages) && in_array($langprefix,$available_languages) && (($qvalue*0.9) > $bestqval)) {
				$bestlang = $langprefix;
				$bestqval = $qvalue*0.9;
			}
		}
                //die( $bestlang);
		return $bestlang;
	}

}
?>