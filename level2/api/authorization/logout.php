<?php

session_start();

// Deleting cookie
setcookie('SID', null, -1);

session_destroy();

echo json_encode(array('ok' => true));
