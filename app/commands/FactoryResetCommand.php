<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Ballen\Executioner\Executer;
use \Setting;
use \Rule;

class FactoryResetCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'turbine:factoryreset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restores the local DB back to default settings';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ($this->confirm('Are you sure you wish to restore to factory defaults? [y/N] ', false)) {

            /**
             * Execute the migration tasks here, meaning that we only have to keep the migrations
             *  up to date and the rest should just work :)
             */
            $app_path = str_replace('/app/commands', '', dirname(__FILE__));

            // We now need to delete all configuration files from the Nginx configuration directory
            $cleanup = new NginxConfig;

            $existing_rules = Rule::all();
            foreach ($existing_rules as $rule) {
                //$cleanup->readConfig($filename)
                $cleanup->setHostheaders($rule->hostheader);
                $cleanup->deleteConfig(Setting::getSetting('nginxconfpath') . '/' . $cleanup->serverNameToFileName() . '.enabled.conf');
            }

            // We now re-build the database using the migrations...
            $execute = new Executer;
            $execute->setApplication('php')->addArgument($app_path . '/artisan')->addArgument('migrate:refresh')->addArgument('--seed')->execute();

            // and then restart the daemon as otherwise Nginx will continue to proxy traffic...
            $cleanup->reloadConfig();

            // We now generate a new API key..
            $genkey = new Executer;
            $genkey->setApplication('php')->addArgument($app_path . '/artisan')->addArgument('turbine:generatekey')->execute();

            /**
             *  Users can uncommet this code block if they want verbose factory resets!
             *
             * foreach ($execute->resultAsArray() as $outputline) {
             *    $this->info($outputline); - Users can uncommet this if they want verbose factory resets!
             * }
             *
             */
            Log::alert('Factory settings restored from the console');
            $this->info('Factory settings restored and a new API key has been generated!');
        } else {
            $this->error('User cancelled!');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
                //array('example', InputArgument::REQUIRED, 'An example argument.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
                //rearray('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}