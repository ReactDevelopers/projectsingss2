<?php

namespace App\Console\Commands;

// Can't extend DuskCommand if we're on anything
// other than local or testing environments.
// So just return early so we don't fail.

if (app()['env'] != 'testing' && app()['env'] != 'local') {
    return false;
}

use RuntimeException;
use App\Support\ProcessBuilder;
use Laravel\Dusk\Console\DuskCommand;
use Symfony\Component\Process\Process;

class DuskServeCommand extends DuskCommand
{
    // Credit: https://medium.com/@deleugpn/running-serve-automatically-prior-to-laravel-dusk-9eedf295bbd6

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application and run Dusk tests';

    /**
     * @var Process
     */
    protected $serve;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Snippets copied from DuskCommand::handle()
        $this->purgeScreenshots();

        $this->purgeConsoleLogs();

        return $this->withDuskEnvironment(function () {
            // Start the Web Server AFTER Dusk handled the environment, but before running PHPUnit
            $serve = $this->serve();

            // Run PHP Unit
            return $this->runPhpunit();
        });
    }

    /**
     * Snippet copied from DuskCommand::handle() to actually run PHP Unit
     *
     * @return int
     */
    protected function runPhpunit() {
        $options = array_slice($_SERVER['argv'], 2);

        $process = (new ProcessBuilder())
            ->setTimeout(null)
            ->setPrefix($this->binary())
            ->setArguments($this->phpunitArguments($options))
            ->getProcess();

        try {
            $process->setTty(true);
        } catch (RuntimeException $e) {
            $this->output->writeln('Warning: ' . $e->getMessage());
        }

        return $process->run(function ($type, $line) {
            $this->output->write($line);
        });
    }

    /**
     * Build a process to run php artisan serve
     *
     * @return Process
     */
    protected function serve() {
        // Compatibility with Windows and Linux environment
        $arguments = [PHP_BINARY, 'artisan', 'serve'];

        // Build the process
        $serve = (new ProcessBuilder($arguments))
            ->setTimeout(null)
            ->getProcess();

        return tap($serve, function (Process $serve) {
            $serve->start(function ($type, $line) {
                $this->output->writeln($line);
            });
        });
    }
}
