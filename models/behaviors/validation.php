<?php
class ValidationBehavior extends ModelBehavior { 
	var $settings = array();
	
	// エンコードの設定
	var $encoding;
	
	/**
	 * 初期化
	 * 
	 * @access public
	 * @author sakuragawa
	 */
	public function setup(&$model, $config = array()) { 
		$this->settings = $config;

		// エンコード
		if(isset($config['encoding'])){
			$this->encoding = $config['encoding'];
		}else{
			$this->encoding = Configure::read('App.encoding');
		}
	}
	
	/**
	 * 日付チェック
	 *
	 * @access public
	 * @param $model モデル(cakeがセット)
	 * @param $data  データ
	 * @param $format YMD:年月日でチェック、YM：年月でチェック
	 * @param $valid バリデーション情報(cakeがセット)
	 * @author sakuragawa
	 */
	public function checkdate(&$model, $data, $format, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if(is_null($val) || empty($val)){
			// NULLはチェックしない
			return true;
		}
		
		if($format == 'YM'){
			// YMの場合は検証用に[-01]をつける
			$val .= '-01';
		}
		
		$rcode = true;
		$reg = "/^[0-9]{4}[\-\/][0-9]{1,2}[\-\/][0-9]{1,2}$/";
		
		if(preg_match($reg, $val)){
			$ret = explode("-", $val);
			if(count($ret) == 3){
				$year = intval($ret[0]);
				$month = intval($ret[1]);
				$day = intval($ret[2]);
				if(checkdate($month, $day, $year) === false){
					// 日付が不正
					$rcode = false;
				}
			}else{
				// 入力形式が正しくない.(フォーマット異常)
				$rcode = false;
			}
		}else{
			// 入力形式が正しくない.(フォーマット異常)
			$rcode = false;
		}
		
		return $rcode;
	}


	/**
	 * 文字数チェック(一致)
	 *
	 * @access protected
	 * @param $model モデル(cakeがセット)
	 * @param $data  データ
	 * @param $checkLen チェック文字数
	 * @param $valid バリデーション情報(cakeがセット)
	 * @author sakuragawa
	 * @return true：一致、false：一致しない
	 */
	public function isLengthEqual(&$model, $data, $checkLen, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		$len = mb_strlen($val, $this->encoding);
		if($checkLen == $len){
			return true;
		}else{
			return false;
		}
		
		return false;
	}
	
	/**
	 * 文字数チェック(大きい)
	 *
	 * @access protected
	 * @author sakuragawa
	 * @param $model モデル(cakeがセット)
	 * @param $data  データ
	 * @param $checkLen チェック文字数
	 * @param $valid バリデーション情報(cakeがセット)
	 * @return 大きい：true、小さい：false
	 */
	public function isLengthGreater(&$model, $data, $checkLen, $equal, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		$len = mb_strlen($val, $this->encoding);
		if($equal === true){
			// 以上
			if($len >= $checkLen){
				return true;
			}else{
				return false;
			}
		}else if($equal === false){
			// より上
			if($len > $checkLen){
				return true;
			}else{
				return false;
			}
		}else{
			echo sprintf("The argument is illegal. <br>Function : %s<br>Line : %s", __FUNCTION__, __LINE__);
			exit;
		}
	}
	
	/**
	 * 文字数チェック(小さい)
	 *
	 * @access protected
	 * @author sakuragawa
	 * @param $model モデル(cakeがセット)
	 * @param $data  データ
	 * @param $checkLen チェック文字数
	 * @param $valid バリデーション情報(cakeがセット)
	 * @return 小さい：true、大きい：false
	 */
	public function isLengthLess(&$model, $data, $checkLen, $equal, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		$len = mb_strlen($val, $this->encoding);
		if($equal === true){
			// 以下
			if($len <= $checkLen){
				return true;
			}else{
				return false;
			}
		}else if($equal === false){
			// より下
			if($len < $checkLen){
				return true;
			}else{
				return false;
			}
		}else{
			echo sprintf("The argument is illegal. <br>Function : %s<br>Line : %s", __FUNCTION__, __LINE__);
			exit;
		}
	}
	
	/**
	 * 半角英数字
	 *
	 * @access public
	 * @param $model モデル(cakeがセット)
	 * @param $data  データ
	 * @param $valid バリデーション情報(cakeがセット)
	 * @author sakuragawa
	 */
	public function isAlphaNumeric(&$model, $data, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		if(preg_match("/^[0-9a-zA-Z]+$/u", $val)){
			// 半角英数
			return true;
		}else{
			// 以外
			return false;
		}
	}
	
	/**
	 * 半角数値チェック
	 *
	 * @access public
	 * @param $model モデル(cakeがセット)
	 * @param $data  データ
	 * @param $valid バリデーション情報(cakeがセット)
	 * @author sakuragawa
	 */
	function isNumeric(&$model, $data, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}		
		
		if(preg_match("/^\d+$/u", $val)){
			// 半角数値
			return true;
		}else{
			// 以外
			return false;
		}
	}
	
	/**
	 * 半角英字かチェック
	 *
	 * @access public
	 * @author sakuragawa
	 * @param $model モデル(cakeがセット)
	 * @param $data  データ
	 * @param $valid バリデーション情報(cakeがセット)
	 * @return
	 */
	function isAlpha(&$model, $data, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		if(preg_match("/^[a-zA-Z]+$/u", $val)){
			// 半角英字である
			return true;
		}else{
			// 以外
			return false;
		}
	}
	
	
	/**
	 * ひらがなチェック
	 *
	 * @access public
	 * @author sakuragawa
	 */
	function isHiragana(&$model, $data, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		if(preg_match("/^[ぁ-ゞー]+$/u",$data[$key])){
			// ひらがなである
			return true;
		}else{
			// 以外
			return false;
		}
	}
	
	/**
	 * カタカナチェック
	 *
	 * @access public
	 * @author sakuragawa
	 */
	function isKana(&$model, $data, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		if(preg_match("/^[ｦ-ﾟァ-ヶー]+$/u",$data[$key])){
			// カタカナである
			return true;
		}else{
			// 以外
			return false;
		}
	}
	
	/**
	 * 全角カタカナチェック
	 *
	 * @access public
	 * @author sakuragawa
	 */
	function isZenKana(&$model, $data, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		if(preg_match("/^[ァ-ヶー]+$/u",$data[$key])){
			// 全角カナ
			return true;
		}else{
			// 以外
			return false;
		}
	}
	
	/**
	 * 半角カタカナチェック
	 *
	 * @access public
	 * @author sakuragawa
	 */
	function isHanKana(&$model, $data, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		if(preg_match("/^[ｦ-ﾟ ]+$/u",$data[$key])){
			// 半角カナ
			return true;
		}else{
			// 以外
			return false;
		}
	}
	
	
	/**
	 * 電話番号のチェック
	 * 
	 * @access public
	 * @author sakuragawa
	 */
	public function checkTel(&$model, $data, $valid = null){
		$key = key($data);
		$val = $data[$key];
		
		if($val == ''){
			// 空は許可
			return true;
		}
		
		if(preg_match("/^[\d\-]+$/u",$data[$key])){
			// 半角カナ
			return true;
		}else{
			// 以外
			return false;
		}
	}
	
	/**
	* ファイルアップロードエラーチェック
	* （エラーのみチェックする[サイズ上限超えは別チェック]）
	*
	* @access public
	* @author sakuragawa
	*/
	function checkRequireFileError(&$model, $data, $valid = null){
		$key = key($data);
		
		$error = $data[$key]['error'];
		if($error == UPLOAD_ERR_NO_FILE){
			// ファイルはアップロードされていない
			return false;
		}else{
			// ファイルはアップロードされている
			return true;
		}
	}
	
	
	/**
	* ファイルアップロードエラーチェック
	* （エラーのみチェックする[サイズ上限超えは別チェック]）
	*
	* @access public
	* @author sakuragawa
	*/
	function checkFileError(&$model, $data, $valid = null){
		$key = key($data);
		
		$error = $data[$key]['error'];
		if($error == UPLOAD_ERR_NO_FILE){
			// ファイルはアップロードされていない
			return true;
		}
		
		switch($error)
		{
			case UPLOAD_ERR_OK:
				// アップロードOK
				return true;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				// サイズが上限を超えた
				return true;
			case UPLOAD_ERR_PARTIAL:		// 1部のみアップロード
			case UPLOAD_ERR_NO_TMP_DIR:		// テンポラリフォルダなし
			case UPLOAD_ERR_CANT_WRITE:		// 書き込み失敗
				return false;
		}
	}
	
	
	/**
	 * ファイルアップロードサイズチェック
	 * （サイズ上限超えチェック[エラーは別チェック]）
	 *
	 * @access public
	 * @author sakuragawa
	 */
	function checkFileSize(&$model, $data, $valid = null){
		$key = key($data);
		
		$error = $data[$key]['error'];
		if($error == UPLOAD_ERR_NO_FILE){
			// ファイルはアップロードされていない
			return true;
		}
		
		switch($error)
		{
			case UPLOAD_ERR_OK:
				// アップロードOK
				return true;
			case UPLOAD_ERR_INI_SIZE:		// php.iniのファイルサイズ
			case UPLOAD_ERR_FORM_SIZE:		// MAX_FILE_SIZEを超えている
				// サイズが上限を超えた
				return false;
			case UPLOAD_ERR_PARTIAL:		// 1部のみアップロード
			case UPLOAD_ERR_NO_TMP_DIR:		// テンポラリフォルダなし
			case UPLOAD_ERR_CANT_WRITE:		// 書き込み失敗
				return true;
		}
	}
	
	
	/**
	 * ファイルの拡張子チェック
	 *
	 * @access public
	 * @author sakuragawa
	 */
	function checkFileExt(&$model, $data, $extList, $valid = null){
		$key = key($data);
		
		$error = $data[$key]['error'];
		if($error == UPLOAD_ERR_NO_FILE){
			// ファイルはアップロードされていない
			return true;
		}
		
		$name = $data[$key]['name'];
		$info = pathinfo($name);
		$upFileExt = $info['extension'];
		
		foreach($extList as $ext){
			if(up($upFileExt) == up($ext)){
				// 拡張子一致
				return true;
			}
		}
		// 拡張子一致しない
		return false;
	}
} 
?>