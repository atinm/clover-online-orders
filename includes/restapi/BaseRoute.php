<?php
/**
 * Created by Mohammed EL BANYAOUI.
 * User: Smart MerchantApps
 * Date: 3/5/2019
 * Time: 12:44 PM
 */

class BaseRoute
{
    /*
     * isProduction : it's a flag to hide all php notices in production mode
     */
    protected $isProduction = true;

    /**
     * The namespace and the version of the api
     * @var string
     */
    protected $namespace = 'moo-clover/v2';

}