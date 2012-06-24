<?php

class Routing extends Application {

  public static function fetch_uri($config) {

    // Get request from server, split into segments, store as controller,
    // view, id and params
    $request = substr($_SERVER['REQUEST_URI'], (strlen($_SERVER['PHP_SELF']) - 10));

    // Split at '.'
    $dot_split = preg_split("/\./", $request);

    // Grab format from after '.' but before '?'
    if (isset($dot_split[1])) {
      $format_split = preg_split("/\?/", $dot_split[1]);
      $format = $format_split[0];
    } else {
      $format = NULL;
    }

    // Split request at each '/' to obtain route (using everything before '.')
    $segments = preg_split("/\//", $dot_split[0]);

    // Set up uri variable to pass to app

    $uri['controller'] = $segments[1];

    if (isset($segments[2])) {
      $uri['action'] = $segments[2];
    } else {
      $uri['action'] = NULL;
    }

    $uri['format'] = $format;
    $uri['params'] = array_map('htmlentities', $_GET);

    if (isset($segments[3])) {
      $uri['params']['id'] = $segments[3];
    } else {
      $uri['params']['id'] = NULL;
    }

    // Set the controller to the default if not in URI
    if (empty($uri['controller'])) {
      $uri['controller'] = $config->default_controller;
    }

    return $uri;

  }

  public static function route() {

    require_once 'config/routes.php';
    $routes = new Routes();

    // Get request from server and remove BASE_DIR
    $request = substr($_SERVER['REQUEST_URI'], (strlen($_SERVER['PHP_SELF']) - 10));

    // Remove everything after '?'
    $request = preg_split("/\?/", $request);
    $request = $request[0];

    // Remove trailing slash if present
    if (substr($request, -1) == '/') {
      $request = substr($request, 0, -1);
    }

    // Split at the '.' to obtain the request format
    $format = preg_split("/\./", $request);
    $request = $format[0];
    if (isset($format[1])) {
      $format = $format[1];
    } else {
      $format = NULL;
    }

    $routeFound = FALSE;

    foreach ($routes->aliases as $k => $v) {

      // Swap asterisks for valid regex
      $k = str_replace("*", "([a-zA-Z0-9]+)", $k);

      // Match the request against current route
      if (preg_match('|^' . $k . '$/?|', $request, $matches)) {

        $uri['controller'] = $v['controller'];
        $uri['action'] = $v['action'];

        // Assign components of $uri['params'] based on array in
        // routes class
        foreach ($v as $k => $v) {

          if ($k != 'controller' && $k != 'action') {

            // Convert $x to xth parameter
            if (strstr($v, "$")) {
              $i = substr($v, 1);
              $v = $matches[$i];
            }

            $uri['params'][$k] = $v;

          }

        }

        $uri['format'] = $format;
        $routeFound = TRUE;
        break;

      }

    }

    if ( ! $routeFound) {
      throw new RoutingException($uri, "Page not found");
    }

    return $uri;

  }

  public static function url_for($controller, $action = '', $id = '', $params = array()) {

    // Create $uri array containing controller, action and all other
    // params including id

    $uri = array('controller' => $controller);

    if ( ! empty($action)) {
      $uri['action'] = $action;
    }

    if ( ! empty($id)) {
      $params['id'] = $id;
    }

    foreach ($params as $k => $v) {
      $uri[$k] = $v;
    }

    require_once 'config/routes.php';
    $routes = new Routes();

    // Search through all route targets and find the last one to match
    // both the controller, action and the names of additional parameters
    $targets = array_values($routes->aliases);
    $match = NULL;
    $size = count($targets);

    for ($i = 0; $i < $size; $i++) {

      // Find all the additional parameter names (excluding controller
      // and action)
      $additional_param_names = array_diff(array_keys($targets[$i]), array('controller', 'action'));

      // If the difference between the additional target params and the
      // additional input params is empty, then they have the same
      // additional params

      $diff = array_diff($additional_param_names, array_keys($params));

      $param_values_match = TRUE;
      if (empty($diff)) {
        // Same params set so check that values match
        foreach ($additional_param_names as $key => $value) {
          if ($uri[$value] != $targets[$i][$value]) {
            $param_values_match = FALSE;
          }
        }
      }

      if (
        (isset($uri['controller']) && $targets[$i]['controller'] == $uri['controller']) // controllers match
        && (isset($uri['action']) && isset($targets[$i]['action']) && $uri['action'] == $targets[$i]['action'] ) // actions match
        && empty($diff) // remaining params match (diff is empty)
        && $param_values_match
      ) {

        $match = $i;

      }

    }

    // If a matching target is found, the route can be condensed
    if ( ! is_null($match)) {

      // Find the condensed route, route_format
      $route_format = array_keys($routes->aliases);
      $route_format = $route_format[$match];

      // Replace each successive occurrence of '*' in the condensed
      // route with the correct value
      $route = $route_format;
      $count = substr_count($route_format, "*");

      for ($i = 1; $i <= $count; $i++) {
        $route = preg_replace('/\*/', $uri[array_search("$$i", $routes->aliases[$route_format])], $route, 1);
      }

      // Condensed routes all preceded by '/' so remove it
      if (substr($route, 0, 1) == '/') {
        $route = substr($route, 1);
      }

    } else {

      // Construct the standard route
      $route = $controller;

      if ( ! empty($action)) {
        $route .= "/$action";
      }

      if ( ! empty($id)) {
        $route .= "/$id";
      }

    }

    return BASE_DIR . $route;

  }

}
