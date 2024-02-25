<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/api/weather', function (Request $request, Response $response) {
        
        // Check if latitude and longitude values are present
        if (!isset($queryParams['lat']) || !isset($queryParams['lon'])) {
            $errorResponse = [
                'error' => 'Latitude and longitude values are required.'
            ];
            $response->getBody()->write(json_encode($errorResponse));
        
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
           
        // Retrieve the latitude and longitude values
        $latitude = $request->getQueryParams()['lat'];
        $longitude = $request->getQueryParams()['lon'];
    
        // Make a request to the OpenWeatherMap API
        $apiKey = 'da6aea7789902d3e923eb2a61040704b';
        $url = "https://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&appid=$apiKey";
        $weatherData = file_get_contents($url);
    
        // Return the weather data as JSON
        $response->getBody()->write($weatherData);
        return $response->withHeader('Content-Type', 'application/json');
    });
    $app->run();
    
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world! youmna');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
