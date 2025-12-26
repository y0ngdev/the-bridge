<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    |
    | Email address to send database backups to. Falls back to the default
    | mail from address if not specified.
    |
    */

    'backup_mail_to' => env('BACKUP_MAIL_TO', env('MAIL_FROM_ADDRESS')),

    /*
    |--------------------------------------------------------------------------
    | Birthday Notifications
    |--------------------------------------------------------------------------
    |
    | Email address to send birthday notifications to. Falls back to the
    | default mail from address if not specified.
    |
    */

    'birthday_notification_email' => env('BIRTHDAY_NOTIFICATION_EMAIL', env('MAIL_FROM_ADDRESS')),

];
