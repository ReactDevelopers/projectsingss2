<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Factory;
use GuzzleHttp\Mimetypes;
use GuzzleHttp\Psr7\Stream;
use Intervention\Image\ImageManager;
use Modules\Media\Entities\File;
use Modules\Media\ValueObjects\MediaPath;
use Modules\Media\Services\MediaService;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Image\ImageFactoryInterface;
use Modules\Media\Image\ThumbnailsManager;
use Modules\Media\Image\Imagy;

class UpdateMediaThumb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:update:thumb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add large thumb media';

    /**
     * @var FileRepository
     */
    protected $file;
    /**
     * @var \Intervention\Image\Image
     */
    protected $image;
    /**
     * @var ImageFactoryInterface
     */
    protected $imageFactory;
    /**
     * @var ThumbnailsManager
     */
    /**
     * @var ThumbnailsManager
     */
    private $manager;
    /**
     * @var Factory
     */
    private $filesystem;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    
    protected $imagy;

    public function __construct(FileRepository $file, ImageFactoryInterface $imageFactory, ThumbnailsManager $manager)
    {
        parent::__construct();

        $this->imagy = app(Imagy::class);
        $this->imagy->file = $file;
        $this->manager = $manager;
        $this->filesystem = app(Factory::class);
        $this->image = app(ImageManager::class);
        $this->imageFactory = $imageFactory;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = $this->imagy->file->all();
        // echo json_encode($files[0]->path->getRelativeUrl());

        foreach ($files as $key => $file) {
            $path = $file->path;
            $pathImage = $this->getDestinationPath($path->getRelativeUrl());
            if($this->fileExists($pathImage)) {
                if ($this->imagy->isImage($path)) {
                    $type = "";
                    
                    echo $file->id . '. ' . $pathImage . ': ';

                    $fileName = pathinfo($file->path, PATHINFO_FILENAME);
                    $extension = pathinfo($file->path, PATHINFO_EXTENSION);

                    foreach ($this->manager->all() as $thumbnail) {
                        $pathThumb = config('asgard.media.config.files-path') . "{$fileName}_{$thumbnail->name()}.{$extension}";
                        if (!$this->fileExists($this->getDestinationPath($pathThumb))) {
                            // $paths[] = (new MediaPath($this->getDestinationPath($path)))->getRelativeUrl();
                            $image = $this->image->make($this->filesystem->disk($this->getConfiguredFilesystem())->get($pathImage));
                            $filename = config('asgard.media.config.files-path') . $this->newFilename($path, $thumbnail->name());
                            foreach ($thumbnail->filters() as $manipulation => $options) {

                                $image = $this->imageFactory->make($manipulation)->handle($image, $options);
                            }
            
                            $image = $image->stream(pathinfo($path, PATHINFO_EXTENSION), array_get($thumbnail->filters(), 'quality', 90));

                            $this->writeImage($filename, $image);

                            $type.= " " . $thumbnail->name();
                        }
                    }
                    echo $type;
                    echo "\r\n";
                }
            }
            else{
                echo $file->id . '. ' . $pathImage . ': deleted';
                echo "\r\n";
            }
        }

        echo "done!!!";
        echo "\r\n";
    }

    /**
     * @param string $path
     * @return string
     */
    private function getDestinationPath($path)
    {
        if ($this->getConfiguredFilesystem() === 'local') {
            return basename(public_path()) . $path;
        }

        return $path;
    }


    private function getConfiguredFilesystem()
    {
        return config('asgard.media.config.filesystem');
    }

    /**
     * @param $filename
     * @return bool
     */
    private function fileExists($filename)
    {
        return $this->filesystem->disk($this->getConfiguredFilesystem())->exists($filename);
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

    /**
     * Write the given image
     * @param string $filename
     * @param Stream $image
     */
    private function writeImage($filename, Stream $image)
    {
        $filename = $this->getDestinationPath($filename);
        $resource = $image->detach();
        $config = [
            'visibility' => 'public',
            'mimetype' => Mimetypes::getInstance()->fromFilename($filename),
        ];
        if ($this->fileExists($filename)) {
            return $this->filesystem->disk($this->getConfiguredFilesystem())->updateStream($filename, $resource, $config);
        }
        $this->filesystem->disk($this->getConfiguredFilesystem())->writeStream($filename, $resource, $config);
    }
}
