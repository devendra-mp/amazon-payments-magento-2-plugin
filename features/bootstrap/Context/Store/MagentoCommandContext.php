<?php

namespace Context\Store;

use Behat\Behat\Tester\Exception\PendingException;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\CommandExecutor;

class MagentoCommandContext extends PageObjectContext implements Context, SnippetAcceptingContext
{
    /**
     * @var CommandExecutor
     */
    private $commandExecutor;

    /**
     * Init context
     */
    public function __construct()
    {
        $this->commandExecutor = new CommandExecutor();
    }

    /**
     * @When the order file generator command is ran
     */
    public function theCommandResponsibleToGenerateWarehouseFilesRuns()
    {
        $this->commandExecutor->runCommand(CommandExecutor::COMMAND_GENERATE_ORDER_FILES);
    }

    /**
     * @When the order status file download command is ran
     */
    public function theOrderStatusFileDownloadCommandIsRan()
    {
        $this->commandExecutor->runCommand(CommandExecutor::COMMAND_DOWNLOAD_ORDER_STATUS_UPDATES);
    }

    /**
     * @When the order status update command is ran
     */
    public function theOrderStatusUpdateCommandIsRan()
    {
        $this->commandExecutor->runCommand(CommandExecutor::COMMAND_UPDATE_ORDER_STATUSES);
    }

    /**
     * @When the order upload command is ran
     */
    public function theOrderUploadCommandIsRan()
    {
        $this->commandExecutor->runCommand(CommandExecutor::COMMAND_UPLOAD_ORDER_FILES);
    }

    /**
     * @When the product stock update file download command is ran
     */
    public function theProductStockUpdateFileDownloadCommandIsRan()
    {
        $this->commandExecutor->runCommand(CommandExecutor::COMMAND_DOWNLOAD_PRODUCT_STOCK_UDATES);
    }

    /**
     * @When the product stock update command is ran
     */
    public function theProductStockUpdateCommandIsRan()
    {
        $this->commandExecutor->runCommand(CommandExecutor::COMMAND_UPDATE_PRODUCT_STOCKS);
    }
}
