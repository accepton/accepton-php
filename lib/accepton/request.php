<?php

namespace AcceptOn;

class Request {

  public $client, $request_method, $headers, $options, $path;


  private $urls = array(
    'development' => 'http://checkout.accepton.dev',
    'staging' => 'https://staging-checkout.accepton.com',
    'production' => 'https://checkout.accepton.com'
  );

  public function __construct($client, $request_method, $path, $options = null) {
      $options = array_merge($this->default_options(), $options);
      $url = $this->urls[$options["environment"]];
      $this->client = $client;
      $this->request_method = strtolower($request_method);
      $uri = Utils::startsWith($path, 'http') ? $path : $url.$path;
      $this->options = $options;
      $this->path = $uri;
      $headers = new \AcceptOn\Headers($client);
      $this->headers = $headers->request_headers();
  }

  public function default_options() {
    return array('environment' => 'production');
  }

  # @return [Hashie::Mash] if the request reutrns a success code
  # @raise [AcceptOn::Error] if the request returns a failure code
  public function perform() {
    $curl = curl_init();

    $headers = array();
    foreach ($this->headers as $key => $value) {
      $headers[] = $key.": ".$value;
    }

    $fields = array();
    foreach ($this->options as $key => $value) {      
      $fields[$key] = urlencode($value);
    }

    //url-ify the data for the POST
    $fields_string  = "";
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    curl_setopt($curl, CURLOPT_URL, $this->path);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // disabling SSL verification (for debug only)
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    // Uncomment for debugging with Fiddler http://www.telerik.com/fiddler
    //curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:8888');

    curl_setopt($curl, CURLOPT_POST, count($fields));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);

    $resp = curl_exec($curl);
    if(!$resp){
      die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
    } else {
      echo $resp;
      return json_decode($resp);
    }
  }

  private static function urlencode_recursive($value) {
    if (is_string($value)) return urlencode($value);
    if (is_array($value)) {
      $array = array();

    }
  }

}
