<?php
///////////////////////////////
// FACEBOOK SHARE UTILS PLUGIN
// DAVID OBANDO
// WWW.ITROS.NET
//////////////////////////////

error_reporting(E_ALL);

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
	$rnd = generateRandomString();
	$uploadfile = $uploaddir . $rnd . '.png';

	$data = $_POST['imgtosave']; 
	$image = explode('base64,',$data); 
	if (file_put_contents($uploadfile, base64_decode($image[1]))) {
	  echo str_replace("fbshare.php", "", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")."images/$rnd.png";
	} else {
	   echo "-1";
	}
} else {
// Handle GET Request
	$id = default_value($_GET,"id","NOTDEF");
	$js = default_value($_GET,"js","NOTDEF");
	$appid = default_value($_GET,"appid","NOTDEF");
	
	if ($js === "") {
		header('Content-type: text/javascript');
		if ($appid === "NOTDEF") {
?>			console.error("FBShare error: missing Facebook APP ip, pass it with 'passid'"); <?php
		}
?>
var script = document.createElement('script');
script.src = "base64min.js";
document.getElementsByTagName('script')[0].parentNode.appendChild(script);

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
js.src = "//connect.facebook.net/en_US/sdk.js";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function base64ToBlob(base64, mime) 
{
    mime = mime || '';
    var sliceSize = 1024;
    var byteChars = Base64.decode(base64);
    var byteArrays = [];

    for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
        var slice = byteChars.slice(offset, offset + sliceSize);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    }

    return new Blob(byteArrays, {type: mime});
}

function shareImage(i){

	$.ajax({
		url: 'fbshare.php', 
		type: "POST",
		datatype: 'text',
		data: {
		        imgtosave : i
		    }
	})
	.done(function(e){
		if ( e === "-1" ) { 
			console.error("fbshare: oooops, something happened uploading the image");
		} else {
			var imgURL=e;
			FB.getLoginStatus(function(response) {
			  if (response.status === 'connected') {
			    FB.api('/me/photos', 'post', {
				message:'Aqui logrando logros si',
				url:imgURL        
				}, function(response){
				if (!response || response.error) {
				console.error('FB error: ' + response.error.message);
				} else {
				console.log('Post ID: ' + response.id);
				alert("Se ha compartido el grafico en FB");
				}
				});
			  }
			  else {
			    FB.login(function(){}, {scope: 'publish_actions'});
			  }
			});
		}
	});
}

function chartSetup(c,d,t){
	if (t === undefined) {
		t = '<a href="#" class="share-graph">Compartir</a>';
	}
	google.visualization.events.addListener(
		c, 'ready', 
		function(){
			$(t).insertAfter($("#"+d));
			$('.share-graph').click(function(){
				shareImage(c.getImageURI());
				/*FB.ui({
					method: 'share',
					href: 'https://itros.net/sandbox/facebook-share/fbshare.php?id=436'
				}, function(response){});*/
			});
		});
}
<?php
	}
}

/*
<head>

<meta property="og:url" content="itros.net" />
<meta property="og:type" content="website" />
<meta property="og:image" content="https://itros.net/sandbox/facebook-share/grafico.png" />
<meta property="og:image:type" content="image/png" />
<meta property="og:image:width" content="204" />
<meta property="og:image:height" content="153" />

</head>
<html>
</html>
*/
?>
