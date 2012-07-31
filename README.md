# Zonda

Zonda——Degas自家用的前端框架。

## Zonda 原厂零件

- **上传模块** : 自家实现的跨浏览器多文件上传。
    

    + 使用XHR上传，进度条显示；对于不能使用XHR上传的浏览器则使用iframe+同步表单上传。
    
    + 跨浏览器，IE6+。

    + 可配置的允许上传的文件类型。

    + 两个模块事件，loading，ready。
    
    + 自动生成上传文件列表。
    
    + 自定义错误消息。

- **幻灯片模块** : 自家用的幻灯片模块。

    + 自动生成带数字或不带数字的页码，以及‘next’，‘prev’按钮。

    + 跨浏览器，IE6+。

    + next，prev，play，stop功能。

    + 多种切换效果，其实目前只有两种 >_<

    + 简洁可扩展的核心play()函数。

    + TODO 缩略图作为页码的功能正在进行中。

- **数据验证模块** : 自家用的表单验证模块。

    + Email验证，数字验证，必填项验证。
    
    + 跨浏览器，IE6+。 
        
    + 简介可爱的核心检测方式，可扩展。

    + TODO，自定义错误事件，回调消息函数。

    + 目前支持验证普通的 input:text，select:option，textarea，以后会慢慢添加更多类型的表单验证 ^_^
    
## Zonda搭载的强劲零件

- [`Seajs`](http://seajs.org/docs/#intro) : 国产强劲引擎，作为核心库，进行模块的依赖管理。

- [`Typo`](http://typo.sofish.de/) : 钟表式仪表盘，获得更美观的中文排版。

- [`Modernizr`](http://modernizr.com/) : 进口行车电脑，检测浏览器对HTML5和CSS3的支持。

- [`Bootstrap`](http://twitter.github.com/bootstrap/index.html) : 进口碳素纤维车身，强力的UI，对IE的支持不好，自己进行了一些改装，使之在IE6+环境中能正常使用。

- [`Less`](http://lesscss.org/) : 进口涡轮增压装置，对CSS进行组织，将CSS代码模块化，可配置。

谢谢Seajs，Less的作者，他们让前端写代码变成一件愉悦的事情，维护起来也不再那么头疼了。

### Zonda名字来源

`Pagani`的超级跑车[`Zonda`](http://www.pagani.com/zonda/default.aspx)，意大利语“风之子”之意。
![alt text](http://www.widescreenbackgrounds.net/wallpapers/background-widescreen-white-pagani-zonda-wallpapers.jpg 'Zonda')
