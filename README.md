# 图形魔术手

基于php实现的图形处理工具

[![Build Status](https://travis-ci.org/slince/magic-hand.svg?branch=master)](https://travis-ci.org/slince/magic-hand)
[![Latest Stable Version](https://poser.pugx.org/slince/magic-hand/v/stable)](https://packagist.org/packages/slince/magic-hand)
[![Total Downloads](https://poser.pugx.org/slince/magic-hand/downloads)](https://packagist.org/packages/slince/magic-hand)
[![Latest Unstable Version](https://poser.pugx.org/slince/magic-hand/v/unstable)](https://packagist.org/packages/slince/magic-hand)
[![License](https://poser.pugx.org/slince/magic-hand/license)](https://packagist.org/packages/slince/magic-hand)


# 安装
```
composer global require slince/magic-hand *@dev
```

# Basic Usage
魔术手是基于命令行使用的，目前支持的命令有
- thumbnail 图形缩略

支持的参数：

 --src [-s] 图片源文件夹路径，默认是工作目录下的src

 --dst [-d] 保存新生成图片的路径，默认是工作目录下的dst
 
 --mode [-m] 缩略图模式[inset, outbound], inset模式为保证缩略图像内容的完整不会严格按照给定尺寸裁剪，outbound会先进行缩放，再按照尺寸裁剪
 所以可以保证尺寸的准确，但图像内容可能会有所损失；默认是inset模式

 注：参数都是可选的

调用方式
```
magichand thumbnail -s 图片路径 -d 保存路径 -m 缩略模式

```
如果需要帮助，可执行下面命令查看
```
magichand thumbnail --help
```



