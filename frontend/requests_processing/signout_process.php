<?php
session_start();
session_unset();
session_destroy();
echo '<html><head><meta http-equiv="refresh" content="0;url=../universalsuccess.php?success=signout"></head></html>';
?>