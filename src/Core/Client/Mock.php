<?php

namespace Amazon\Core\Client;

use PayWithAmazon\ClientInterface;

class Mock implements ClientInterface
{
    /**
     * @var array
     */
    protected $config;

    public function __construct($config = null)
    {
        $this->config = $config;
    }

    public function getUserInfo($access_token)
    {
        $existingUserToken = 'Atza|IpFFMdZjI2qp1UQ3dXqX3uTsHJrNvxzENQI1SSNfKyLU6LQR2S3YOjAADBHYFBqqAy07FLD2IL6OAbD0YwUnnsbCN1gzubYfsyEudkyqvXFJREMNTijuhMUbjjl5Mnot2O42KsQnsGyNd_w7QDI4CYByg-2amX1Q6RBpgdGYRMzsGTOVCkT-vCWVNnSB5risO9ck0D9lBFfzG160WFfwlih8sSqsJr_RmEWyMad0bOBwPqU3kvu45X6OSjTCnbuD4firhr2aDXC3s2YJT0Bd1CMvb5GKBKQkH70BIAnNiAvSDjQUJM4Lb5RO9tVPqkZ2-akbzop5zFDXsv5hqiqefAIB0ZN5AuZDP6f-Tc0bR_AiQdzq-5Yyoj1qN26_qtXeMZMsjaYcZ9H15H7qH7Y6-0HXU0xbk3Z1sLH73odg3aqqNlmj5drLxcNXq14gLdw7eyse5gfEmZaIomo1VL9IR7J1tN1Z67SELr1vbiB4TeRpYJ-ie0PLRObsKo-tDfRZ6dV7u1MfigCUFZ0fdPq4t6y2WGQoRPq1zpCx3iiEGhTat78LQ79ecJLT0E1LiG0Zj9R3bnlSrHVVv43OrixNhjicV6CcCQ4zq-kIF31BPyTCoQXM';

        $responses = [
            $existingUserToken => [
                'user_id' => 'amzn1.account.VQ3AF2JFB5H7K5JZRDIZHUH7IVNH',
                'name'    => 'John Doe',
                'email'   => 'existingamazon@example.com'
            ]
        ];

        return $responses[$access_token];
    }

    public function setSandbox($value)
    {
    }

    public function setClientId($value)
    {
    }

    public function setProxy($proxy)
    {
    }

    public function setMwsServiceUrl($url)
    {
    }

    public function __get($name)
    {
    }

    public function getParameters()
    {
    }

    public function getOrderReferenceDetails($requestParameters = array())
    {
    }

    public function setOrderReferenceDetails($requestParameters = array())
    {
    }

    public function confirmOrderReference($requestParameters = array())
    {
    }

    public function cancelOrderReference($requestParameters = array())
    {
    }

    public function closeOrderReference($requestParameters = array())
    {
    }

    public function closeAuthorization($requestParameters = array())
    {
    }

    public function authorize($requestParameters = array())
    {
    }

    public function getAuthorizationDetails($requestParameters = array())
    {
    }

    public function capture($requestParameters = array())
    {
    }

    public function getCaptureDetails($requestParameters = array())
    {
    }

    public function refund($requestParameters = array())
    {
    }

    public function getRefundDetails($requestParameters = array())
    {
    }

    public function getServiceStatus($requestParameters = array())
    {
    }

    public function createOrderReferenceForId($requestParameters = array())
    {
    }

    public function getBillingAgreementDetails($requestParameters = array())
    {
    }

    public function setBillingAgreementDetails($requestParameters = array())
    {
    }

    public function confirmBillingAgreement($requestParameters = array())
    {
    }

    public function validateBillingAgreement($requestParameters = array())
    {
    }

    public function authorizeOnBillingAgreement($requestParameters = array())
    {
    }

    public function closeBillingAgreement($requestParameters = array())
    {
    }

    public function charge($requestParameters = array())
    {
    }

    public function getProviderCreditDetails($requestParameters = array())
    {
    }

    public function getProviderCreditReversalDetails($requestParameters = array())
    {
    }

    public function reverseProviderCredit($requestParameters = array())
    {
    }
}