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
		$data = default_value($_POST,"imgtoremove","NOTDEF");
		if ( $data === "NOTDEF" ) {
			echo "";
		} else {
			$link_array = explode('/',$data);
    		$fdata = end($link_array);
			if(unlink($uploaddir . $fdata)){
				//echo "Deleted " . $fdata;
				echo "";
			} else {
				echo "-1";
			}
		}
	} else {
		$rnd = generateRandomString();
		$uploadfile = $uploaddir . $rnd . '.png';
		$image = explode('base64,',$data); 
		if (file_put_contents($uploadfile, base64_decode($image[1]))) {
		  echo str_replace("fbshare.php", "", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")."images/$rnd.png";
		} else {
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
?>			log("FBShare error config: edit the appropiate configuration values."); <?php
		}
?>
/*
var script = document.createElement('script');
script.src = "base64min.js";
document.getElementsByTagName('script')[0].parentNode.appendChild(script);
*/
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

function log(t){
	if ( debug ) {
		console.log(t);
	}
}

function fbPost(m,i,s){
	FB.api('/me/photos', 'post', {
		message:m,
		url:i        
	}, 
	function(response){
		if (!response || response.error) {
			log('FBShare: Error sharing, ' + response.error.message);
			s({ result : 'error', msg : 'FBShare: Error sharing, ' + response.error.message });
		} else {
			log('FBShare: Post done, ID#' + response.id);
			s({ result : 'success', msg : 'FBShare: Post done, ID#' + response.id });
			$.ajax(
			{
				url: 'fbshare.php', 
				type: 'POST',
				datatype: 'text',
				data: { imgtoremove : i }
			})
			.done(function(e){
					if ( e === "-1" ) { 
						log("FBShare error: something wrong happened deleting the temp image.");
					} else {
						log("FBShare: temporary image removed, " + e);
					}
			});
		}
	});
}

function shareImage(i,m,s){
	$.ajax(
	{
		url: 'fbshare.php', 
		type: 'POST',
		datatype: 'text',
		data: { imgtosave : i }
	})
	.done(function(e){
		if ( e === "-1" ) { 
			log("FBShare error: something wrong happened uploading the image.");
			s({ result : 'error', msg : 'FBShare: something wrong happened uploading the image.' });
		} else {
			var imgURL=e;
			var msg=$('#'+m).val().trim();
			FB.getLoginStatus(function(response) {
			  if (response.status === 'connected') {
			  	fbPost(msg,imgURL,s);
			  }
			  else {
			    FB.login(function(){
			    	fbPost(msg,imgURL,s);
			    }, {scope: 'publish_actions'});
			}});
		}
	});
}
// c - chart object
// t - objeto que inicia de la accion compartir al recibir un click, href button etc. 
// m - objeto del cual se va a tomar el texto del mensaje que va a tener la foto en FB
// s - funcion de exito que se llama cuando se compartio el post
function chartSetup(c,t,m,s,d){
	debug = d;

	google.visualization.events.addListener(
		c, 'ready', 
		function(){
			$('#'+t).click(function(){
				shareImage(c.getImageURI(),m,s);
			});
		});
}
<?php
}}
?>
