<?php
namespace Amazon\Core\Domain;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class AmazonAddressFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var array
     */
    protected $perCountryAddressHandlers;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $perCountryAddressHandlers Per-country custom handlers of incoming address data.
     *                                         The key as an "ISO 3166-1 alpha-2" country code and
     *                                         the value as an FQCN of a child of AmazonAddress.
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $perCountryAddressHandlers = []
    ) {
        $this->objectManager = $objectManager;
        $this->perCountryAddressHandlers = array_change_key_case($perCountryAddressHandlers, CASE_UPPER);
    }

    /**
     * @param array $data
     * @return AmazonAddress
     * @throws LocalizedException
     */
    public function create(array $data = array())
    {
        $instanceClassName = AmazonAddress::class;
        $countryCode = strtoupper($data['address']['CountryCode']);

        if (!empty($this->perCountryAddressHandlers[$countryCode])) {
            $instanceClassName = (string) $this->perCountryAddressHandlers[$countryCode];
        }

        $instance = $this->objectManager->create($instanceClassName, $data);

        if (!$instance instanceof AmazonAddress) {
            throw new LocalizedException(
                __('Address country handler %1 must be of type %2', [$instanceClassName, AmazonAddress::class])
            );
        }

        return $instance;
    }
}
