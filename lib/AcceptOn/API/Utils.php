<?php

namespace AcceptOn;

trait Utils
{
    public static function startsWith($haystack, $needle)
    {
        if ($needle === "") {
            return true;
        }

        return strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    private function performGetWithObject($path, $params, $klass)
    {
        return $this->performRequestWithObject("get", $path, $params, $klass);
    }

    private function performGetWithObjects($path, $params, $klass)
    {
        return $this->performRequestWithObjects("get", $path, $params, $klass);
    }

    private function performPostWithObject($path, $params, $klass)
    {
        return $this->performRequestWithObject("post", $path, $params, $klass);
    }

    private function performRequest($requestMethod, $path, $params)
    {
        $request = new \AcceptOn\Request($this, $requestMethod, $path, $this->withEnvironment($params));
        return $request->perform();
    }

    private function performRequestWithObject($requestMethod, $path, $params, $klass)
    {
        $response = $this->performRequest($requestMethod, $path, $params);
        return new $klass($response);
    }

    private function performRequestWithObjects($requestMethod, $path, $params, $klass)
    {
        $response = $this->performRequest($requestMethod, $path, $params);
        $objects = array();


        foreach ($response->data as $idx =>$object) {
            array_push($objects, new $klass($object));
        }

        return $objects;
    }

    private function withEnvironment($params)
    {
        $params["environment"] = $this->environment;
        return $params;
    }
}