# 图片处理魔术手

基于php实现的图形处理工具

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

 注：参数都是可选的

调用方式
```
magichand thumbnail -s 图片路径 -d 保存路径

```
如果需要帮助，可执行
```
magichand thumbnail --help
```
查看


