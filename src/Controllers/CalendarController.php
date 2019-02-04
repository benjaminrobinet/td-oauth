<?php
/**
 * Created by PhpStorm.
 * User: benjaminrobinet
 * Date: 2019-02-04
 * Time: 16:30
 */

namespace Controllers;


use DateTimeInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class CalendarController
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response){
        if(isset($_SESSION['google_data']->access_token)){
            $minTime = date(DateTimeInterface::RFC3339, time());
            $maxTime = date(DateTimeInterface::RFC3339, strtotime("+1 month", time()));
            $url = "https://www.googleapis.com/calendar/v3/calendars/primary/events?timeMin=" . urlencode($minTime) . "&timeMax=" . urlencode($maxTime) . "&access_token=" . $_SESSION['google_data']->access_token;
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $rep = curl_exec($curl);
            $rep = json_decode($rep);
            $data = $rep->items;
            $this->container->renderer->render($response, 'calendar.phtml', $data);
        } else {
            echo 'You are not logged in. <a href="http://localhost:8080/login">Connexion</a>';
        }
    }

    public function add(Request $request, Response $response){
        if(isset($_SESSION['google_data']->access_token)){
            $url = "https://www.googleapis.com/calendar/v3/calendars/primary/events?access_token=" . $_SESSION['google_data']->access_token;
            $content = json_encode([
                "start" => ["date" => "2019-04-01"],
                "end" => ["date" => "2019-04-01"],
                "summary" => "Have fun with mummy"
            ]);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $rep = curl_exec($curl);
            $rep = json_decode($rep);
            if($rep->id){
                echo 'Event added! <a href="/calendar">Retour</a>';
            } else {
                echo 'Erreur';
            }
        } else {
            echo 'You are not logged in. <a href="http://localhost:8080/login">Connexion</a>';
        }
    }
}