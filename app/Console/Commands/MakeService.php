<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\Console\Input\InputArgument;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     * Need the name as argument
     * @var string
     */
    protected $signature = 'make:service {name}';


    protected $name = 'Make service command';

    protected $help = 'Create a new service class:
        You need to write the name before the command:
        Example: php artisan make:service Test';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Execute the console command.
     */
    public function handle()
    { 
 
        $serviceName = $this->argument('name');
        if (empty($serviceName))   $this->error('Can`t create a service without a name (missing: "name" before make:service ---).');

        
        if (!$this->isFolder($this->getPath())){
            if (!$this->makeServiceFolder()) $this->error('Can`t create Service folder');
        }

        if ($this->alreadyExists($serviceName)) $this->error('The service already exists!');

        if (!$this->makeNewServiceClass($serviceName)) $this->error('Error creating service');

        $this->info('----Service '.$serviceName.' created successfully!');

        // $this->register($serviceName);
        // $this->call('make:provider', [
        //     'name' => ucfirst($serviceName) . 'ServiceProvider'
        // ]);

    }

    public function register(string $serviceName)
    {
        $this->getApplication()->register(ucfirst($serviceName)::class);
    }


    /**
     * Verify if the folder exists
     * @param string $path
     * @return bool
     */
    private function isFolder(string $path): bool
    {
        return is_dir($path);
    }


    /**
     * Create the service folder if not exists
     * @return bool
     */
    private function makeServiceFolder(): bool
    {
        return mkdir($this->getPath());
    }


    /**
     * Create the new service class
     * @param string $newService
     * @return bool
     */
    private function makeNewServiceClass(string $newService)
    {
        if (!$resource = $this->createFile($newService)) throw new \Exception('Can`t create the file');

        return $this->setTemplate($resource, $newService);
    }

    /**
     * get the current path of the Service folder
     * @return string
     */
    private function getPath(): string
    {
        return app_path('/Services');
    }

    /**
     * Verify that the service class already exists
     * @param string $serviceName
     * @return bool
     */
    private function alreadyExists($serviceName): bool
    {
        return file_exists($this->getPath().'/'.$serviceName);
    }

    private function createFile(string $newService)
    {
        return fopen($this->getPath().'/'.ucfirst($newService).'Service.php', 'w', true);
    }

    private function setTemplate($resource, $newService)
    {
        $templateData = $this->getCommonData($newService);

        return fwrite($resource, $templateData);
    }

    private function getCommonData(string $newService)
    {
        return "<?php \n \n namespace App\Services; \n \n class ".ucfirst($newService)."Service \n { \n \n \t// do stuff \n \tpublic function doSmth() \n \t{ \n \t} \n }";
    }

}
