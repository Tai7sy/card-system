<?php
 namespace Symfony\Component\HttpKernel; use Symfony\Component\BrowserKit\Client as BaseClient; use Symfony\Component\BrowserKit\CookieJar; use Symfony\Component\BrowserKit\History; use Symfony\Component\BrowserKit\Request as DomRequest; use Symfony\Component\BrowserKit\Response as DomResponse; use Symfony\Component\HttpFoundation\File\UploadedFile; use Symfony\Component\HttpFoundation\Request; use Symfony\Component\HttpFoundation\Response; class Client extends BaseClient { protected $kernel; private $catchExceptions = true; public function __construct(HttpKernelInterface $kernel, array $server = array(), History $history = null, CookieJar $cookieJar = null) { $this->kernel = $kernel; $this->followRedirects = false; parent::__construct($server, $history, $cookieJar); } public function catchExceptions($catchExceptions) { $this->catchExceptions = $catchExceptions; } protected function doRequest($request) { $response = $this->kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, $this->catchExceptions); if ($this->kernel instanceof TerminableInterface) { $this->kernel->terminate($request, $response); } return $response; } protected function getScript($request) { $kernel = var_export(serialize($this->kernel), true); $request = var_export(serialize($request), true); $errorReporting = error_reporting(); $requires = ''; foreach (get_declared_classes() as $class) { if (0 === strpos($class, 'ComposerAutoloaderInit')) { $r = new \ReflectionClass($class); $file = \dirname(\dirname($r->getFileName())).'/autoload.php'; if (file_exists($file)) { $requires .= 'require_once '.var_export($file, true).";\n"; } } } if (!$requires) { throw new \RuntimeException('Composer autoloader not found.'); } $code = <<<EOF
<?php

error_reporting($errorReporting);

$requires

\$kernel = unserialize($kernel);
\$request = unserialize($request);
EOF;
return $code.$this->getHandleScript(); } protected function getHandleScript() { return <<<'EOF'
$response = $kernel->handle($request);

if ($kernel instanceof Symfony\Component\HttpKernel\TerminableInterface) {
    $kernel->terminate($request, $response);
}

echo serialize($response);
EOF;
} protected function filterRequest(DomRequest $request) { $httpRequest = Request::create($request->getUri(), $request->getMethod(), $request->getParameters(), $request->getCookies(), $request->getFiles(), $request->getServer(), $request->getContent()); foreach ($this->filterFiles($httpRequest->files->all()) as $key => $value) { $httpRequest->files->set($key, $value); } return $httpRequest; } protected function filterFiles(array $files) { $filtered = array(); foreach ($files as $key => $value) { if (\is_array($value)) { $filtered[$key] = $this->filterFiles($value); } elseif ($value instanceof UploadedFile) { if ($value->isValid() && $value->getSize() > UploadedFile::getMaxFilesize()) { $filtered[$key] = new UploadedFile( '', $value->getClientOriginalName(), $value->getClientMimeType(), 0, UPLOAD_ERR_INI_SIZE, true ); } else { $filtered[$key] = new UploadedFile( $value->getPathname(), $value->getClientOriginalName(), $value->getClientMimeType(), $value->getClientSize(), $value->getError(), true ); } } } return $filtered; } protected function filterResponse($response) { ob_start(); $response->sendContent(); $content = ob_get_clean(); return new DomResponse($content, $response->getStatusCode(), $response->headers->all()); } } 