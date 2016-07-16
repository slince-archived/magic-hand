<?php
/**
 * slince magic hand library
 * @author Tao <taosikai@yeah.net>
 */
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
    protected $thumbMode = ImageInterface::THUMBNAIL_INSET;

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

    function __construct($src, $dst)
    {
        $this->src = $src;
        $this->dst = $dst;
        $this->imagine = new Imagine();
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
        $this->dispatcher = new Dispatcher();
    }

    function run()
    {
        $files = $this->finder->files()->in($this->src);
        $this->dispatcher->dispatch(static::EVENT_BEGIN, new Event(static::EVENT_BEGIN, $this, [
            'images' => $files
        ]));
        $successFiles = [];
        foreach ($files as $file) {
            $image = $this->imagine->open($file->getRealPath());
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
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @return Finder
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * 设置缩略大小
     * @param array $size
     */
    function setThumbBox(array $size)
    {
        $this->thumbBox = new Box($size[0], $size[1]);
        //生成文件目录
        $this->savePath = $this->dst . '/' . $this->thumbBox->getWidth() . ' x ' . $this->thumbBox->getHeight();
        $this->filesystem->mkdir($this->savePath);
    }

    /**
     * @return array|Box
     */
    public function getThumbBox()
    {
        return $this->thumbBox;
    }

    /**
     * 设置缩略模式
     * ImageInterface::THUMBNAIL_INSET 自适应宽比
     * ImageInterface::THUMBNAIL_OUTBOUND 固定宽比
     * @param $thumbMode
     */
    public function setThumbMode($thumbMode)
    {
        $this->thumbMode = $thumbMode;
    }
    /**
     * @return string
     */
    public function getThumbMode()
    {
        return $this->thumbMode;
    }

    /**
     * 获取图像文件保存路径
     * @param SplFileInfo $fileInfo
     * @return string
     */
    protected function getImageSavePath(SplFileInfo $fileInfo)
    {
        return $this->savePath . '/' . $fileInfo->getBasename(true);
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