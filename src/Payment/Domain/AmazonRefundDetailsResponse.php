<?php
/**
 * Copyright 2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
namespace Amazon\Payment\Domain;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use Amazon\Payment\Domain\Response\AmazonResponseInterface;
use PayWithAmazon\ResponseInterface;
use Amazon\Payment\Domain\Response\Part\RefundDetailsPart;
use Amazon\Payment\Domain\Response\Part\RefundDetailsPartFactory;

class AmazonRefundDetailsResponse implements AmazonResponseInterface
{
    /**
     * @var RefundDetailsPart
     */
    protected $refundDetailsPart;

    /**
     * @param ResponseInterface $response
     * @param RefundDetailsPartFactory $refundDetailsPartFactory
     * @throws AmazonServiceUnavailableException
     */
    public function __construct(
        ResponseInterface $response,
        RefundDetailsPartFactory $refundDetailsPartFactory
    ) {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $this->refundDetailsPart = $refundDetailsPartFactory->create([
            'rawRefundDetails' => $data['GetRefundDetailsResult']['RefundDetails'],
        ]);
    }

    /**
     * @return RefundDetailsPart
     */
    public function getRefundDetails()
    {
        return $this->refundDetailsPart;
    }
}
