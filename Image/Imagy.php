<?php namespace Modules\Media\Image;

use Illuminate\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Config\Repository;

class Imagy
{
    /**
     * @var \Intervention\Image\Image
     */
    private $image;
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $finder;
    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    private $config;
    /**
     * @var ImageFactoryInterface
     */
    private $imageFactory;

    public function __construct(Repository $config, ImageFactoryInterface $imageFactory)
    {
        $this->image = App::make('Intervention\Image\ImageManager');
        $this->finder = App::make('Illuminate\Filesystem\Filesystem');
        $this->config = $config;
        $this->imageFactory = $imageFactory;
    }

    public function get($path, $thumbnail)
    {
        $filename = '/assets/media/' . $this->newFilename($path, $thumbnail);
        try {
            $this->finder->get(public_path(). $filename);
            return $filename;
        } catch (FileNotFoundException $e) {
            $image = $this->image->make(public_path() . $path);

            foreach ($this->config->get("media::thumbnails.{$thumbnail}") as $manipulation => $options) {
                $image = $this->imageFactory->make($manipulation)->handle($image, $options);
            }

            $image = $image->encode(pathinfo($path, PATHINFO_EXTENSION));

            $this->finder->put(public_path() . $filename, $image);
        }
    }

    /**
     * Prepend the thumbnail name to filename
     * @param $path
     * @param $thumbnail
     * @return mixed|string
     */
    private function newFilename($path, $thumbnail)
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        return $filename . '_' . $thumbnail . '.' . pathinfo($path, PATHINFO_EXTENSION);
    }
}