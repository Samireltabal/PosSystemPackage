<?php

namespace Synciteg\PosSystem\Commands;

use Illuminate\Console\Command;

class PosSystemCommand extends Command
{
    public $signature = 'syncit:pos';

    public $description = 'Pos System Package';

    public function handle(): int
    {
      $this->info('Starting..');
      $this->comment('With Comment');
      $this->error('With Error');
      $this->info('And All Done');

        return self::SUCCESS;
    }
}