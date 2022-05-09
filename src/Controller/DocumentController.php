<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use App\Service\QrCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/document")
 */
class DocumentController extends AbstractController
{
    /**
     * @Route("/", name="document_list")
     */
    public function index(DocumentRepository $repository): Response
    {
        return $this->render('document/index.html.twig', [
            'documents' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/add", name="document_add")
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($document);
            $manager->flush();
            $this->addFlash('success', 'Document saved .');

            return $this->redirectToRoute('document_list');
        }

        return $this->render('document/add.html.twig', [
            'form' => $form->createView(),
            'title' => 'New Document',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="document_edit")
     */
    public function edit(Request $request, Document $document, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash('success', 'Document updated .');

            return $this->redirectToRoute('document_list');
        }

        return $this->render('document/add.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit document',
        ]);
    }

    /**
     * @Route("/remove/{id}", name="document_remove")
     */
    public function remove(Document $document, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($document)
            ->add('confirm', SubmitType::class, ['attr' => ['class' => 'btn btn-danger']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $document->setDeleted(true);
            $entityManager->flush();
            $this->addFlash('success', 'Document deleted !.');

            return $this->redirectToRoute('document_list');
        }

        return $this->render('document/delete.html.twig', [
            'form' => $form->createView(),
            'document' => $document,
        ]);
    }

    /**
     * @Route("/qrcode/{id}", name="document_qrcode")
     */
    public function downloadQrCode(QrCodeGenerator $codeGenerator, Document $document): BinaryFileResponse
    {
        $result = $codeGenerator->generateQr($document);

        return $this->file('qr/'.$document->getId().'.png');
    }

    /**
     * @Route("/pdf/{id}", name="document_pdf")
     */
    public function downloadPdf(Document $document, Pdf $pdf): PdfResponse
    {
        return new PdfResponse($pdf->getOutputFromHtml($document->getContent()), $document->getId().'.pdf');
    }

    private function saveFile(Document $document): string
    {
        $path = $this->getParameter('kernel.project_dir').'/public/pdf/'.$document->getId().'.pdf';
        file_put_contents($path, $document->getContent());

        return $path;
    }
}
