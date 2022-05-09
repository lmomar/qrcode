<?php

namespace App\Service;

use App\Entity\Document;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class QrCodeGenerator
{
    private $package;

    public function __construct()
    {
        $this->package = new Package(new EmptyVersionStrategy());
    }

    public function generateQr(Document $document)
    {
        $qr = Builder::create()
            ->writer(new PngWriter())
            ->data($document->getContent())
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            // ->logoPath($this->package->getUrl('assets/logo.png'))
            ->labelText($document->getTitle())
            ->labelAlignment(new LabelAlignmentCenter())
            ->build();
        $qr->saveToFile($this->package->getUrl('qr/'.$document->getId().'.png'));

        return $qr;
    }
}
