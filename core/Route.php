<?php

namespace Core;

class Route
{
    private $AppInit;
    private $modulesAllAr = [];
    private $uriOnly = "";
    private $uriRoute = "";
    private $uriRouteInfoAr = [];
    private $uriVariablesAr = [];

    private $allRoutesAr = [];
    private $controllerPath = "";
    private $method = "";
    private $permAll_ar = [];

    function __construct(AppInit $AppInit)
    {
        $this->AppInit = $AppInit;

        $i = 0;
        $routsAppConfigObj = xmlFileToObject("app/app-config.xml");
        foreach ($routsAppConfigObj->modules as $modulesObj) {
            if ((string)$modulesObj->attributes()->status[0] == 1) {
                $priority = $modulesObj->attributes()->priority * 100 + ++$i;
                $this->modulesAllAr[$priority] = $modulesObj;
            }
        }

        ksort($this->modulesAllAr);

        foreach ($this->modulesAllAr as $modulesObj) {
            $preRouteVal = "";
            $configPerm = (string)$modulesObj->Attributes()->perm;

            //--
            $xmlRoutesPath = realpath("app/" . $modulesObj->attributes()->dir . "/routes.xml");
            $routsObj = xmlFileToObject($xmlRoutesPath, "routes.xml is not found on module " . $modulesObj->attributes()->dir);
            if ((string)$modulesObj->attributes()->preRoutes != "null") {
                $preRouteVal = (string)$modulesObj->attributes()->preRoutes;
            }

            foreach ($routsObj->controller as $pathObj) {
                $controller = (string)$pathObj->Attributes()->val;
                $pathPageTitle = (string)$pathObj->Attributes()->pageTitle;
                $pathPageSubTitle = (string)$pathObj->Attributes()->pageSubTitle;
                $pathNameRoute = (string)$pathObj->Attributes()->nameRoute;
                $pathPerm = (string)$pathObj->Attributes()->perm;
                $pathValidationToken = (string)$pathObj->Attributes()->validationToken;
                $pathAuth = (string)$pathObj->Attributes()->auth;
                $pathIsFailedUrl = (string)$pathObj->Attributes()->isFailedUrl;

                foreach ($pathObj as $routeObj) {
                    $routeVal = $this->routeAdder($preRouteVal, (string)$routeObj->Attributes()->val);
                    $get = (string)$routeObj->Attributes()->get;
                    $post = (string)$routeObj->Attributes()->post;
                    $routePageTitle = (string)$routeObj->Attributes()->pageTitle;
                    $routePageSubTitle = (string)$routeObj->Attributes()->pageSubTitle;
                    $routeNameRoute = (string)$routeObj->Attributes()->nameRoute;
                    $routePerm = (string)$routeObj->Attributes()->perm;
                    $routeValidationToken = (string)$routeObj->Attributes()->validationToken;
                    $routeAuth = (string)$routeObj->Attributes()->auth;
                    $routeIsFailedUrl = (string)$routeObj->Attributes()->isFailedUrl;

                    $routeVal = trim($routeVal, "/") ?: "/";
                    $perm = ($routePerm ?: $pathPerm) ?: $configPerm;
                    $this->allRoutesAr[$routeVal] = [
                        'route' => $routeVal,
                        'controller' => $controller,
                        'get' => $get,
                        'post' => $post,
                        'module' => (string)$modulesObj->attributes()->dir,
                        'pageTitle' => $routePageTitle ?: $pathPageTitle,
                        'pageSubTitle' => $routePageSubTitle ?: $pathPageSubTitle,
                        'nameRoute' => $routeNameRoute ?: $pathNameRoute,
                        'configPerm' => $configPerm,
                        'perm' => $perm,
                        'validationToken' => $routeValidationToken ?: $pathValidationToken,
                        'auth' => $routeAuth ?: $pathAuth,
                        'isFailedUrl' => $routeIsFailedUrl ?: $pathIsFailedUrl,
                    ];

                    $this->permAll_ar[$perm] = $perm;
                }
            }
        }

        //--detect()
        $this->detect();

        $this->controllerPath = treen_realpath('app/' . $this->uriRouteInfoAr['module'] . '/controllers/' . $this->uriRouteInfoAr['controller'], "/");
        $this->controllerPath = "\\" . str_replace("/", "\\", ucfirst($this->controllerPath));
        $this->method = $this->uriRouteInfoAr[$this->getUriMethod()];

        if (!$this->uriRoute) {

            ErrorPages::RouteUrl(1, "Page Not Found");
        } else if (!$this->uriRouteInfoAr['module']) {

            ErrorPages::Route(2, "Config Error: Module not detected");
        } else if (!$this->uriRouteInfoAr['controller']) {

            ErrorPages::Route(3, "Config Error: Controller not detected");
        } else if (!$this->method) {

            ErrorPages::Route(4, "Config Error: Method not detected");
        } else if (!is_dir("app/" . $this->uriRouteInfoAr['module'] . "/")) {

            ErrorPages::Route(5, "Module Dir Not Found");
        } else if (!is_file("app/" . $this->uriRouteInfoAr['module'] . "/controllers/" . $this->uriRouteInfoAr['controller'] . ".php")) {

            ErrorPages::Route(6, "Controller File Not found");
        } else if (!class_exists($this->controllerPath)) {

            ErrorPages::Route(7, "Controller Class Not Valid (Route)");
        }
    }

    private function routeAdder($r0, $r1): string
    {
        $r = implode("/", array_filter([$r0, $r1]));
        return str_replace("//", "/", $r);
    }

    private function detect(): bool
    {
        $this->uriOnly = $this->AppInit->getUriOnly();
        $uri_ar = explode("/", $this->uriOnly);

        //--Detect Default Route
        foreach ($this->allRoutesAr as $key => $det_ar) {
            $key_ar = explode("/", $key);
            if (
                $this->checker($key_ar, $uri_ar, 0) &&
                $this->checker($key_ar, $uri_ar, 1) &&
                $this->checker($key_ar, $uri_ar, 2) &&
                $this->checker($key_ar, $uri_ar, 3) &&
                $this->checker($key_ar, $uri_ar, 4) &&
                $this->checker($key_ar, $uri_ar, 5) &&
                $this->checker($key_ar, $uri_ar, 6) &&
                $this->checker($key_ar, $uri_ar, 7) &&
                $this->checker($key_ar, $uri_ar, 8) &&
                $this->checker($key_ar, $uri_ar, 9)
            ) {
                $this->uriRoute = $key ?: "/";
                $this->uriRouteInfoAr = $det_ar;
                return true;
            }
        }
        return false;
    }

    function checker($key_ar, $route_ar, $pos): bool
    {
        $key_ar[$pos] = isset($key_ar[$pos]) ? $key_ar[$pos] : "";
        $route_ar[$pos] = isset($route_ar[$pos]) ? $route_ar[$pos] : "";

        if ($key_ar[$pos] == $route_ar[$pos]) {
            return true;
        } else if (preg_match('/\{([a-zA-Z\_\-]+)\}/i', $key_ar[$pos], $match_ar) && $route_ar[$pos]) {
            $this->uriVariablesAr[$match_ar[1]] = $route_ar[$pos];
            return true;
        }
        return false;
    }

    public function getModulesAllAr(): array
    {
        return $this->modulesAllAr;
    }

    public function getAllRoutesAr(): array
    {
        return $this->allRoutesAr;
    }

    public function getUriOnly(): string
    {
        return $this->uriOnly;
    }

    public function getUriRoute(): string
    {
        return $this->uriRoute;
    }

    public function getUriRouteInfoAr(): array
    {
        return $this->uriRouteInfoAr;
    }

    public function getUriVariablesAr(): array
    {
        return $this->uriVariablesAr;
    }

    public function getUriMethod(): string
    {
        return $this->AppInit->getUriMethod();
    }

    public function mkUrl(int $userIndex, string $uriRoute, array $valuesAr = [], array $getsAr = [], bool $permittedOnly = false): string
    {
        $url = "#";
        $arrayKeys_ar = [];

        foreach (array_keys($valuesAr) as $det) {
            $arrayKeys_ar[] = '{' . $det . '}';
        }

        if (substr($uriRoute, 0, 4) == "http" || substr($uriRoute, 0, 2) == "//") {
            return $uriRoute;
        }

        global $Auth;

        if ($this->allRoutesAr[$uriRoute]) { // todo: Permission Module Development Required
            //$detectedPerm_ar = $Auth->getDetectedPermissionAr();
            //$requiredPerm_ar = array_filter(array_map("trim", explode(",", $this->allRoutesAr[$uriRoute]['perm'])));

            //--Check Required Permission
            //if (empty(array_diff($requiredPerm_ar, $detectedPerm_ar)) && !$permittedOnly) {
            //dc("Permitted", true);
            $url = "/" . str_replace($arrayKeys_ar, array_values($valuesAr), $uriRoute) . "/" . (!empty($getsAr) ? "?" . http_build_query($getsAr) : "");
            //} else {
            //dc("Not Permitted", true);
            //$url = "#PERM";
            //}
        }

        $url = ($url == "#" ? "#" : ($userIndex ? "/u-" . $userIndex : "") . $url);
        return str_replace(["///", "///"], ["/", "/"], $url);
    }

    public function getControllerPath(): string
    {
        return $this->controllerPath;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPageTitle(): string
    {
        return $this->uriRouteInfoAr['pageTitle'];
    }

    public function getPermAllAr(): array
    {
        return array_filter($this->permAll_ar);
    }
}