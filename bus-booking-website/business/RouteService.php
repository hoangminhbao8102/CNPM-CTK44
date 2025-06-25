<?php
require_once __DIR__ . '/../access/RouteRepository.php';
require_once __DIR__ . '/../models/Route.php';

class RouteService {
    public function listRoutes() {
        $repo = new RouteRepository();
        return $repo->getAllRoutes();
    }

    public function addNewRoute($start, $end) {
        $route = new Route();
        $route->DiemDi = $start;
        $route->DiemDen = $end;

        $repo = new RouteRepository();
        return $repo->addRoute($route);
    }
}
?>
