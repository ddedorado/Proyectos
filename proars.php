<?php
/******* HORAS: 4 ******/



require 'src/facebook.php';

function is_imgvalid($format)
{
  $i = 0;
  $arrayformat = array("jpg", "png", "gif");
  for($i = 0; $i<count($arrayformat); $i++)
  {
    if($format == $arrayformat[$i])
    {
      return true;
    }   
  }
  return false;
}

$facebook = new Facebook(array(
  'appId'  => '342792912500591',
  'secret' => '205d97159a9a781a058c494693ddf5c3'
));

//-----------------------------------COMPROBACION DE SI SE ACCEDE A LA INFORMACION DEL USUARIO-----------------------------------
$user = $facebook->getUser();

if ($user)
{
  try 
  {
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) 
  {
    error_log($e);
    $user = null;
  }
}
//---------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------LINKS PARA LOGUEO CON PERMISOS--------------------------------------------------
if($user)
{
  $logoutUrl = $facebook->getLogoutUrl();
}
else
{
  $loginUrl = $facebook->getLoginUrl(array(
    'scope' => 'read_stream, publish_stream, user_birthday, user_location, user_work_history, user_hometown, user_photos',
    ));
}
//---------------------------------------------------------------------------------------------------------------------------

// ---------------------------------COMPROBACION DEL POST PARA ENVIAR EL MENSAJE AL MURO-------------------------------------
if(isset($_POST['enviar']))
{
  if(isset($_POST['imagen']))
  {
    $img = explode(".", $_POST['imagen']);
    if(is_imgvalid($img[1]))
    {
      /*if(!file_exists("img")) //LA SUBIDA DE IMAGENES QUEDA POR REALIZARSE BIEN
      {
        mkdir("img");
      }
      move_uploaded_file($_POST['imagen'],'img/'.$_POST['imagen']);*/
      if($user)
      {
        try 
        {
          $publishStream = $facebook->api("/".$user."/feed", 'post', array(
                        'message'   => $_POST['mensaje'],
                        'picture'   => "http://ernestogamez.es/wp-content/uploads/php_logo.jpg", 
                        'name'      => $_POST['titulo']
                      ));
          $ok = true;
        }catch(FacebookApiException $e)
        {
          error_log($e);
        }
      }
      else
      {
        header("Location: ".$loginUrl);
      }
    }
  }
}
//--------------------------------------------------------------------------------------------------------------------------
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Proars</title>
  </head>
  <body>
    <?php if ($ok): echo "<h1>PROARS</h1><br />Mensaje subido al muro<br />"; else: echo "<h1>PROARS</h1><br />"; endif?>
    <form method="POST" action="">
      <label>T&iacute;tulo:</label>
      <input type="text" name="titulo" value="" /><br />
      <label>Mensaje:</label><br />
      <textarea name="mensaje" cols="50" rows="5"></textarea><br />
      <label>Imagen:</label>
      <input type="file" name="imagen" value="" /><br />
      <input type="submit" name="enviar" value="Enviar"/>
    </form>
  </body>
</html>