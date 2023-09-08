<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $from = $this->createFormBuilder()
                    ->add('attachment', FileType::class, [
        'constraints' => [
          new File([
            'maxSize' => '1M',
            'maxSizeMessage' => 'Le fichier {{ name }} fait {{ size }} {{ suffix }} et la limite est {{ limit }} {{ suffix }}.',
            'mimeTypes' => [
              'image/png'
            ],
            'mimeTypesMessage' => 'Le type du fichier ({{ type }}) est invalide. Les types requis sont {{ types }}.',
          ])
        ]
      ])

                     ->getForm();
        $from->handleRequest($request);
        if($from->isSubmitted() && $from->isValid()){
            /** @var UploadedFile */
            $attachments = $from->get('attachement')->getData();
            foreach ( $attachments as $attachment){
                $extension = $attachment->guessExtension() ?? 'bin';
                $name = pathinfo($attachment->getClientOriginalName(),PATHINFO_FILENAME).'-'.bin2hex(random_bytes(10));
                $attachment->move('images',$name.'.'.$extension);
            }


        }
        return $this->render('home/index.html.twig', [
            'form'=> $from->createView(),
        ]);
    }
}
