<?php
$to = 'sididiop53@gmail.com';
$subject = 'Test Email';
$message = 'This is a test email';
$headers = 'From: sididiop5@gmail.com';

mail($to, $subject, $message, $headers);
