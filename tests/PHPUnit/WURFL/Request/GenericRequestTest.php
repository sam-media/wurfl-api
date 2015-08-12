<?php

/**
 * test case
 */
class WURFL_Request_GenericRequestTest extends PHPUnit_Framework_TestCase
{
    function testShouldNormalizeHeaders()
    {

        $headers = array(
          "REQUEST_START_TIME" => 1429192614,
          "REQUEST_TIME" => 1429192614,
          "REQUEST_TIME_FLOAT" => 1429192614.5303,
          "HTTP_ACCEPT_LANGUAGE" => "en-us",
          "HTTP_ACCEPT" => "*/*",
          "HTTP_X_FORWARDED_PROTO" => "http",
          "HTTP_CONNECTION" => "keep-alive",
          "HTTP_REFERER" => "http://example.com",
          "HTTP_X_FORWARDED_PORT" => "80",
          "HTTP_ACCEPT_ENCODING" => "gzip, deflate",
          "HTTP_X_FORWARDED_FOR" => "127.0.1.1",
          "HTTP_CONTENT_TYPE" => "",
          "HTTP_CONTENT_LENGTH" => "",
          "HTTP_USER_AGENT" => "Mozilla/5.0 (iPad; CPU OS 7_1_1 like Mac OS X)",
          "HTTP_HOST" => "example.com",
          "GATEWAY_INTERFACE" => "CGI/1.1",
          "SERVER_ADDR" => "10.0.0.1",
          "SERVER_NAME" => "",
          "SERVER_PORT" => "80",
          "SERVER_SOFTWARE" => "nginx/1.4.6",
          "SERVER_PROTOCOL" => "HTTP/1.1",
          "SERVER_ADMIN" => "",
          "SERVER_SIGNATURE" => "",
          "REDIRECT_STATUS" => "200",
          "CONTENT_LENGTH" => "",
          "REQUEST_METHOD" => "GET",
          "DOCUMENT_URI" => "/example.php",
          "SCRIPT_NAME" => "/example.php",
          "REMOTE_PORT" => 11111,
          "REQUEST_URI" => "/example.php",
          "REMOTE_ADDR" => "10.0.0.1",
          "CONTENT_TYPE" => "",
          "DOCUMENT_ROOT" => "/var/www/example",
          "QUERY_STRING" => "",
          "SCRIPT_FILENAME" => "/var/www/example/web/example.php",
          "SCRIPT_URL" => "/example.php",
          "SCRIPT_URI" => "http://example.com/example.php",
          "PHP_SELF" => "/example.php",
          "PATH_TRANSLATED" => "/var/www/example/web/example.php",
          "HTTPS" => "",
          "argv" => [],
          "argc" => 1,
          "THREAD_TYPE" => "Web Request"
        );
        $ua = "Mozilla/5.0 (iPad; CPU OS 7_1_1 like Mac OS X)";

        $request_factory = new WURFL_Request_GenericRequestFactory();
        $request = $request_factory->createRequest($headers);
        $this->assertEquals($ua, $request->userAgent);
    }
}
