## 天然打造·原生编写 
[Github地址](https://github.com/yuncopy/students )
[码云地址](https://gitee.com/yuncopy/students )

### BaleCMS 是一个基于组件化原生编写的后台管理系统。

---
#### 我们是一个有温度的系统，致力于化简为繁琐，原生编写让更多爱好者了解底层框架原理，系统提供完整的组件及API，基于此框架可以快速开发应用。

#### 安装部署一键完成及二次开发可以参考 案例代码或者联系作者，初始化数据库文件摆放在项目根目录下。

> #### 芭乐学生管理系统基于BalePHP框架开发，遵循 Apache2 开源协议发布。Apache Licence 是著名的非盈利开源组织 Apache 采用的协议。该协议和 BSD 类似，鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再作为开源或商业软件发布。


#### 主要特性
- 轻量级后台系统
- 核心功能组件化
- 路由配置更灵活
- 强化路由功能
- 更灵活的控制器
- 优雅ORM操作
- 配置文件可分离
- 命令行访问支持
- 一键按照部署
- 完整的权限控制
- 执行流程透明化

#### 继续完善
- 后台数据权限
- 新增前台界面
- 推出小程序商城

#### 安装部署
- 环境要求
>PHP >= 5.6.0 (推荐PHP7.1版本)
Mysql >= 5.5.0 (需支持innodb引擎)
Apache 或 Nginx
PDO PHP Extension
CURL PHP Extension
Composer (可选,用于管理第三方扩展包)
- 主要配置nginx
```
   location / {
 	   # Redirect everything that isn't a real file to index.php
 	     try_files $uri $uri/ /index.php$is_args$args;
	}
```
- 访问public/install/index.php即可按照提示安装
- 安装成功 初始化用户/密码： jackin / 123456

#### 功能介绍
  - 项目安装
   ![image.png](https://upload-images.jianshu.io/upload_images/2897604-452ad1eea32ad2a5.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

  - 后台首页
  ![image.png](https://upload-images.jianshu.io/upload_images/2897604-7ad54c539047e51a.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
  - 权限管理
 ![image.png](https://upload-images.jianshu.io/upload_images/2897604-c54340522d95bc11.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
 - 授权页面
 ![image.png](https://upload-images.jianshu.io/upload_images/2897604-a08c28d858061f23.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

#### 技术支持
遇到问题到问题或者需要二次开发请联系

![image.png](https://upload-images.jianshu.io/upload_images/2897604-2741529bfbfaf80c.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)


