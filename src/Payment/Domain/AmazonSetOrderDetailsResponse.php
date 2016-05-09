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

    public function __construct(ResponseInterface $response, AmazonConstraintFactory $amazonConstraintFactory)
    {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data['SetOrderReferenceDetailsResult']['OrderReferenceDetails'];

        $this->constraints = [];

        if (isset($details['Constraints'])) {
            foreach ($details['Constraints'] as $constraint) {
                $this->constraints[] = $amazonConstraintFactory->create([
                    'id'          => $constraint['ConstraintID'],
                    'description' => $constraint['Description']
                ]);
            }
        }
    }

    public function getConstraints()
    {
        return $this->constraints;
    }
}