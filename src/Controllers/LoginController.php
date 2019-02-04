<?php
/**
 * Created by PhpStorm.
 * User: benjaminrobinet
 * Date: 2019-02-04
 * Time: 15:12
 */

namespace Controllers;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController
{
    private $container;
    private $client_id;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->client_id =  json_decode("{\"installed\":{\"client_id\":\"36304779426-qbqc4g88herrikh64cr30qeee0b6jsi5.apps.googleusercontent.com\",\"project_id\":\"td-oauth-230713\",\"auth_uri\":\"https://accounts.google.com/o/oauth2/auth\",\"token_uri\":\"https://oauth2.googleapis.com/token\",\"auth_provider_x509_cert_url\":\"https://www.googleapis.com/oauth2/v1/certs\",\"client_secret\":\"dhlwRIGJG-lTcUno_fxkmQ8k\",\"redirect_uris\":[\"urn:ietf:wg:oauth:2.0:oob\",\"http://localhost\"]}}");
    }

    public function index(Request $request, Response $response){
        $scope = 'https://www.googleapis.com/auth/calendar';
        $redirect_uri = 'http://localhost:8080/login';

        $data = [];
        $data['url'] = "https://accounts.google.com/o/oauth2/v2/auth?scope=" . $scope . "&redirect_uri=" . $redirect_uri . "&response_type=code&client_id=" . $this->client_id->installed->client_id . "";

        if(!empty($request->getQueryParam('code'))){
            $code = $request->getQueryParam('code');
            $url = "https://www.googleapis.com/oauth2/v4/token";
            $content = "code=" . urlencode($code) . "&client_id=" . urlencode($this->client_id->installed->client_id) . "&client_secret=" . urlencode($this->client_id->installed->client_secret) . "&redirect_uri=" . urlencode($redirect_uri) . "&grant_type=authorization_code";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            $rep = curl_exec($curl);
            $rep = json_decode($rep);

            if(!empty($rep->access_token)){
                $_SESSION['google_data'] = json_decode(json_encode($rep));
                $data['logged_in'] = true;
            }
        }

        $this->container->renderer->render($response, 'login.phtml', $data);
    }
}