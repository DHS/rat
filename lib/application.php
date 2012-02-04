<?php

class Application {
    
    public $uri, $config;
    
    private function __construct() {}
    
    public static function initialise() {
        
        require_once 'config/server.php';
        require_once 'config/application.php';
            
        $config = new AppConfig;

        try {
            
            $uri = Application::fetch_uri($config);
            
            $controller = ucfirst($uri['controller']).'Controller';
            @include "controllers/{$uri['controller']}_controller.php";
            
            if (substr($uri['action'], 0, 1) == '?') {
                $uri['action'] = '';
            }
            
            if (empty($uri['action']) && method_exists($controller, 'index')) {
                $uri['action'] = 'index';
            }
            
            // If controller found and action exists
            if (class_exists($controller) && method_exists($controller, $uri['action'])) {
                
                $app = new $controller;
                
            } else {
                
                $uri = Application::route();
                
                $controller = ucfirst($uri['controller']).'Controller';
                @include "controllers/{$uri['controller']}_controller.php";
                
                $app = new $controller;
                
            }
            
            $app->loadConfig($config);
            $app->loadTwig();
            $app->loadModels();
            $app->loadPlugins();
            
            $app->uri = $uri;
            
            // Helper var to simplify reponding to json
            $app->json = $app->uri['format'] == 'json';  
            
            require_once 'lib/filter.php';
            $app->runFilters();
            
            $app->loadDefaultLibs();
            
            // Set timezone from config
            date_default_timezone_set($config->timezone);
        
            // Call relevant function in controller
            $app->loadAction();
            
            unset($_SESSION['flash']);
            
        } catch (ValidationException $e) {
            
            ob_end_clean();

            Application::flash('error', $e->getMessage());
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            
        } catch (RoutingException $e) {
            
            ob_end_flush();
            
            // RoutingExceptions only thrown from static context
            // so must set up new Application before rendering 404
            $app = new Application;
            $app->loadConfig($config);
            $app->loadView('pages/404');
            
        } catch (ApplicationException $e) {
            
            ob_end_flush();
            
            $e->app->loadView('pages/500');
            
        }
        
    }
    
    private static function fetch_uri($config) {
        
        // Get request from server, split into segments, store as controller, view, id and params
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
    
    private static function route() {
        
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
        $format = $format[1];

        $routeFound = FALSE;
        
        foreach ($routes->aliases as $k => $v) {
            
            // Swap asterisks for valid regex
            $k = str_replace("*", "([a-zA-Z0-9]+)", $k);
            
            // Match the request against current route
            if (preg_match('|^'.$k.'$/?|', $request, $matches)) {
                
                $uri['controller'] = $v['controller'];
                $uri['action'] = $v['action'];
                
                // Assign components of $uri['params'] based on array in routes class
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
        
        if (! $routeFound) throw new RoutingException($uri, "Page not found");
        
        return $uri;
        
    }
    
    private function loadConfig($config) {
        
        $this->config = $config;
        
        // Work out the live domain from config
        $domain = substr($this->config->url, 7);
        
        // Determine if site is live or dev, set site_identifier constant and base_dir
        if ($_SERVER['HTTP_HOST'] == $domain || $_SERVER['HTTP_HOST'] == 'www.'.$domain) {
            define('SITE_IDENTIFIER', 'live');
            $base_dir = $this->config->base_dir;
        } else {
            define('SITE_IDENTIFIER', 'dev');
            $base_dir = $this->config->dev_base_dir;
        }
        
        // Add trailing slash if necessary
        if (substr($base_dir, -1) != '/') {
            $base_dir = $base_dir.'/';
        }
        
        // Set base_dir constant
        $this->config->base_dir = $base_dir;
        
        // Remove this eventually
        define('BASE_DIR', $base_dir);
        
        // Update config->url and append base_dir
        if (SITE_IDENTIFIER == 'live') {
            $this->config->url .= $base_dir;
        } else {
            $this->config->url = $this->config->dev_url . $base_dir;
        }
        
    }
    
    private function loadTwig() {
        
        if ($this->config->theme == 'twig') {
            
            $twig_config['cache'] = 'static/template_cache';
            
            // If we're in dev mode then force template compiling
            if (SITE_IDENTIFIER == 'dev') {
                $twig_config['auto_reload'] = TRUE;
            }
            
            require_once 'lib/twig/Autoloader.php';
            Twig_Autoloader::register();
            $this->twig = new Twig_Environment(new Twig_Loader_Filesystem('themes/'.$this->config->theme), $twig_config);
            
            // Load a separate instance of twig to handle strings
            $this->twig_string = new Twig_Environment(new Twig_Loader_String(), $twig_config);
            
        }
        
    }
    
    public function writeConfig($file, $settings = array()) {
        
        $config_file = $this->twig_string->render(file_get_contents("config/$file.twig"), array('app' => array('config' => $settings)));
        
        $handle = fopen("config/$file.php", 'w');
        fwrite($handle, $config_file);
        fclose($handle);
        
    }
    
    private function loadModels() {
        
        require_once 'lib/mysql.php';
        
        $models = glob('models/*.php' );
        foreach ($models as $model) {
            require_once $model;
        }
                
    }
    
    private function loadPlugins() {
        
        $this->plugins = new StdClass();
        foreach ($this->config->plugins as $key => $value) {
            if ($value == TRUE) {
                require_once "plugins/$key.php";
                $this->plugins->$key = new $key;
            }
        }
        
    }
    
    private function runFilters() {
        
        $uri = $this->uri;
        
        $reflect = new ReflectionClass($this);
        
        foreach ($reflect->getProperties(ReflectionProperty::IS_PROTECTED) as $filter) {
            $filter_name = $filter->getName();
            $filter = new Filter($this);
            $filter->$filter_name($uri, $this->$filter_name);
        }
        
    }
    
    private function loadDefaultLibs() {
        
        require_once 'lib/content.php';     
        require_once 'lib/email.php';
        $this->email = new Email($this);
        
    }
    
    private function loadAction() {
        $param_index = array_keys($this->uri['params']);
        $param_index = $param_index[0];
        if (method_exists($this, $this->uri['action'])) {
            if (isset($this->uri['params'][$param_index])) {
                $this->{$this->uri['action']}($this->uri['params'][$param_index]);
            } else {
                $this->{$this->uri['action']}();
            }
        } elseif (empty($this->uri['action']) && method_exists($this, 'index')) {
            $this->index($this->uri['params'][$param_index]);
        } else {
            throw new RoutingException($uri, "Page not found");
        }
        
    }
    
    protected function loadView($view, $params = NULL, $layout = NULL) {
        
        if (is_null($layout)) {
            $layout = 'default';
        }
        
        if ($this->config->theme == 'twig') {
            
            // Note: the following is hardcoded in ajax methods
            $params['view'] = $view;
            $params['app'] = $this;
            $params['session'] = $_SESSION;
            
            // Hacks for user menu in header
            if (class_exists('User')) {
                $params['user_menu_enabled'] = true;
                if (isset($_SESSION['user_id'])) {
                    $params['viewer'] = User::get_by_id($_SESSION['user_id']);
                }
            }
            
            echo $this->twig->render("layouts/{$layout}.html", $params);
            
        } else {
            
            include "themes/{$this->config->theme}/layouts/{$layout}.php";
            
        }
        
    }
    
    protected function loadPartial($partial) {
        
        include "themes/{$this->config->theme}/partials/{$partial}.php";
        
    }

    public function url_for($controller, $action = '', $id = '', $params = array()) {

        // Create an array, uri, containing controller, action and all other params including id.
        $uri = array('controller' => $controller);
        if (! empty($action)) $uri['action'] = $action;
        if (! empty($id)) $params['id'] = $id;
        foreach ($params as $k => $v) $uri[$k] = $v;

        require_once 'config/routes.php';
        $routes = new Routes();

        // Search through all route targets and find the last one to match both the controller,
        // action and the names of additional parameters
        $targets = array_values($routes->aliases);
        $match = NULL;
        $size = sizeof($targets);
        for ($i = 0; $i < $size; $i++) {
            // Find all the additional parameter names (excluding controller and action)
            $param_names = array_diff(array_keys($targets[$i]), array('controller', 'action'));

            // If the difference between the additional target params and the additional input params is empty,
            // then they have the same additional params
            $diff = array_diff($param_names, array_keys($params));
            if ((! is_null($uri['controller']) && $targets[$i]['controller'] == $uri['controller'])
                    && (! is_null($uri['action']) && $targets[$i]['action'] == $uri['action'])
                    && empty($diff)) {
                $match = $i;
            }
        }

        // If a matching target is found, the route can be condensed
        if (! is_null($match)) {

            // Find the condensed route, route_format
            $route_format = array_keys($routes->aliases);
            $route_format = $route_format[$match];
            
            // Replace each successive occurrence of '*' in the condensed route with the correct value
            $route = $route_format;
            $count = substr_count($route_format, "*");
            for ($i = 1; $i <= $count; $i++) {
                $route = preg_replace('/\*/', $uri[array_search("$$i", $routes->aliases[$route_format])], $route, 1);
            }

            // Condensed routes all preceded by '/' so remove it
            if (substr($route, 0, 1) == '/') $route = substr($route, 1);
            
        } else {

            // Construct the standard route
            $route = $controller;
            if (!empty($action)) $route .= "/$action";
            if (!empty($id)) $route .= "/$id";

        }

        return BASE_DIR . $route;
    
    }
    
    // url_for wrapper for use with Twig
    public function echo_url_for($controller, $action = '', $id = '', $params = array()) {
        
        echo $this->url_for($controller, $action, $id, $params);
        
    }
    
    public function url_for_route($route, array $params) {
        
        foreach ($params as $param) {
            $route = implode($param, explode('*', $route, 2));
        }
        
        return substr(BASE_DIR, 0, -1) . $route;
        
    }
    
    public function link_to($link_text, $controller, $action = '', $id = '', $params = array()) {
        
        echo '<a href="'.$this->url_for($controller, $action, $id, $params).'">'.$link_text.'</a>';
        
    }
    
    public function get_link_to($link_text, $controller, $action = '', $id = '', $params = array()) {
        
        return '<a href="'.$this->url_for($controller, $action, $id, $params).'">'.$link_text.'</a>';
        
    }
    
    public function redirect_to($controller, $action = '', $id = '', $params = array()) {
        
        header('Location: ' . $this->url_for($controller, $action, $id, $params));
        
    }
    
    public static function flash($category, $message) {
        
        if (! in_array($category, array('error', 'notice', 'success'))) {
            $category = 'success';
        }
        
        $_SESSION['flash'] = array('category' => $category, 'message' => $message);
        
    }
    
    public function render_json($ref) {

        if (is_array($ref)) {
            foreach ($ref as $r) {
                unset($r->user);
            }
        } else {
            unset($ref->user);
        }

        echo json_encode($ref);
    
    }
    
}

?>
