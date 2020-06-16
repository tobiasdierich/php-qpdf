<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use tobiasdierich\qpdf\Pdf;

class PdfTest extends TestCase
{
    /**
     * @var \tobiasdierich\qpdf\Pdf
     */
    private $pdf;

    public function testCanSetBackground()
    {
        $document1 = $this->getDocument1();
        $document2 = $this->getDocument2();

        $this->pdf = new Pdf($document1);
        $this->assertInstanceOf('tobiasdierich\qpdf\Pdf', $this->pdf->background($document2));

        $outputPdf = $this->pdf->execute();

        $this->assertTrue($this->pdf->getCommand()->getExecuted());
        $this->assertFileExists($outputPdf->getOutputFile()->getFileName());


        $tmpFile = $this->pdf->getOutputFile()->getFileName();
        $this->assertEquals("qpdf '$document1' '--underlay' '$document2' '--repeat'='1' '--' '$tmpFile'", (string) $this->pdf->getCommand());
    }

    protected function getDocument1()
    {
        return __DIR__ . '/files/document1.pdf';
    }

    protected function getDocument2()
    {
        return __DIR__ . '/files/document2.pdf';
    }
}
