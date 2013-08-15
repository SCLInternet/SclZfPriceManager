<?php

namespace SclZfPriceManager\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * PriceManagerOptions
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class PriceManagerOptions extends AbstractOptions implements
    PriceManagerOptionsInterface
{
    protected $defaultProfile;

    /**
     * {@inheritDoc}
     *
     * @param  int  $profile
     * @return void
     */
    public function setDefaultProfile($profile)
    {
        $this->defaultProfile = $profile;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function getDefaultProfile()
    {
        return $this->defaultProfile;
    }
}
