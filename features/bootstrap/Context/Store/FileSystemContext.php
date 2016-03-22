<?php

namespace Context\Store;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Fixtures\FileContentGenerator;
use Fixtures\FileHandler;
use Page\Store\CheckoutPage;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

/**
 * @codingStandardsIgnoreFile
 */
class FileSystemContext extends PageObjectContext implements Context, SnippetAcceptingContext
{
    /**
     * @var CheckoutPage
     */
    private $checkoutPage;

    /**
     * @var FileHandler
     */
    private $fileSystem;

    /**
     * @var FileContentGenerator
     */
    private $fileContentProvider;

    /**
     * @param CheckoutPage $checkoutPage
     */
    public function __construct(CheckoutPage $checkoutPage)
    {
        $this->checkoutPage = $checkoutPage;
        $this->fileSystem = new FileHandler();
        $this->fileContentProvider = new FileContentGenerator();
    }

    /**
     * @Given there is a local file :localFilePath which contains :content
     */
    public function thereIsALocalFile($localFilePath, $content)
    {
        $this->fileSystem->createFileInVarFolder($localFilePath, $content);
    }

    /**
     * @Given there is a remote file :remoteFilePath which contains :content
     */
    public function thereIsARemoteFile($remoteFilePath, $content)
    {
        $this->fileSystem->createFileInMockFtpFolder($remoteFilePath, $content);
    }

    /**
     * @Then the local :localFilePath file should exists
     */
    public function theLocalFileShouldExists($localFilePath)
    {
        $this->fileSystem->checkFileExistsInVarFolder($localFilePath);
    }

    /**
     * @Then the :localFilePath file should be downloaded to the :localFolder folder
     */
    public function theFileShouldBeDownloaded($localFilePath, $localFolder)
    {
        $fileName = basename($localFilePath);
        $this->fileSystem->checkFileExistsInVarFolder("{$localFolder}/{$fileName}");
    }

    /**
     * @Then the :localFilePath file should be uploaded to the :remoteFolder ftp folder
     */
    public function theFileShouldBeUploaded($localFilePath, $remoteFolder)
    {
        $fileName = basename($localFilePath);
        $this->fileSystem->checkFileExistsInMockFtpFolder("{$remoteFolder}/{$fileName}");
    }

    /**
     * @Then the remote :fromPath file should be archived to :toPath
     */
    public function theRemoteFileShouldBeArchived($fromPath, $toPath)
    {
        $this->fileSystem->checkFileNotExistsInMockFtpFolder($fromPath);
        $this->fileSystem->checkFileExistsInMockFtpFolder($toPath);
    }

    /**
     * @Then the local :fromPath file should be archived to :toPath
     */
    public function theLocalFileShouldBeArchived($fromPath, $toPath)
    {
        $this->fileSystem->checkFileNotExistsInVarFolder($fromPath);
        $this->fileSystem->checkFileExistsInVarFolder($toPath);
    }

    /**
     * @Then the :localFilePath file should not be downloaded to the :localFolder folder
     */
    public function theFileShouldNotBeDownloaded($localFilePath, $localFolder)
    {
        $fileName = basename($localFilePath);
        $this->fileSystem->checkFileNotExistsInVarFolder("{$localFolder}/{$fileName}");
    }

    /**
     * @Then the :orderFilePath order file should be generated
     */
    public function theOrderFileShouldBeGenerated($orderFilePath)
    {
        $this->theLocalFileShouldExists($this->addOrderNumber($orderFilePath));
    }

    /**
     * @Then the :orderFilePath order file should be uploaded to the :remoteFolder ftp folder
     */
    public function theOrderFileShouldBeUploaded($orderFilePath, $remoteFolder)
    {
        $this->theFileShouldBeUploaded($this->addOrderNumber($orderFilePath), $remoteFolder);
    }

    /**
     * @Then the local :fromPath order file should be archived to :toPath
     */
    public function theLocalOrderFileShouldBeArchived($fromPath, $toPath)
    {
        $this->theLocalFileShouldBeArchived($this->addOrderNumber($fromPath), $this->addOrderNumber($toPath));
    }

    /**
     * @When there is a :fileName order status update file for that order with remote status :orderRemoteStatus in the :remoteFolder ftp folder
     * @When there is a :fileName shipping confirmation file for that order in the :remoteFolder ftp folder
     */
    public function thereIsAnOrderStatusUpdateFileOnTheFtp($fileName, $remoteFolder, $orderRemoteStatus = null)
    {
        $orderNumber = $this->checkoutPage->getLastOrderNumber();

        $content = $this->fileContentProvider->createEuOrderStatusUpdateFileContent($orderNumber, $orderRemoteStatus);
        if (is_null($orderRemoteStatus)) {
            $content = $this->fileContentProvider->createUsOrderStatusUpdateFileContent($orderNumber);
        }

        $this->thereIsARemoteFile("{$remoteFolder}/{$fileName}", $content);
    }

    /**
     * @Given there is a :fileName product stock update file for the product with sku :sku with stock :qty in the :remoteFolder ftp folder
     */
    public function thereIsAProductStockUpdateFileOnTheFtp($fileName, $sku, $qty, $remoteFolder)
    {
        $content = $this->fileContentProvider->createProductStockUpdateFileContent($sku, $qty);

        $this->thereIsARemoteFile("{$remoteFolder}/{$fileName}", $content);
    }

    private function addOrderNumber($path)
    {
        $orderNumber = $this->checkoutPage->getLastOrderNumber();
        return str_replace('%ordernumber%', $orderNumber, $path);
    }
}
