<?php
/******* HORAS: 6 ******/



require 'src/facebook.php';

$ok = false;
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
  if(isset($_FILES['imagen']))
  {
    if(is_uploaded_file($_FILES['imagen']['tmp_name']))
    {
      if(!file_exists("img")) 
      {
        mkdir("img");
      }
      if($_FILES['imagen']['type']=="image/jpeg" || $_FILES['imagen']['type']=="image/png" || $_FILES['imagen']['type']=="image/gif" || $_FILES['imagen']['type']=="image/jpeg")
      { 
        $path = "img/".$_FILES['imagen']['name'];
        if(move_uploaded_file($_FILES['imagen']['tmp_name'], $path))
        {
          if($user)
          {
            try 
            {
              $publishStream = $facebook->api("/".$user."/feed", 'post', array(
                            'message'   => $_POST['mensaje'],
                            'picture'   => "http://localhost:8080/img/".$_FILES['imagen']['name'],//"http://ernestogamez.es/wp-content/uploads/php_logo.jpg", 
                            'name'      => $_POST['titulo']
                          ));
              $ok = true;
            }catch(FacebookApiException $e)
            {
              echo error_log($e);
            }
          }
          else
          {
            header("Location: ".$loginUrl);
          }  
        }
      }
      else echo "La Imagen no es valida.";
    }
  }
}
//--------------------------------------------------------------------------------------------------------------------------
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Proars</title>
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/form.css">
  </head>
  <body>
    <div id="main">
      <h1>PROARS</h1><br />
      <form method="POST" action="" enctype="multipart/form-data">
        <label>T&iacute;tulo:</label>
        <input type="text" name="titulo" value=""  id="titulo" /><br />
        <label>Mensaje:</label><br />
        <textarea id="comment" name="mensaje" cols="50" rows="5"></textarea><br />
        <label>Imagen:</label>
        <input type="file" name="imagen" /><br />
        <input type="submit" name="enviar" value="Enviar"/>
      </form>
      <?php if($ok) echo '<div id="ok"><p>Se ha compartido en su muro</div>'; ?>
    </div>
  </body>
</html>
