<?php

namespace Configurator\Foundation;

use Illuminate\Config\Repository as LaravelRepository;
use Illuminate\Support\Facades\File;

class Repository extends LaravelRepository
{
    protected $writable = [];

    public function save()
    {
    	dd('still writing to config files');
        collect($this->writable)->each(function ($path, $key) {
            $data = var_export(config($key), true);
            File::put($path, '<?php return ' . $data . ';' . PHP_EOL);
            sleep(2);
        });
    }

    protected function markWritable($key)
    {
        collect($this->getConfigFile($key))->each(function($path, $key) {
            $this->writable[$key] = $path;
        });
    }

    protected function getConfigFile($key)
    {
        $configKey = explode('.', $key, 2)[0];
        $path = app()->configPath() . '/' . $configKey . '.php';

        if (File::exists($path)) {
            return [$configKey => $path];
        }

        return null;
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string $key
     * @param  mixed $value
     * @return void
     */
    public function set($key, $value = null)
    {
        parent::set($key, $value);

        $keys = is_array($key) ? $key : [$key => $value];

        collect($keys)->each(function ($value, $key) {
            $this->markWritable($key);
        });
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function prepend($key, $value)
    {
        parent::prepend($key, $value);

        $this->markWritable($key);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        parent::push($key, $value);

        $this->markWritable($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);

        $this->markWritable($key);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);

        $this->markWritable($key);
    }
}
