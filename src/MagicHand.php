<?php
namespace Slince\MagicHand;

use Imagine\Gd\Image;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Slince\Event\Dispatcher;
use Slince\Event\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MagicHand
{
    /**
     * 事件名，开始处理
     * @var string
     */
    const EVENT_BEGIN = 'begin';

    /**
     * 事件名，处理中
     * @var string
     */
    const EVENT_PROCESS = 'process';

    /**
     * 事件名，处理结束
     * @var string
     */
    const EVENT_END = 'end';
    /**
     * @var Imagine
     */
    protected $imagine;

    /**
     * 缩略大小
     * @var array|Box
     */
    protected $thumbBox = [];

    /**
     * 缩略模式
     * @var string
     */
    protected $thumbMode;

    /**
     * 图片源路径
     * @var string
     */
    protected $src;

    /**
     * 图片保存路径
     * @var string
     */
    protected $dst;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * 保存路径
     * @var string
     */
    protected $savePath;

    function __construct($src, $dst, array $size, $mode = ImageInterface::THUMBNAIL_INSET)
    {
        $this->src = $src;
        $this->dst = $dst;
        $this->imagine = new Imagine();
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
        $this->dispatcher = new Dispatcher();
        $this->thumbBox = new Box($size[0], $size[1]);
        $this->thumbMode = $mode;
        //生成文件目录
        $savePath = $dst . $this->thumbBox->getWidth() . 'x' . $this->thumbBox->getHeight();
        $this->filesystem->mkdir($savePath);
    }

    function run()
    {
        $files = $this->finder->files()->in($this->src);
        $this->dispatcher->dispatch(static::EVENT_BEGIN, new Event(static::EVENT_BEGIN, $this, [
            'images' => $files
        ]));
        $successFiles = [];
        foreach ($files as $file) {
            $image = $this->imagine->open($file->getPath());
            $image = $this->process($image);
            if ($image->save($this->getImageSavePath($file))) {
                $successFiles[] = $file;
            }
        }
        $this->dispatcher->dispatch(static::EVENT_END, new Event(static::EVENT_END, $this, [
            'images' => $files,
            'successFiles' => $successFiles
        ]));
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * 获取图像文件保存路径
     * @param SplFileInfo $fileInfo
     * @return string
     */
    protected function getImageSavePath(SplFileInfo $fileInfo)
    {
        return $this->savePath . $fileInfo->getBasename(true);
    }

    /**
     * @return Imagine
     */
    public function getImagine()
    {
        return $this->imagine;
    }

    /**
     * 处理图片
     * @param Image $image
     * @return ImageInterface
     */
    protected function process(Image $image)
    {
        $this->dispatcher->dispatch(static::EVENT_PROCESS, new Event(static::EVENT_END, $this, [
            'image' => $image
        ]));
        return $this->thumbnail($image);
    }

    /**
     * 缩略图
     * @param Image $image
     * @return ImageInterface
     */
    protected function thumbnail(Image $image)
    {
        return $image->thumbnail($this->thumbBox, $this->thumbMode);
    }
}