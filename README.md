# Chart Share
Utilidad para compartir graficos de Google Viz en Facebook o por email

### Uso

Copie el archivo fbshare.php en su servidor en una ubicacion conocida.

Agregue la porcion de javascript al HTML usando:

```html
<script src="fbshare.php?js&appid=[FBAPPID]"></script>
```
Este FBAPPID es el id de su aplicacion de FB.
Si no cuenta con una tiene que crear una en la consola de Developers de Facebook.
Si no incluye un FBAPPID, solo se incluye la parte de js necesaria para compartir el grafico por correo electronico.

Posterior a esto, para un HTML de ejemplo:
```html
<div id="chart_div"></div>
<input type="text" id="mensaje" size="30" value="Texto de la imagen...">
<a href="#" id="compartir">Compartir en FB</a>
```
Agregue el siguiente js:
```js
function compartido(e) {
	console.log("Compartir dice: " + e.result);
}
chartSetup(chart,'compartir','mensaje',compartido);
[...]
chart.draw(data, options);
```
Parametros de la funcion "chartsetup":
- objeto del chart (variable de js), 
- id del objeto que inicia de la accion compartir al recibir un click, href button etc. , 
- id del objeto del cual se va a tomar el texto del mensaje que va a tener la foto en FB,
- funcion de exito que se llama cuando se compartio el post
