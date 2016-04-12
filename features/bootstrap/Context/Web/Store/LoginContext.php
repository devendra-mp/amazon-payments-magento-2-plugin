<?php

namespace Context\Web\Store;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Customer as CustomerFixture;
use Page\Store\Authorize;
use Page\Store\Login;

class LoginContext implements SnippetAcceptingContext
{
    /**
     * @var Authorize
     */
    protected $authorizePage;

    /**
     * @var Login
     */
    protected $loginPage;

    protected $accessTokens = [
        'existingamazon@example.com' => 'Atza|IpFFMdZjI2qp1UQ3dXqX3uTsHJrNvxzENQI1SSNfKyLU6LQR2S3YOjAADBHYFBqqAy07FLD2IL6OAbD0YwUnnsbCN1gzubYfsyEudkyqvXFJREMNTijuhMUbjjl5Mnot2O42KsQnsGyNd_w7QDI4CYByg-2amX1Q6RBpgdGYRMzsGTOVCkT-vCWVNnSB5risO9ck0D9lBFfzG160WFfwlih8sSqsJr_RmEWyMad0bOBwPqU3kvu45X6OSjTCnbuD4firhr2aDXC3s2YJT0Bd1CMvb5GKBKQkH70BIAnNiAvSDjQUJM4Lb5RO9tVPqkZ2-akbzop5zFDXsv5hqiqefAIB0ZN5AuZDP6f-Tc0bR_AiQdzq-5Yyoj1qN26_qtXeMZMsjaYcZ9H15H7qH7Y6-0HXU0xbk3Z1sLH73odg3aqqNlmj5drLxcNXq14gLdw7eyse5gfEmZaIomo1VL9IR7J1tN1Z67SELr1vbiB4TeRpYJ-ie0PLRObsKo-tDfRZ6dV7u1MfigCUFZ0fdPq4t6y2WGQoRPq1zpCx3iiEGhTat78LQ79ecJLT0E1LiG0Zj9R3bnlSrHVVv43OrixNhjicV6CcCQ4zq-kIF31BPyTCoQXM'
    ];


    public function __construct(Login $loginPage, Authorize $authorizePage)
    {
        $this->loginPage       = $loginPage;
        $this->authorizePage   = $authorizePage;
        $this->customerFixture = new CustomerFixture;
    }

    /**
     * @Given :email is logged in
     */
    public function isLoggedIn($email)
    {
        $this->loginPage->open();
        $this->loginPage->loginCustomer($email, $this->customerFixture->getDefaultPassword());
    }

    /**
     * @When I have logged in with amazon as :email
     */
    public function iHaveLoggedInWithAmazonAs($email)
    {
        $accessToken = $this->accessTokens[$email];
        $this->authorizePage->open([
            'access_token' => $this->accessTokens[$email]
        ]);
    }
}