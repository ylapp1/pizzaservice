<?php
/**
 * @file
 * @version 0.1
 * @copyright 2018 CN-Consult GmbH
 * @author Yannick Lapp <yannick.lapp@cn-consult.eu>
 */

namespace PizzaService\Lib\Web\App\Controller;

use PizzaService\Lib\Validators\PizzaOrderValidator;
use PizzaService\Lib\Web\PizzaOrder;
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
use PizzaService\Propel\Models\Order;
use PizzaService\Propel\Models\Street;
use PizzaService\Propel\Models\StreetName;
use PizzaService\Propel\Models\StreetNameQuery;
use PizzaService\Propel\Models\StreetQuery;
use PizzaService\Propel\Models\Zip;
use PizzaService\Propel\Models\ZipQuery;

/**
 * Handles the processing of a pizza order.
 */
class PizzaOrderProcessController
{
    /**
     * The pizza order.
     *
     * @var PizzaOrder $pizzaOrder
     */
    private $pizzaOrder;


    /**
     * PizzaOrderProcessController constructor.
     *
     * @throws \PropelException
     */
    public function __construct()
    {
        $this->pizzaOrder = new PizzaOrder();
    }


    /**
     * Checks whether every customer value is valid.
     *
     * @return String|bool Error message or false
     */
    private function validateCustomer()
    {
        // Check whether any value is empty
        if ($_GET["firstName"] == "" ||
            $_GET["lastName"] == "" ||
            $_GET["streetName"] == "" ||
            $_GET["houseNumber"] == "" ||
            $_GET["zip"] == "" ||
            $_GET["city"] == "") {
            return "Fehler: Ein oder mehrere Felder sind leer";
        }
        else return false;
    }

    /**
     * Creates and returns a new customer or returns an existing customer from the database.
     *
     * @return Customer The customer object
     *
     * @throws \PropelException
     */
    private function getCustomer(): Customer
    {
        $customer = $this->findCustomer();

        if (! $customer)
        { // Create a new customer if customer does not exist in the database
            $customer = $this->createCustomer();
        }

        return $customer;
    }

    /**
     * Searches the database for a customer with the data stored in the $_GET variable.
     *
     * @return Customer|bool Customer object or false
     *
     * @throws \PropelException
     */
    private function findCustomer()
    {
        $customerName = $this->findCustomerName();
        if (! $customerName) return false;

        $customerStreet = $this->findCustomerStreet();
        if (! $customerStreet) return false;

        $customerCity = $this->findCustomerCity();
        if (! $customerCity) return false;

        $customerCountry = $this->findCustomerCountry();
        if (! $customerCountry) return false;

        $customerAddress = $this->findCustomerAddress($customerStreet, $customerCity, $customerCountry);
        if (! $customerAddress) return false;

        // Check whether a customer with the customer data already exists in the database
        $customer = CustomerQuery::create()->filterByCustomerName($customerName)
                                           ->filterByCustomerAddress($customerAddress)
                                           ->findOne();

        return $customer;
    }

    /**
     * Creates a new Customer object from the data in the $_GET variable.
     *
     * @throws \PropelException
     */
    private function createCustomer()
    {
        $customerName = $this->findCustomerName();
        if (! $customerName)
        {
            $customerFirstName = FirstNameQuery::create()->findOneByName($_GET["firstName"]);
            if (! $customerFirstName)
            {
                $customerFirstName = new FirstName();
                $customerFirstName->setName($_GET["firstName"]);
            }

            $customerLastName = LastNameQuery::create()->findOneByName($_GET["lastName"]);
            if (! $customerLastName)
            {
                $customerLastName = new LastName();
                $customerLastName->setName($_GET["lastName"]);
            }

            $customerName = new CustomerName();
            $customerName->setFirstName($customerFirstName)
                         ->setLastName($customerLastName);
        }

        $customerStreet = $this->findCustomerStreet();
        if (! $customerStreet)
        {

            $customerStreetName = StreetNameQuery::create()->findOneByName($_GET["streetName"]);
            if (! $customerStreetName)
            {
                $customerStreetName = new StreetName();
                $customerStreetName->setName($_GET["streetName"]);
            }

            $customerHouseNumber = HouseNumberQuery::create()->findOneByNumber($_GET["houseNumber"]);
            if (! $customerHouseNumber)
            {
                $customerHouseNumber = new HouseNumber();
                $customerHouseNumber->setNumber($_GET["houseNumber"]);
            }

            $customerStreet = new Street();
            $customerStreet->setStreetName($customerStreetName)
                           ->setHouseNumber($customerHouseNumber);
        }

        $customerCity = $this->findCustomerCity();
        if (! $customerCity)
        {
            $customerCityName = CityNameQuery::create()->findOneByName($_GET["city"]);
            if (! $customerCityName)
            {
                $customerCityName = new CityName();
                $customerCityName->setName($_GET["city"]);
            }

            $customerCityZip = ZipQuery::create()->findOneByZip($_GET["zip"]);
            if (! $customerCityZip)
            {
                $customerCityZip = new Zip();
                $customerCityZip->setZip($_GET["zip"]);
            }

            $customerCity = new City();
            $customerCity->setCityName($customerCityName)
                         ->setZip($customerCityZip);
        }

        $customerCountry = $this->findCustomerCountry();
        if (! $customerCountry)
        {
            $customerCountry = new Country();
            $customerCountry->setName("Deutschland");
        }

        $customerAddress = $this->findCustomerAddress($customerStreet, $customerCity, $customerCountry);
        if (! $customerAddress)
        {
            $customerAddress = new CustomerAddress();
            $customerAddress->setStreet($customerStreet)
                            ->setCity($customerCity)
                            ->setCountry($customerCountry);
        }

        $customer = new Customer();
        $customer->setCustomerName($customerName)
                 ->setCustomerAddress($customerAddress);

        return $customer;
    }

    /**
     * Finds the customer name from the data in the $_GET variable.
     *
     * @return CustomerName The CustomerName object
     */

    private function findCustomerName()
    {
        $customerName = CustomerNameQuery::create()->useFirstNameQuery()
                                                        ->filterByName($_GET["firstName"])
                                                    ->endUse()
                                                    ->useLastNameQuery()
                                                        ->filterByName($_GET["lastName"])
                                                    ->endUse()
                                                    ->findOne();

        return $customerName;
    }

    /**
     * Finds the customer street from the data in the $_GET variable.
     *
     * @return Street The Street object
     */
    private function findCustomerStreet()
    {
        $customerStreet = StreetQuery::create()->useStreetNameQuery()
                                                   ->filterByName($_GET["streetName"])
                                               ->endUse()
                                               ->useHouseNumberQuery()
                                                   ->filterByNumber($_GET["houseNumber"])
                                               ->endUse()
                                               ->findOne();

        return $customerStreet;
    }

    /**
     * Finds the customer city from the data in the $_GET variable.
     *
     * @return City The City object
     */
    private function findCustomerCity()
    {
        $customerCity = CityQuery::create()->useCityNameQuery()
                                               ->filterbyName($_GET["city"])
                                           ->endUse()
                                           ->useZipQuery()
                                               ->filterByZip($_GET["zip"])
                                           ->endUse()
                                           ->findOne();

        return $customerCity;
    }

    /**
     * Finds the customer country from the data in the $_GET variable.
     *
     * @return Country The Country object
     */
    private function findCustomerCountry()
    {
        $customerCountry = CountryQuery::create()->findOneByName("Deutschland");

        return $customerCountry;
    }

    /**
     * Finds the customer address.
     *
     * @param Street $_customerStreet The customer street
     * @param City $_customerCity The customer city
     * @param Country $_customerCountry The customer country
     *
     * @return CustomerAddress The customer address object
     *
     * @throws \PropelException
     */
    private function findCustomerAddress(Street $_customerStreet, City $_customerCity, Country $_customerCountry)
    {
        $customerAddress = CustomerAddressQuery::create()->filterByStreet($_customerStreet)
                                                         ->filterByCity($_customerCity)
                                                         ->filterByCountry($_customerCountry)
                                                         ->findOne();

        return $customerAddress;
    }

    /**
     * Checks whether every order value is valid.
     *
     * @return String|bool Error message or false
     *
     * @throws \PropelException
     */
    private function validateOrder()
    {
        $pizzaOrderValidator = new PizzaOrderValidator();

        $error = $pizzaOrderValidator->validatePizzaOrder($this->pizzaOrder);
        if ($error) return $error;
        return false;
    }

    /**
     * Creates and returns a new Order object without saving it to the database.
     *
     * @param Customer $_customer The customer object
     *
     * @return Order The order object
     *
     * @throws \Exception
     * @throws \PropelException
     */
    private function getOrder(Customer $_customer): Order
    {
        // Create a new order
        $order = new Order();
        $order->setCustomer($_customer);

        // Add pizzas to the order
        foreach ($this->pizzaOrder->getOrder() as $orderPizza)
        {
            $order->addOrderPizza($orderPizza);
        }

        return $order;
    }

    /**
     * Adds the order to the database when the customer and pizza data is valid.
     *
     * @return String Error message or empty string
     *
     * @throws \Exception
     * @throws \PropelException
     */
    public function addOrder(): String
    {
        $error = $this->validateCustomer();
        if ($error) return $error;

        $error = $this->validateOrder();
        if ($error) return $error;

        $this->getOrder($this->getCustomer())->save();
        $this->pizzaOrder->resetOrder();

        return "";
    }
}
