<?
if(! function_exists('errorMessageHandler')) {
  function errorMessageHandler($errorCode, $exceptionType = 'Exception') {
    $messages = array(
			'MALFORMED_SIGN' => 'QB error message: Parameters in the function are not valid...'
    );
    throw new $exceptionType(isset($messages[$errorCode]) ? $messages[$errorCode] : 'Unknown QB error code: ' . $errorCode);
  }
}

if(! function_exists('paramExistsInAlloweds')) {
  function paramExistsInAlloweds($param, $alloweds) {
    if(array_search($param, $alloweds, true) === false) {
      return false;
    }
    return true;
  }
}
?>