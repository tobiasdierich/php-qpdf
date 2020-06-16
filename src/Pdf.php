<?php

namespace tobiasdierich\qpdf;

use mikehaertl\shellcommand\Command;
use mikehaertl\tmp\File;

class Pdf
{
    const TMP_PREFIX = 'tmp_php_qpdf_';

    /**
     * @var \mikehaertl\shellcommand\Command
     */
    protected $command;

    /**
     * @var string
     */
    protected $originalFile;

    /**
     * @var \mikehaertl\tmp\File
     */
    protected $outputFile;

    /**
     * @var string
     */
    protected $error;

    /**
     * PDF constructor.
     *
     * @param string $pdf
     */
    public function __construct($pdf)
    {
        $this->originalFile = $pdf;

        $this->getCommand()
            ->addArg($pdf, null, true);
    }

    /**
     * @param string $file
     *
     * @return \tobiasdierich\qpdf\Pdf
     */
    public function background($file)
    {
        $this->getCommand()
            ->addArg('--underlay', $file, true)
            ->addArg('--repeat=', 1)
            ->addArg('--');

        return $this;
    }

    /**
     * @return \tobiasdierich\qpdf\Pdf|bool
     */
    public function execute()
    {
        $command = $this->getCommand();
        if ($command->getExecuted()) {
            return new static($this->getOutputFile()->getFileName());
        }

        $outputFilename = $this->getOutputFile()->getFileName();

        $command->addArg($outputFilename, null, true);

        if (!$command->execute()) {
            $this->error = $command->getError();

            if ($outputFilename && !(file_exists($outputFilename) && filesize($outputFilename) !== 0)) {
                return false;
            }
        }

        return new static($outputFilename);
    }

    /**
     * @return bool|false|string
     */
    public function toString()
    {
        return file_get_contents($this->originalFile);
    }

    /**
     * @return \mikehaertl\shellcommand\Command
     */
    public function getCommand()
    {
        if ($this->command === null) {
            $this->command = new Command([
                'command' => 'qpdf',
            ]);
        }

        return $this->command;
    }

    /**
     * @return \mikehaertl\tmp\File
     */
    public function getOutputFile()
    {
        if ($this->outputFile === null) {
            $this->outputFile = new File('', '.pdf', self::TMP_PREFIX);
        }

        return $this->outputFile;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $pdf
     *
     * @return \tobiasdierich\qpdf\Pdf
     */
    public static function create($pdf)
    {
        return new static($pdf);
    }
}
