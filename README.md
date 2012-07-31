Zonda
=====

Zonda——Degas自家用的前端框架。

- **上传模块** : 自家实现的跨浏览器多文件上传。

    + 能使用XHR上传的则使用XHR上传，并有进度条显示；对于不能使用XHR上传的浏览器则使用iframe+同步表单上传。

    + 可配置允许上传的文件类型。

    + 两个模块事件，loading，ready，方便监听上传是否完成。

- **幻灯片模块** : 自家用的幻灯片模块。

    + 实现带数字或不带数字的页码。

    + 跨浏览器，IE6+。

    + next，prev，play，stop功能。

    + 多种切换效果，其实目前只有两种 >_<

    + 简洁可扩展的核心play()函数。

    + TODO 缩略图作为页码的功能正在进行中。

- **数据验证模块** : 自家用的表单验证模块。

    + Email验证，数字验证，为空验证。

    + 跨浏览器，IE6+。

    + TODO，自定义错误事件，回调消息函数。

    + 目前支持验证普通的 input:text 表单，select:option 表单，textarea，以后会慢慢添加更多类型的表单验证。
    
## Zonda搭载的强劲部件

- [`Seajs`](http://seajs.org/docs/#intro) : 作为核心库，进行模块的依赖管理

- [`Typo`](http://typo.sofish.de/) : 获得更美观的中文排版

- [`Modernizr`](http://modernizr.com/) : 检测浏览器对HTML5和CSS3的支持

- [`Bootstrap`](http://twitter.github.com/bootstrap/index.html) : 强力的UI的支持，对IE的支持不好，在寻找支持的IE的解决方案

- [`Less`](http://lesscss.org/)对CSS进行组织

谢谢Seajs，Less的作者，他们让前端写代码变成一件愉悦的事情，维护起来也不再那么头疼了。

## Zonda

`Pagani`的超级跑车[`Zonda`](http://www.pagani.com/zonda/default.aspx)，意大利语“风之子”之意。
![alt text](http://www.widescreenbackgrounds.net/wallpapers/background-widescreen-white-pagani-zonda-wallpapers.jpg 'Zonda')
