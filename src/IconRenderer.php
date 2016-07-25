<?php

namespace DigitLab\BladeIcons;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

class IconRenderer
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The array of active view paths.
     *
     * @var array
     */
    protected $paths;

    /**
     * Get the cache path for the compiled views.
     *
     * @var string
     */
    protected $cachePath;

    /**
     * The array of icons that have been located.
     *
     * @var array
     */
    protected $icons = [];

    /**
     * The class prefix added to the icon.
     *
     * @var string
     */
    protected $classPrefix = 'icon';

    /**
     * IconCompiler constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param array                             $paths
     * @param string                            $cachePath
     */
    public function __construct(Filesystem $files, array $paths, $cachePath)
    {
        if (!$cachePath) {
            throw new InvalidArgumentException('Please provide a valid cache path.');
        }

        $this->files = $files;
        $this->paths = $paths;
        $this->cachePath = $cachePath;
    }

    /**
     * Gets the class prefix.
     *
     * @return string
     */
    public function getClassPrefix()
    {
        return $this->classPrefix;
    }

    /**
     * Sets the class prefix.
     *
     * @param string $value
     */
    public function setClassPrefix($value)
    {
        $this->classPrefix = $value;
    }

    /**
     * Get the string contents of the icon.
     *
     * @param string $name
     * @param array  $attributes
     *
     * @return string
     */
    public function render($name, array $attributes = [])
    {
        $filePath = $this->find($name);

        $compiled = $this->getCompiledPath($filePath, $attributes);

        if ($this->isExpired($filePath, $attributes)) {
            $this->compile($filePath, $name, $attributes);
        }

        return $this->files->get($compiled);
    }

    /**
     * Get the fully qualified location of the view.
     *
     * @param string $name
     *
     * @return string
     */
    protected function find($name)
    {
        if (isset($this->icons[$name])) {
            return $this->icons[$name];
        }

        return $this->icons[$name] = $this->findInPaths($name, $this->paths);
    }

    /**
     * Find the given view in the list of paths.
     *
     * @param string $name
     * @param array  $paths
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function findInPaths($name, $paths)
    {
        foreach ((array) $paths as $path) {
            if ($this->files->exists($viewPath = $path.'/'.$name.'.svg')) {
                return $viewPath;
            }
        }

        throw new InvalidArgumentException("Icon [$name] not found.");
    }

    /**
     * Compile the icon at the given path.
     *
     * @param string $path
     * @param string $name
     * @param array  $attributes
     */
    protected function compile($path, $name, array $attributes = [])
    {
        $file = new \DOMDocument();
        $file->loadXML($this->files->get($path));

        $file->documentElement->setAttribute('class', "$this->classPrefix $this->classPrefix-$name");

        foreach ($attributes as $attr => $value) {
            $file->documentElement->setAttribute($attr, $value);
        }

        $this->files->put($this->getCompiledPath($path, $attributes), $file->saveXML($file->documentElement));
    }

    /**
     * Get the path to the compiled version of an icon.
     *
     * @param string $path
     * @param array  $attributes
     *
     * @return string
     */
    public function getCompiledPath($path, array $attributes = [])
    {
        $key = $this->getCompiledKey($path, $attributes);

        return $this->cachePath.'/'.$key.'.svg';
    }

    /**
     * Get the compiled key of the icon and attributes.
     *
     * @param string $path
     * @param array  $attributes
     *
     * @return string
     */
    public function getCompiledKey($path, array $attributes = [])
    {
        $key = "$path:$this->classPrefix:";

        foreach ($attributes as $attribute => $value) {
            $key .= "$attribute:$value";
        }

        return sha1($key);
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param string $path
     * @param array  $attributes
     *
     * @return bool
     */
    public function isExpired($path, array $attributes = [])
    {
        $compiled = $this->getCompiledPath($path, $attributes);

        if (!$this->files->exists($compiled)) {
            return true;
        }

        $lastModified = $this->files->lastModified($path);

        return $lastModified >= $this->files->lastModified($compiled);
    }
}
