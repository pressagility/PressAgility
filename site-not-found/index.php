<?php
$staticDirPath = dirname(__FILE__).'/static/';
$cssFilePath = $staticDirPath.'style.css';
$imageFilepath = $staticDirPath.'logo-white.png'; //site logo-white for white body background or logo-black for black body background.


$imageData = base64_encode( file_get_contents($imageFilepath) );
$imageSrc = 'data: '.mime_content_type($imageFilepath).';base64,'.$imageData;
?>

<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site not found</title>

    <style>
      <?php include( $cssFilePath );?>
    </style>


    <meta name="robots" content="noindex, follow">
  </head>

  <body>
    <div class="display-table">
      <div class="display-table-cell">
      
        <img class="logo" src="<?php echo $imageSrc; ?>">
        
        <h2>This website is either suspended or under construction.</h2>
        
        <p>
          If you think is an error please contact at
          <a href="#">Your Network Website Address</a>
        </p>
        
        <p>
          <small>Edit "site-not-found" directory in your "WPScalePRO" Git Repository to update this template.</small>
        </p>

      </div>
    </div>

  </body>
</html>