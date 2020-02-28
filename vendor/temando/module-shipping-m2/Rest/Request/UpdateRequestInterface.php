<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Rest\Request;

/**
 * Temando API Update Operation Parameters
 *
 * @package Temando\Shipping\Rest
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.temando.com/
 */
interface UpdateRequestInterface
{
    /**
     * Obtain url path parameters, i.e. entity ids, in order of appearance.
     *
     * @return string[]
     */
    public function getPathParams();

    /**
     * Obtain raw post data.
     *
     * @return mixed
     */
    public function getRequestBody();
}
