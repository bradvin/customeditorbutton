<?php
  global $current_user;
  get_currentuserinfo();

?>

<html>
  <body>
    <h2>Hi, <?php echo $current_user->display_name; ?>, I am an iframe!</h2>
    <p>This does not really do anything yet...</p>
  </body>
</html>