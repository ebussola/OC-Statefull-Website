<?php namespace ebussola\statefull\middleware;

use Closure;
use ebussola\statefull\classes\CacheFileHandler;
use eBussola\Statefull\Plugin;

/**
 * Class StatefullMiddleware
 *
 * This middleware is optional.
 * You may install it if you think that the Statefull's routes are not being reached.
 *
 * @package Pbw\Meucupom\App\Http\Middleware
 */
class StatefullMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (!\Config::get('app.debug') && Plugin::$routerActive === false) {
			$cachePath = (new CacheFileHandler())->getCachePath();
			$blacklist = file_exists($cachePath . '/index-blacklist.config') ?
				file_get_contents($cachePath . '/index-blacklist.config') : null;
			$paramBlacklist = file_exists($cachePath . '/param-blacklist.config') ?
				json_decode(file_get_contents($cachePath . '/param-blacklist.config'), true) : [];

			$paramBlacklistFunctionFile = $cachePath . '/param-blacklist-function.php';
			if (file_exists($paramBlacklistFunctionFile)) {
				include $paramBlacklistFunctionFile;

				if (preg_match('/^(?!\/backend)(?!\/combine)' . $blacklist . '/i', $request->getPathInfo()) === 1 && !isParamBlacklisted($paramBlacklist)) {

					$file = $cachePath  . $request->getPathInfo() . '.html';
					if (file_exists($file)) {
						return \Response::make(file_get_contents($file));
					} else {


						try {
							$responseRaw = file_get_contents(\Config::get('app.url') . $request->getPathInfo() . '?nocache=1');
						} catch (\ErrorException $e) {
							if (strstr($e->getMessage(), '404 Not Found')) {
								$controller = \App::make('Cms\Classes\Controller');
								$response = $controller->run('/404');
								$response->setStatusCode(404);

								$this->tryLazyCache($request->getPathInfo(), $response->getContent());

								return $response;
							} else {
								throw $e;
							}
						}


						$this->tryLazyCache($request->getPathInfo(), $responseRaw);

						return \Response::make($responseRaw);
					}
				}
			}
		}

		return $next($request);
	}

	/**
	 * @param $request
	 * @param $responseRaw
	 */
	private function tryLazyCache($pagePath, $responseRaw)
	{
		if (\Ebussola\Statefull\Models\Settings::get('cache_lazy_cache', false)) {
			(new CacheFileHandler())->saveCacheFile($pagePath, $responseRaw);
		}
	}

}
