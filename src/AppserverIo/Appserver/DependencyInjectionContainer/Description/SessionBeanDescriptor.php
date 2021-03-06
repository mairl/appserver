<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\Description\SessionBeanDescriptor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\DependencyInjectionContainer\Description;

use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PreDestroy;
use AppserverIo\Psr\EnterpriseBeans\Annotations\PostConstruct;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\BeanDescriptorInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\SessionBeanDescriptorInterface;

/**
 * Implementation for an abstract session bean descriptor.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class SessionBeanDescriptor extends BeanDescriptor implements SessionBeanDescriptorInterface
{

    /**
     * The beans session type.
     *
     * @var string
     */
    protected $sessionType;

    /**
     * The local interface name.
     *
     * @var string
     */
    protected $local;

    /**
     * The remote interface name.
     *
     * @var string
     */
    protected $remote;

    /**
     * The array with the post construct callback method names.
     *
     * @var array
     */
    protected $postConstructCallbacks = array();

    /**
     * The array with the pre destroy callback method names.
     *
     * @var array
     */
    protected $preDestroyCallbacks = array();

    /**
     * Sets the beans session type.
     *
     * @param string $sessionType The beans session type
     *
     * @return void
     */
    public function setSessionType($sessionType)
    {
        $this->sessionType = $sessionType;
    }

    /**
     * Returns the beans session type.
     *
     * @return string The beans session type
     */
    public function getSessionType()
    {
        return $this->sessionType;
    }

    /**
     * Sets the local interface name.
     *
     * @param string $local The local interface name
     *
     * @return void
     */
    public function setLocal($local)
    {
        $this->local = $local;
    }

    /**
     * Returns the local interface name.
     *
     * @return string The local interface name
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Sets the remote interface name.
     *
     * @param string $remote The remote interface name
     *
     * @return void
     */
    public function setRemote($remote)
    {
        $this->remote = $remote;
    }

    /**
     * Returns the remote interface name.
     *
     * @return string The remote interface name
     */
    public function getRemote()
    {
        return $this->remote;
    }

    /**
     * Adds a post construct callback method name.
     *
     * @param string $postConstructCallback The post construct callback method name
     *
     * @return void
     */
    public function addPostConstructCallback($postConstructCallback)
    {
        $this->postConstructCallbacks[] = $postConstructCallback;
    }

    /**
     * Adds a pre destroy callback method name.
     *
     * @param string $preDestroyCallback The pre destroy callback method name
     *
     * @return void
     */
    public function addPreDestroyCallback($preDestroyCallback)
    {
        $this->preDestroyCallbacks[] = $preDestroyCallback;
    }

    /**
     * Sets the array with the post construct callback method names.
     *
     * @param array $postConstructCallbacks The post construct callback method names
     *
     * @return void
     */
    public function setPostConstructCallbacks(array $postConstructCallbacks)
    {
        $this->postConstructCallbacks = $postConstructCallbacks;
    }

    /**
     * The array with the post construct callback method names.
     *
     * @return array The post construct callback method names
     */
    public function getPostConstructCallbacks()
    {
        return $this->postConstructCallbacks;
    }

    /**
     * Sets the array with the pre destroy callback method names.
     *
     * @param array $preDestroyCallbacks The pre destroy callback method names
     *
     * @return void
     */
    public function setPreDestroyCallbacks(array $preDestroyCallbacks)
    {
        $this->preDestroyCallbacks = $preDestroyCallbacks;
    }

    /**
     * The array with the pre destroy callback method names.
     *
     * @return array The pre destroy callback method names
     */
    public function getPreDestroyCallbacks()
    {
        return $this->preDestroyCallbacks;
    }

    /**
     * Initializes the bean descriptor instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the bean description
     *
     * @return void
     *
     * @throws \Exception
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // initialize the bean descriptor
        parent::fromReflectionClass($reflectionClass);

        if ($reflectionClass->hasAnnotation('Local')) {
            throw new \Exception('@Local annotation not implemented');
        } else {
            $this->setLocal(sprintf('%sLocal', rtrim($this->getName(), 'Bean')));
        }

        if ($reflectionClass->hasAnnotation('Remote')) {
            throw new \Exception('@Remote annotation not implemented');
        } else {
            $this->setRemote(sprintf('%sRemote', rtrim($this->getName(), 'Bean')));
        }

        // we've to check for a @PostConstruct or @PreDestroy annotation
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            // if we found a @PostConstruct annotation, invoke the method
            if ($reflectionMethod->hasAnnotation(PostConstruct::ANNOTATION)) {
                $this->addPostConstructCallback($reflectionMethod->getMethodName());
            }

            // if we found a @PreDestroy annotation, invoke the method
            if ($reflectionMethod->hasAnnotation(PreDestroy::ANNOTATION)) {
                $this->addPreDestroyCallback($reflectionMethod->getMethodName());
            }
        }
    }

    /**
     * Initializes a bean descriptor instance from the passed deployment descriptor node.
     *
     * @param \SimpleXmlElement $node The deployment node with the bean description
     *
     * @return void
     */
    public function fromDeploymentDescriptor(\SimpleXmlElement $node)
    {
        $node->registerXPathNamespace('a', 'http://www.appserver.io/appserver');

        // initialize the bean descriptor
        parent::fromDeploymentDescriptor($node);

        // query for the session type and set it
        if ($sessionType = (string) $node->{'session-type'}) {
            $this->setSessionType($sessionType);
        }

        // query for the name of the local business interface and set it
        if ($local = (string) $node->{'local'}) {
            $this->setLocal($local);
        } else {
            $this->setLocal(sprintf('%sLocal', rtrim($this->getName(), 'Bean')));
        }

        // query for the name of the remote business interface and set it
        if ($remote = (string) $node->{'remote'}) {
            $this->setRemote($remote);
        } else {
            $this->setRemote(sprintf('%sRemote', rtrim($this->getName(), 'Bean')));
        }

        // initialize the post construct callback methods
        foreach ($node->xpath('a:post-construct/a:lifecycle-callback-method') as $postConstructCallback) {
            $this->addPostConstructCallback((string) $postConstructCallback);
        }

        // initialize the pre destroy callback methods
        foreach ($node->xpath('a:pre-destroy/a:lifecycle-callback-method') as $preDestroyCallback) {
            $this->addPreDestroyCallback((string) $preDestroyCallback);
        }
    }

    /**
     * Merges the passed configuration into this one. Configuration values
     * of the passed configuration will overwrite the this one.
     *
     * @param \AppserverIo\Appserver\DependencyInjectionContainer\Interfaces\BeanDescriptorInterface $beanDescriptor The configuration to merge
     *
     * @return void
     */
    public function merge(BeanDescriptorInterface $beanDescriptor)
    {

        // merge the default bean members by invoking the parent method
        parent::merge($beanDescriptor);

        // merge the session type
        if ($sessionType = $beanDescriptor->getSessionType()) {
            $this->setSessionType($sessionType);
        }

        // merge the post construct callback method names
        foreach ($beanDescriptor->getPostConstructCallbacks() as $postConstructCallback) {
            if (in_array($postConstructCallback, $this->postConstructCallbacks) === false) {
                $this->addPostConstructCallback($postConstructCallback);
            }
        }

        // merge the pre destroy callback method names
        foreach ($beanDescriptor->getPreDestroyCallbacks() as $preDestroyCallback) {
            if (in_array($preDestroyCallback, $this->preDestroyCallbacks) === false) {
                $this->addPreDestroyCallback($preDestroyCallback);
            }
        }
    }
}
