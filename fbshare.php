<?php
///////////////////////////////
// FACEBOOK SHARE UTILS PLUGIN
// DAVID OBANDO
// WWW.ITROS.NET
//////////////////////////////

//error_reporting(E_ALL);

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function default_value($arr, $var, $default) {
    if (isset($arr[$var])) {
    	$t = $arr[$var] ? $default : $arr[$var];
    	return $t === NULL ? $default : $arr[$var];
    } else {
    	return $default;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Handle POST Request

	$uploaddir = getcwd().'/images/';
	if (!file_exists($uploaddir)) {
	    mkdir($uploaddir, 0775, true);
	}

	$data = default_value($_POST,"imgtosave","NOTDEF");
	
	if ( $data === "NOTDEF" ) {
		echo "";
	} else {
		$rnd = generateRandomString();
		$uploadfile = $uploaddir . $rnd . '.png';
		$image = explode('base64,',$data); 
		if (file_put_contents($uploadfile, base64_decode($image[1]))) {
		  echo str_replace("fbshare.php", "", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")."images/$rnd.png";
		} else {
			error_log("FBShare: Error creating the image on the server, file: $uploadfile.");
		   	echo "-1";
		}
	}
} else {
// Handle GET Request
	$js = default_value($_GET,"js","NOTDEF");
	$appid = default_value($_GET,"appid","NOTDEF");
	
	if ($js === "") {
		header('Content-type: text/javascript');
		
		if ($appid === "") {
			//error_log("FBShare error config: edit the appropiate configuration values.");
			echo "console.log('FBShare not enabled, error in the config, missing app ID');";
		} else {
?>
window.fbAsyncInit = function() {
    FB.init({
      appId      : '<?php echo $appid; ?>',
      xfbml      : true,
      version    : 'v2.7'
    });
  };
(function(d, s, id){
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) {return;}
js = d.createElement(s); js.id = id;
js.src = "//connect.facebook.net/es_LA/sdk.js";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


/*
urlImg:
message:
success:
*/
function fbPost(urlImg,message,success){
	FB.api('/me/photos', 'post', {
		message:message,
		url:urlImg        
	}, 
	function(response){
		if (!response || response.error) {
			s({ result : 'error', msg : 'fbPost: Error compartiendo, ' + response.error.message });
		} else {
			s({ result : 'success', msg : 'fbPost: Post completado, ID#' + response.id });
		}
	});
}

/*
baseImg:
message: element ID
success:
*/
function fbUploadShare(baseImg,message,success){
	$.ajax(
	{
		url: 'fbshare.php', 
		type: 'POST',
		datatype: 'text',
		data: { imgtosave : baseImg }
	})
	.done(function(e){
		if ( e === "-1" ) { 
			s({ result : 'error', msg : 'fbUploadShare: something wrong happened uploading the image.' });
		} else {
			var imgURL=e;
			var msg=$('#'+message).val().trim();
			fbPost(imgURL,msg,success);
		}
	});
}

/*
 chart - chart object
 button - objeto que inicia de la accion compartir al recibir un click, href button etc. 
 msg - objeto del cual se va a tomar el texto del mensaje que va a tener la foto en FB
 success - funcion de exito que se llama cuando se compartio el post
*/
function fbShare(chart,button,msg,success){
	fbconnected = false;
	FB.getLoginStatus(function(response) {
		fbconnected = (response && response.status == 'connected');
	});
	google.visualization.events.addListener(
		chart, 'ready', 
		function(){
			$('#'+button).click(function(){
					if (fbconnected) {
						fbUploadShare(chart.getImageURI(),msg,success);
					} else {
						FB.login(function(){
							fbUploadShare(chart.getImageURI(),msg,success);
						}, {scope: 'publish_actions'});
					}
			});
	});
}
<?php
		} // fb share area
// email share js
// TODO
// email share end   
?>
    
<?php
	} // js script end
}
?>
