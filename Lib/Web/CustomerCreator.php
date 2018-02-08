<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web;

use PizzaService\Propel\Models\City;
use PizzaService\Propel\Models\CityName;
use PizzaService\Propel\Models\CityNameQuery;
use PizzaService\Propel\Models\CityQuery;
use PizzaService\Propel\Models\Country;
use PizzaService\Propel\Models\CountryQuery;
use PizzaService\Propel\Models\Customer;
use PizzaService\Propel\Models\CustomerAddress;
use PizzaService\Propel\Models\CustomerAddressQuery;
use PizzaService\Propel\Models\CustomerName;
use PizzaService\Propel\Models\CustomerNameQuery;
use PizzaService\Propel\Models\CustomerQuery;
use PizzaService\Propel\Models\FirstName;
use PizzaService\Propel\Models\FirstNameQuery;
use PizzaService\Propel\Models\HouseNumber;
use PizzaService\Propel\Models\HouseNumberQuery;
use PizzaService\Propel\Models\LastName;
use PizzaService\Propel\Models\LastNameQuery;
use PizzaService\Propel\Models\Street;
use PizzaService\Propel\Models\StreetName;
use PizzaService\Propel\Models\StreetNameQuery;
use PizzaService\Propel\Models\StreetQuery;
use PizzaService\Propel\Models\Zip;
use PizzaService\Propel\Models\ZipQuery;

/**
 * Creates a new Customer object from a list of values.
 */
class CustomerCreator
{
    /**
     * Returns the Customer object for a customer.
     *
     * @param String $_firstName The first name of the customer
     * @param String $_lastName The last name of the customer
     * @param String $_countryName The country name of the customers country
     * @param String $_cityName The city name of the customers city
     * @param String $_zip The city zip of the customers city
     * @param String $_streetName The name of the customers city
     * @param String $_houseNumber The house number of the customer
     *
     * @return Customer The customer object
     *
     * @throws \PropelException
     */
    public function getCustomer(String $_firstName, String $_lastName, String $_countryName, String $_cityName, String $_zip, String $_streetName, String $_houseNumber): Customer
    {
        $customerName = $this->getCustomerName($_firstName, $_lastName);
        $customerAddress = $this->getCustomerAddress($_countryName, $_cityName, $_zip, $_streetName, $_houseNumber);

        $customer = CustomerQuery::create()->filterByCustomerName($customerName)
                                           ->filterByCustomerAddress($customerAddress)
                                           ->findOne();

        if (! $customer)
        {
            $customer = new Customer();
            $customer->setCustomerName($customerName)
                     ->setCustomerAddress($customerAddress);
        }

        return $customer;
    }

    /**
     * Returns the FirstName object for the first name of the customer.
     *
     * @param String $_firstName The first name of the customer
     *
     * @return FirstName The first name object
     */
    private function getFirstName(String $_firstName): FirstName
    {
        $firstName = FirstNameQuery::create()->findOneByName($_firstName);

        if (! $firstName)
        {
            $firstName = new FirstName();
            $firstName->setName($_firstName);
        }

        return $firstName;
    }

    /**
     * Returns the LastName object for the last name of the customer.
     *
     * @param String $_lastName The last name of the customer
     *
     * @return LastName The last name object
     */
    private function getLastName(String $_lastName): LastName
    {
        $lastName = LastNameQuery::create()->findOneByName($_lastName);

        if (! $lastName)
        {
            $lastName = new LastName();
            $lastName->setName($_lastName);
        }

        return $lastName;
    }

    /**
     * Returns the CustomerName object for the first/last name combination of the customer.
     *
     * @param String $_firstName The first name of the customer
     * @param String $_lastName The last name of the customer
     *
     * @return CustomerName The customer name object
     *
     * @throws \PropelException
     */
    private function getCustomerName(String $_firstName, String $_lastName): CustomerName
    {
        $firstName = $this->getFirstName($_firstName);
        $lastName = $this->getLastName($_lastName);

        $customerName = CustomerNameQuery::create()->filterByFirstName($firstName)
                                                   ->filterByLastName($lastName)
                                                   ->findOne();

        if (! $customerName)
        {
            $customerName = new CustomerName();
            $customerName->setFirstName($firstName)
                         ->setLastName($lastName);
        }

        return $customerName;
    }

    /**
     * Returns the Country object for the country of the customer.
     *
     * @param String $_countryName The country name of the customers country
     *
     * @return Country The country object
     */
    private function getCountry(String $_countryName): Country
    {
        $country = CountryQuery::create()->findOneByName($_countryName);

        if (! $country)
        {
            $country = new Country();
            $country->setName($_countryName);
        }

        return $country;
    }

    /**
     * Returns the CityName object for the city name of the customers city.
     *
     * @param String $_cityName The city name of the customers city
     *
     * @return CityName The city name object
     */
    private function getCityName(String $_cityName): CityName
    {
        $cityName = CityNameQuery::create()->findOneByName($_cityName);

        if (! $cityName)
        {
            $cityName = new CityName();
            $cityName->setName($_cityName);
        }

        return $cityName;
    }

    /**
     * Returns the Zip object for the city zip of the customers city.
     *
     * @param String $_zip The city zip of the customers city
     *
     * @return Zip The zip object
     */
    private function getZip(String $_zip): Zip
    {
        $zip = ZipQuery::create()->findOneByZip($_zip);

        if (! $zip)
        {
            $zip = new Zip();
            $zip->setZip($_zip);
        }

        return $zip;
    }

    /**
     * Returns the City object for the customers city.
     *
     * @param String $_cityName The city name of the customers city
     * @param String $_zip The zip of the customers city
     *
     * @return City The city object
     *
     * @throws \PropelException
     */
    private function getCity(String $_cityName, String $_zip): City
    {
        $cityName = $this->getCityName($_cityName);
        $zip = $this->getZip($_zip);

        $city = CityQuery::create()->filterByCityName($cityName)
                                   ->filterByZip($zip)
                                   ->findOne();

        if (! $city)
        {
            $city = new City();
            $city->setCityName($cityName)
                 ->setZip($zip);
        }

        return $city;
    }

    /**
     * Returns the StreetName object for the customers street.
     *
     * @param String $_streetName The name of the customers street
     *
     * @return StreetName The street name object
     */
    private function getStreetName(String $_streetName): StreetName
    {
        $streetName = StreetNameQuery::create()->findOneByName($_streetName);

        if (! $streetName)
        {
            $streetName = new StreetName();
            $streetName->setName($_streetName);
        }

        return $streetName;
    }

    /**
     * Returns the HouseNumber object for the customers street.
     *
     * @param String $_houseNumber The house number of the customers street
     *
     * @return HouseNumber The house number object
     */
    private function getHouseNumber(String $_houseNumber): HouseNumber
    {
        $houseNumber = HouseNumberQuery::create()->findOneByNumber($_houseNumber);

        if (! $houseNumber)
        {
            $houseNumber = new HouseNumber();
            $houseNumber->setNumber($_houseNumber);
        }

        return $houseNumber;
    }

    /**
     * Returns the Street object for the customers street.
     *
     * @param String $_streetName The street name of the customers street
     * @param String $_houseNumber The house number of the customers street
     *
     * @return Street The street object
     *
     * @throws \PropelException
     */
    private function getStreet(String $_streetName, String $_houseNumber): Street
    {
        $streetName = $this->getStreetName($_streetName);
        $houseNumber = $this->getHouseNumber($_houseNumber);

        $street = StreetQuery::create()->filterByStreetName($streetName)
                                       ->filterByHouseNumber($houseNumber)
                                       ->findOne();

        if (! $street)
        {
            $street = new Street();
            $street->setStreetName($streetName)
                   ->setHouseNumber($houseNumber);
        }

        return $street;
    }

    /**
     * Returns the CustomerAddress object for the customers address.
     *
     * @param String $_countryName The customers country name
     * @param String $_cityName The customers city name
     * @param String $_zip The customers city zip
     * @param String $_streetName The customers street name
     * @param String $_houseNumber The customers house number
     *
     * @return CustomerAddress The customer address object
     *
     * @throws \PropelException
     */
    private function getCustomerAddress(String $_countryName, String $_cityName, String $_zip, String $_streetName, String $_houseNumber): CustomerAddress
    {
        $country = $this->getCountry($_countryName);
        $city = $this->getCity($_cityName, $_zip);
        $street = $this->getStreet($_streetName, $_houseNumber);

        $customerAddress = CustomerAddressQuery::create()->filterByCountry($country)
                                                         ->filterByCity($city)
                                                         ->filterByStreet($street)
                                                         ->findOne();

        if (! $customerAddress)
        {
            $customerAddress = new CustomerAddress();
            $customerAddress->setCountry($country)
                            ->setCity($city)
                            ->setStreet($street);
        }

        return $customerAddress;
    }
}
