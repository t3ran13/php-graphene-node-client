<?php
class Autoloader
{
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    protected function autoload($class)
    {
        $pathParts = explode('\\', $class);

        if (is_array($pathParts)) {
            unset($pathParts[0]);
            $filePath = PATH . DIRECTORY_SEPARATOR . implode('/', $pathParts) . '.php';

            if (file_exists($filePath)) {
                require_once($filePath);
            }
        }
    }

}
$autoloader = new Autoloader();
$autoloader->register();