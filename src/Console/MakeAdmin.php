<?php

namespace Neon\Admin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Neon\Admin\Models\Admin;

class MakeAdmin extends Command
{
  /**
   * @var \Neon\Admin\Models\Admin|null
   */
  protected $admin        = null;

  protected $signature    = 'make:admin';

  protected $description  = 'Add NEON administer user.';

  public function handle()
  {
    $data = [];
    $data['name']      = $this->ask('What is the admin\'s name?');
    $data['email']     = $this->ask('What is the admin\'s e-mail?');
    $data['password']  = $this->secret('What will be the password to protect?');
    
    if ($this->confirm('Is this information correct?')) {

      $validator = Validator::make($data, [
        'name'      => 'required',
        'email'     => 'required|email|unique:admins,email',
        'password'  => 'required'
      ]);

      if ($validator->fails()) {
        $this->info('Can\'t create admin user:');
    
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }
        return 1;
      }

      $this->admin = new Admin();
      $this->admin->name      = $data['name'];
      $this->admin->email     = $data['email'];
      $this->admin->password  = Hash::make($data['password']);
      $this->admin->save();
    }
  }

}