<?php

return [
  /**
   * --------------------------------------------------------------------------
   *  Authentication guard
   * --------------------------------------------------------------------------
   * 
   * As for Laravel defined guards we add a new one, called 'admin'
   */
  'guards' => [
    'admin' => [
      'driver' => 'session',
      'provider' => 'admins',
    ],
  ],

  /** 
   * --------------------------------------------------------------------------
   * Admin provider
   * --------------------------------------------------------------------------
   */
  'providers' => [
    'admins' => [
      'driver'  => 'eloquent',
      'model'   => Neon\Admin\Models\Admin::class
    ]
  ],

  /**
   * --------------------------------------------------------------------------
   * Resetting passwords
   * --------------------------------------------------------------------------
   * ...same as users.
   */
  'passwords' => [
    'admins' => [
      'provider'  => 'admins',
      'table'     => 'password_resets',
      'expire'    => 60,
      'throttle'  => 60,
    ],
  ],
];
