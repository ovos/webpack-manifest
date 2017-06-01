<?php
namespace Ovos\Webpack;

use Eloquent\Pathogen\FileSystem\FileSystemPath;
use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\Resolver\BasePathResolver;

class Manifest
{
    const MANIFEST_FILENAME = 'manifest.json';

    /**
     * [manifest file dirname => [src file => target file]]
     * @var array[]
     */
    protected static $manifests = [];

    /**
     * Resolve path to the file from manifest.json
     *
     * @param string $path
     * @param string $base base dir which should be prepended to $path
     * @param string $manifestFilename
     *
     * @return string
     */
    public static function resolve($path, $base = null, $manifestFilename = self::MANIFEST_FILENAME)
    {
        $relativePath = Path::fromString($path);
        $basePath = FileSystemPath::fromString($base ? $base : getcwd());

        $resolver = new BasePathResolver;
        $fullPath = $resolver->resolve($basePath, $relativePath);

        $dirname = dirname($fullPath->string());
        $manifest = self::getManifest($dirname, $manifestFilename);

        $filename = $relativePath->name();
        if (isset($manifest[$filename])) {
            $path = $relativePath->replaceName($manifest[$filename])->string();
        }

        return $path;
    }

    /**
     * Return contents of manifest file from a given dir
     *
     * @param string $dir
     * @param string $manifestFilename
     * @return array
     */
    public static function getManifest($dir, $manifestFilename = MANIFEST_FILENAME)
    {
        $manifestPath = $dir . DIRECTORY_SEPARATOR . $manifestFilename;
        // normalize slashes
        $manifestPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $manifestPath);
        if (!isset(self::$manifests[$manifestPath])) {
            $manifest = [];
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
            }

            self::$manifests[$manifestPath] = $manifest;
        }

        return self::$manifests[$manifestPath];
    }
}
