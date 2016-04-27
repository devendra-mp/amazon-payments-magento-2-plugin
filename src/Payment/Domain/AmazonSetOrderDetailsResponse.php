<?php

namespace Amazon\Payment\Domain;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use PayWithAmazon\ResponseInterface;

class AmazonSetOrderDetailsResponse
{
    /**
     * @var AmazonConstraint[]
     */
    protected $constraints;
    
    public function __construct(ResponseInterface $reponse)
    {
        $data = $reponse->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data['SetOrderReferenceDetailsResult']['OrderReferenceDetails'];

        $this->constraints = [];

        if (isset($details['Constraints'])) {
            foreach($details['Constraints'] as $constraint) {
                $this->constraints[] = new AmazonConstraint($constraint['ConstraintID'], $constraint['Description']);
            }
        }
    }

    public function getConstraints()
    {
        return $this->constraints;
    }
}