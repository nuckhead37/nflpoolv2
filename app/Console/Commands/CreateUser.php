<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:create-user')]
#[Description('Command description')]
class CreateUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::create([
            'name' => 'Clive',
            'email' => 'cw@elfstar.co.uk',
            'password' => Hash::make('R4id3rNat1on'),
        ]);
        User::create([
            'name' => 'Jim',
            'email' => 'chicagojab@yahoo.com',
            'password' => Hash::make('ilovethedolphins'),
        ]);
    }
}
