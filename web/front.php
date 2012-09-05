<?php
require_once __DIR__ . '/../vendor/autoload.php';
 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;

function render_template($request)
{
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__.'/../src/pages/%s.php', $_route);
 
    return new Response(ob_get_clean());
}

$routes = include __DIR__.'/../src/app.php';

$request = Request::createFromGlobals();

$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
try {
 	$request->attributes->add($matcher->match($request->getPathInfo()));
	$response = call_user_func($request->attributes->get('_controller'), $request);
} catch (Routing\Exception\ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
} catch (Exception $e) {
    $response = new Response('An error occurred', 500);
}
 
$response->send();
