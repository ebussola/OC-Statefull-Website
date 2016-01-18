<?php

if (!function_exists('isParamBlacklisted')) {
    function isParamBlacklisted($paramBlacklist) {
        return array_reduce(
            $paramBlacklist,
            function($result, $param) {
                if (!$result) {
                    $isNameBlacklisted = $result != in_array($param['name'], array_keys($_GET));

                    if ($isNameBlacklisted) {
                        $values = json_decode($param['values'], true);

                        return count($values) === 0 ?: in_array($_GET[$param['name']], array_map(function($item) { return $item['value']; }, $values));
                    }
                    else {
                        return false;
                    }
                }

                return true;
            },
            false
        );
    };
}