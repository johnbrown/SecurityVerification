<?php

namespace RB\SecurityVerificationBundle\Services;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use Doctrine\Common\Annotations\Reader;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class SecurityVerifyService
 * @package RB\SecurityVerificationBundle\Services
 * @author  John Brown <brown.john@gmail.com>
 */
class SecurityVerifyService
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $annotationReader;

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     * @param \Doctrine\Common\Annotations\Reader            $annotationReader
     */
    public function __construct(Router $router, Reader $annotationReader)
    {
        $this->router           = $router;
        $this->annotationReader = $annotationReader;
    }

    /**
     * getRoutes
     *
     * gets all the routes
     *
     * @return array
     */
    public function getRoutes()
    {
        $collection = $this->router->getRouteCollection();
        return $collection->all();
    }

    /**
     * getRouteInfo
     *
     * gets info for the routes
     *
     * @return array
     */
    public function getRouteInfo()
    {
        $rawRoutes = $this->getRoutes();
        $routes    = array();
        foreach ($rawRoutes as $route => $params){
            $defaults = $params->getDefaults();
            if (isset($defaults['_controller']))
            {
                $controllerAction = explode(':', $defaults['_controller']);
                $controller = $controllerAction[0];
                $action     = isset($controllerAction[2]) ? $controllerAction[2] : $controllerAction[1];

                if (!isset($routes[$controller])) {
                    $routes[$controller] = array();
                }

                $routes[$controller][$action] = array(
                    'route'      => $route,
                    'controller' => $controller,
                    'action'     => $action,
                );
            }
        }
        return $routes;
    }

    /**
     * getSecurityInfo()
     * @return array
     */
    public function getSecurityInfo()
    {
        $routeInfo = $this->getRouteInfo();
        foreach ($routeInfo as $class => $data) {
            foreach($data as $action => $details) {
                // check to see if the class exists
                if (class_exists($class)) {
                    $method      = new \ReflectionMethod($class, $action);
                    $annotations = $this->annotationReader->getMethodAnnotations($method);
                    $found       = false;
                    foreach($annotations as $annotation) {
                        if($annotation instanceof Secure) {
                            $found = true;
                            $roles = $annotation->roles;
                            $routeInfo[$class][$action]['roles'] = implode(', ', $roles);
                        }
                    }

                    if (!$found) {
                        $routeInfo[$class][$action]['roles'] = 'NOT SECURED';
                    }
                } else {
                    $routeInfo[$class][$action]['roles'] = "CLASS DIES BIT EXIST:" . $class;
                }
            }
        }

        return $routeInfo;
    }
}