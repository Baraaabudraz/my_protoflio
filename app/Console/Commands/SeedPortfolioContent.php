<?php

namespace App\Console\Commands;

use Database\Seeders\PortfolioSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('portfolio:seed
    {--fresh : Replace ALL existing content with the exported data}
    {--force : Skip the confirmation prompt for --fresh}')]
#[Description('Seed the portfolio content from database/seeders/data (fills empty tables; --fresh replaces everything)')]
class SeedPortfolioContent extends Command
{
    public function handle(): int
    {
        $fresh = (bool) $this->option('fresh');

        if ($fresh && ! $this->option('force')
            && ! $this->confirm('This deletes all settings, services, projects, experience and skills on this server and replaces them with the exported data. Continue?')) {
            $this->components->warn('Cancelled.');

            return self::FAILURE;
        }

        $this->components->info($fresh ? 'Replacing portfolio content…' : 'Filling missing portfolio content…');

        (new PortfolioSeeder)->setContainer($this->laravel)->setCommand($this)->__invoke(['fresh' => $fresh]);

        $this->newLine();
        $this->components->info('Done.');

        return self::SUCCESS;
    }
}
