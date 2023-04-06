<?php

return [

  /**
   * --------------------------------------------------------------------------
   * Resetting passwords
   * --------------------------------------------------------------------------
   * ...same as users.
   */
  'admins' => [
    'provider'  => 'admins',
    'table'     => 'password_resets',
    'expire'    => 60,
    'throttle'  => 60,
  ],
];
