<?php return array (
  'barryvdh/laravel-ide-helper' => 
  array (
    'providers' => 
    array (
      0 => 'Barryvdh\\LaravelIdeHelper\\IdeHelperServiceProvider',
    ),
  ),
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'intervention/image' => 
  array (
    'providers' => 
    array (
      0 => 'Intervention\\Image\\ImageServiceProvider',
    ),
    'aliases' => 
    array (
      'Image' => 'Intervention\\Image\\Facades\\Image',
    ),
  ),
  'jacobcyl/ali-oss-storage' => 
  array (
    'providers' => 
    array (
      0 => 'Jacobcyl\\AliOSS\\AliOssServiceProvider',
    ),
  ),
  'ken/blade-minify' => 
  array (
    'providers' => 
    array (
      0 => 'Ken\\BladeMinify\\ServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'mews/captcha' => 
  array (
    'providers' => 
    array (
      0 => 'Mews\\Captcha\\CaptchaServiceProvider',
    ),
    'aliases' => 
    array (
      'Captcha' => 'Mews\\Captcha\\Facades\\Captcha',
    ),
  ),
  'naux/sendcloud' => 
  array (
    'providers' => 
    array (
      0 => 'Naux\\Mail\\SendCloudServiceProvider',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'overtrue/laravel-wechat' => 
  array (
    'providers' => 
    array (
      0 => 'Overtrue\\LaravelWeChat\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'EasyWeChat' => 'Overtrue\\LaravelWeChat\\Facade',
    ),
  ),
  'rap2hpoutre/laravel-log-viewer' => 
  array (
    'providers' => 
    array (
      0 => 'Rap2hpoutre\\LaravelLogViewer\\LaravelLogViewerServiceProvider',
    ),
  ),
  'tymon/jwt-auth' => 
  array (
    'aliases' => 
    array (
      'JWTAuth' => 'Tymon\\JWTAuth\\Facades\\JWTAuth',
      'JWTFactory' => 'Tymon\\JWTAuth\\Facades\\JWTFactory',
    ),
    'providers' => 
    array (
      0 => 'Tymon\\JWTAuth\\Providers\\LaravelServiceProvider',
    ),
  ),
  'zgldh/qiniu-laravel-storage' => 
  array (
    'providers' => 
    array (
      0 => 'zgldh\\QiniuStorage\\QiniuFilesystemServiceProvider',
    ),
  ),
);