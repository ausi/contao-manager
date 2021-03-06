<?php

/*
 * This file is part of Contao Manager.
 *
 * Copyright (c) 2016-2017 Contao Association
 *
 * @license LGPL-3.0+
 */

namespace Contao\ManagerApi\Process;

class ContaoApi
{
    /**
     * @var ConsoleProcessFactory
     */
    private $processFactory;

    /**
     * @var string
     */
    private $contaoVersion = false;

    /**
     * Constructor.
     *
     * @param ConsoleProcessFactory $processFactory
     */
    public function __construct(ConsoleProcessFactory $processFactory)
    {
        $this->processFactory = $processFactory;
    }

    /**
     * Gets the Contao version by trying to run the contao:version command.
     *
     * @return null|string
     */
    public function getContaoVersion()
    {
        if (false === $this->contaoVersion) {
            $this->contaoVersion = null;

            $process = $this->processFactory->createContaoConsoleProcess(['contao:version']);
            $process->run();

            if ($process->isSuccessful()) {
                $version = trim($process->getOutput());

                if (preg_match('/^\d+\.\d+\.\d+$/', $version)) {
                    $this->contaoVersion = $version;
                }
            }
        }

        return $this->contaoVersion;
    }
}
