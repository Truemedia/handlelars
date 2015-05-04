<?php
namespace Handlelars;

use Illuminate\View\Engines\EngineInterface;
use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use LightnCandy;

class MustacheEngine implements EngineInterface
{
    /** @var Filesystem */
    private $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function get($path, array $data = array())
    {
        $filename = $this->files->name($path) . '.' . $this->files->extension($path);
        $compile_path = \Config::get('view.compiled') . DIRECTORY_SEPARATOR . $filename;

        $template_last_modified = $this->files->lastModified($path);
        $cache_last_modified = $this->files->lastModified($compile_path);

        $view = $this->files->get($path);
        $app = app();

        // $m = new Mustache_Engine($app['config']->get('handlelars'));
        // Configuration
        $always_compile = false;
        $helpers = \Config::get('handlelars.helpers');

        // Precompile templates to view cache when necessary
        $ignore_cache = ($template_last_modified > $cache_last_modified || $always_compile);
        if (!$this->files->isFile($compile_path) || $ignore_cache)
        {
            $tpl = LightnCandy::compile($view, compact('helpers'));
            $this->files->put($compile_path, $tpl);
        }

        if (isset($data['__context']) && is_object($data['__context'])) {
            $data = $data['__context'];
        } else {
            $data = array_map(function ($item) {
                return (is_object($item) && method_exists($item, 'toArray')) ? $item->toArray() : $item;
            }, $data);
        }
 
        $renderer = $this->files->getRequire($compile_path);
        return $renderer($data);
    }
}
