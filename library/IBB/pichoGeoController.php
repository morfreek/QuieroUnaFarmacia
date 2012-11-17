<?php
class IBB_pichoGeoController{

    public function __construct(){
		$ip = $this->getIP();
		$d = file_get_contents("http://api.hostip.info/get_html.php?ip=".$ip);
       
       $pattern = '/City:(.*)/';
               preg_match($pattern, $d,$coincidencias);
               //echo $coincidencias[1];
		
		$this->city = $coincidencias[1];
		
				
	}
	 
	// Para obtener la IP del visitante
	private function getIP(){
		if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if( isset( $_SERVER ['HTTP_VIA'] ))  $ip = $_SERVER['HTTP_VIA'];
		else if( isset( $_SERVER ['REMOTE_ADDR'] ))  $ip = $_SERVER['REMOTE_ADDR'];
		else $ip = null ;
		return $ip;
	}
	 
	// Para obtener la IP del propio servidor
	/*function ownIP(){
	 $ip= file_get_contents('http://myip.eu/');
	 $ip= substr($ip,strpos($ip,'<font size=5>')+14);
	 $ip= substr($ip,0,strpos($ip,'<br'));
	 return $ip;
	}
	 
	// Obtiene la información y la muestra
	$rec= locateIp(getIP());
	 
	// es posible que la API de Google necesite una key. Solicitar en
	// http://code.google.com/intl/ca/apis/maps/signup.html
	// y ubicar la clave tras "&key=CLAVE"
	echo '<img style="border:1px solid black;" src="http://maps.google.com/staticmap?center='.$rec[latitude].','.$rec[longitude].'&markers='.$rec[latitude].','.$rec[longitude].',tinyblue&zoom=11&size=200x200&key=" />
	',$rec['city'], ',', $rec['country_code'];
	*/
}