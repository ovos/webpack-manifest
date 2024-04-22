<?php
namespace Ovos\Webpack;

use Ovos\Dir;

class Manifest
{
    public const MANIFEST_FILENAME = 'manifest.json';

    /**
     * [manifest file dirname => [src file => target file]]
     * @var array[]
     */
    protected static array $manifests = [];

    /**
     * Resolve path to the file from manifest.json
     *
     * @param string $path (src or href value)
     * @param ?string $base base dir which should be prepended to $path
     * @param string $manifestFilename
     *
     * @return string
     */
    public static function resolve(
        string $path,
        ?string $base = null,
        string $manifestFilename = self::MANIFEST_FILENAME,
    ): string
    {
        $relativeDir = dirname($path);
        $dir = ($base ?? __DIR__) . DIRECTORY_SEPARATOR . Dir::preProcess($relativeDir);
        $manifest = self::getManifest($dir, $manifestFilename);
        $filename = basename($path);

        if (isset($manifest[$filename])) {
            $path = $relativeDir . '/' . $manifest[$filename];
        }

        return $path;
    }

    /**
     * Return contents of manifest file from a given dir
     *
     * @param string $dir
     * @param string $manifestFilename
     *
     * @return array
     */
    public static function getManifest(
        string $dir,
        string $manifestFilename = self::MANIFEST_FILENAME,
    ): array
    {
        $manifestPath = $dir . DIRECTORY_SEPARATOR . $manifestFilename;
        if (!isset(self::$manifests[$manifestPath])) {
            $manifest = [];
            if (file_exists($manifestPath)) {
                $manifest = json_decode(
                    file_get_contents($manifestPath),
                        associative: true,
                        flags: JSON_THROW_ON_ERROR,
                    );
            }

            self::$manifests[$manifestPath] = $manifest;
        }

        return self::$manifests[$manifestPath];
    }
}
