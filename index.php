<?php

// index.php

date_default_timezone_set('Europe/Paris');

session_start();


/*

// Waiting Room

if (!isset($_SESSION['start_time']) || (time() - $_SESSION['start_time'] < 5)) {
    if (!isset($_SESSION['start_time'])) {
        $_SESSION['start_time'] = time();
    }
    header("Refresh: 5");
    echo 
<<<ASCII
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
<center><pre>
Veuillez patienter... Please Wait... Espere por Favor...

 ____    __  _  __                    
|  _ \  /_/ (_) \_\_     __   ___   _ 
| | | |/ _ \| |/ _` |____\ \ / / | | |
| |_| |  __/| | (_| |_____\ V /| |_| |
|____/ \___|/ |\__,_|      \_/  \__,_|
          |__/                        
       _                        
       \`*-.                    
        )  _`-.                 
       .  : `. .                
       : _   '  \               
       ; *` _.   `*-._          
       `-.-'          `-.       
         ;       `       `.     
         :.       .        \    
         . \  .   :   .-'   .   
         '  `+.;  ;  '      :   
         :  '  |    ;       ;-. 
         ; '   : :`-:     _.`* ;
[bug] .*' /  .*' ; .*`- +'  `*' 
      `*-*   `*-*  `*-*'

“Les impatients arrivent toujours trop tard.”
</pre></center>
ASCII;
    exit();
}

*/

// Core :

require_once 'core/Helpers.php';
require_once 'core/Router.php';
require_once 'core/Database.php';
require_once 'core/Model.php';
require_once 'core/View.php';
require_once 'core/Controller.php';
require_once 'core/Flash.php';
require_once 'core/Function.php';
require_once 'core/Redis.php';
require_once 'core/Language.php';

// Base :

anti_injection_sql();

require_once 'controllers/BaseController.php';
require_once 'models/BaseModel.php';

// Libraries :
// --------------------------
require_once 'libraries/Parsedown.php';
require_once 'libraries/Captcha.php';
// --------------------------

// Route :

$router = new Router();
$router->run();
