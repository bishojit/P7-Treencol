<?php

namespace Core;

use Packages\mysql\QuerySelect;

class Auth
{
    private SystemDefaults $SystemDefaults;
    private int $userSl = 0;
    private array $userInfoAr = [];
    private array $requiredPermission_ar = [];
    private array $detectedPermission_ar = [];
    private array $remainPermission_ar = [];
    private string $uriMethod = "";

    public function __construct(SystemDefaults $SystemDefaults, int $userIndex, string $uriMethod)
    {
        global $SoftInfo;
        $this->uriMethod = strtoupper($uriMethod);

        $this->SystemDefaults = $SystemDefaults;
        //$multiUser = $this->SystemDefaults->getMultiUser();

        //--Pick User SL
        $this->userSl = $this->pickUserSl($userIndex);

        //--Collect UserInfo
        $this->userInfoAr = getRow('system_users', $this->userSl);

        $Route = route();
        if ($Route->getUriRouteInfoAr()['auth'] == "true") { // is required auth

            //--Collect Required Permission
            $this->requiredPermission_ar = array_map("trim", explode(",", $Route->getUriRouteInfoAr()['perm']));

            //--Collect Detected Permission
            $this->detectedPermission_ar = array_map("trim", explode(",", $this->userInfoAr['permission']));

            if ($this->userInfoAr['type'] == "develop" || $this->userInfoAr['type'] == "admin") {
                $this->detectedPermission_ar = route()->getPermAllAr();
            }

            $this->detectedPermission_ar[] = $this->userInfoAr['type'];
            $this->detectedPermission_ar = array_values(array_filter($this->detectedPermission_ar));
            $this->detectedPermission_ar = array_combine($this->detectedPermission_ar, $this->detectedPermission_ar);

            //--Include Map Permission
            $permMapObj = xmlFileToObject("app/permission-map.xml", null);
            foreach ($permMapObj->add ?: [] as $permObj) {
                $perm = (string)$permObj->attributes();
                $addedPerm = (string)$permObj;
                if ($this->detectedPermission_ar[$perm] && $addedPerm) {
                    $this->detectedPermission_ar[$addedPerm] = $addedPerm;
                }
            }
            foreach ($permMapObj->remove ?: [] as $permObj) {
                $perm = (string)$permObj->attributes();
                $addedPerm = (string)$permObj;
                if ($this->detectedPermission_ar[$perm] && $addedPerm) {
                    unset($this->detectedPermission_ar[$addedPerm]);
                }
            }

            //--Collect Remain Permission
            $this->remainPermission_ar = $this->chkRemainPermission($this->requiredPermission_ar, $this->detectedPermission_ar);
            if (!$SoftInfo->getData()->system->loginUri) {

                ErrorPages::Auth(1, "Please configure default." . getDefaultDomain() . ".xml->system->loginUri", $this);
                exit();
            }

            $message = "";
            if (!$this->userInfoAr) {

                $message = "You are not logged in. Please Login";
            } else if ($this->userInfoAr['time_deleted']) {

                $message = "User not valid";
            } else if ($this->userInfoAr['status'] != "active") {

                $message = "User not active";
            } else if (!empty($this->remainPermission_ar)) {

                ErrorPages::Auth(2, "Invalid Permission or More Permission Required (" . implode(", ", $this->remainPermission_ar) . ")", $this);
            }

            if ($message) {
                if ($this->uriMethod == "GET") {
                    $configLoginRoute = $SoftInfo->getData()->system->loginUri;
                    $currentUrl = mkUrl(route()->getUriRoute(), route()->getUriVariablesAr(), $_GET);
                    $loginPage = mkUrl($configLoginRoute, [], ['url' => $currentUrl]);

                    if ($loginPage != "#") {
                        header("Location: {$loginPage}");
                    } else {
                        ErrorPages::Auth(2, "Invalid Login Route in " . getDefaultDomain() . ".softinfo.xml file ->system->loginUri", $this);
                    }
                } else {
                    echo json_encode([
                        'error' => 2,
                        'message' => $message
                    ]);
                }
                //message("User not active"); //todo: error-code required
                exit();
            } else if (!empty($this->remainPermission_ar)) {

                ErrorPages::Auth(2, "Invalid Permission or More Permission Required", $this);
            }

            //--Set Timezone
            global $TimeZone;
            if ($this->userInfoAr['time_zone']) {

                $TimeZone->setTimeZone($this->userInfoAr['time_zone']);
            }
        }
    }

    private function pickUserSl($userIndex): int
    {
        //--If Session Access Activated
        if ($this->SystemDefaults->isSessionAccess() && !empty($_SESSION['user_sl_ar'])) {
            //--Session Auth
            return (int)array_values($_SESSION['user_sl_ar'] ?: [])[$userIndex];
        }

        if ($this->SystemDefaults->isTokenAccess()) {
            //--Collect Token Info
            $select = new QuerySelect("system_users_login");
            $select->setQueryString("
            SELECT `user_sl`
            FROM `system_users_login`
            WHERE `unique_key`=" . quote(getallheaders()[$this->SystemDefaults->getTokenName()]) . "
            ");
            $select->pull();
            $userSl = (int)array_values($select->getColValues('user_sl'))[$userIndex] ?: 0;

            if ($userSl) {
                header("Secure-Auth: true");
            }

            //dd($select->getQueryString());

            return $userSl;
        }

        return 0;
    }

    private function chkRemainPermission($requiredPermission_ar, $detectedPermission_ar)
    {
        $remainPermission_ar = [];
        foreach ($requiredPermission_ar as $permission) {
            if ($permission && !in_array($permission, $detectedPermission_ar))
                $remainPermission_ar[$permission] = $permission;
        }

        return $remainPermission_ar;
    }

    public function getUserSl(): int
    {
        return $this->userSl;
    }

    public function getUserInfoAr(): array
    {
        return $this->userInfoAr;
    }

    public function getRequiredPermissionAr(): array
    {
        return $this->requiredPermission_ar;
    }

    public function getDetectedPermissionAr(): array
    {
        return $this->detectedPermission_ar;
    }

    public function isAdminPerm(): bool
    {
        if (in_array("admin", $this->detectedPermission_ar)) {
            return true;
        }

        return false;
    }

    public function isDeveloperPerm()
    {

        if (in_array("developer", $this->detectedPermission_ar)) {
            return true;
        }

        return false;
    }
}