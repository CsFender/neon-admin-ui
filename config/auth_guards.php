<?php

return [

  /**
   * --------------------------------------------------------------------------
   *  Authentication guard
   * --------------------------------------------------------------------------
   * 
   * As for Laravel defined guards we add a new one, called 'admin'
   */
  'admin' => [
    'driver' => 'session',
    'provider' => 'admins',
  ],
];
