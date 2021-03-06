<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Image;
use App\Form\Admin\ImageType;
use App\Repository\Admin\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/image")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/", name="admin_image_index", methods="GET")
     */
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('admin/image/index.html.twig', ['images' => $imageRepository->findAll()]);
    }

    /**
     * @Route("/{id}/new", name="admin_image_new", methods="GET|POST")
     */
    public function new(Request $request,$id,ImageRepository $imageRepository): Response
    {
        $imagelist=$imageRepository->findBy( ['product_id' => $id ] );
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
            echo $file = $request->files->get('imagename');
            echo $id;
        if ($request->files->get('imagename'))
        {

            $file = $request->files->get('imagename');
           $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
           
             // Move the file to the directory where brochures are stored
             try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $image->setImage($fileName);
            $image->setProductid($id);


            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            return $this->redirectToRoute('admin_image_new',array('id'=>$id));
        }

      /*  if ($form->isSubmitted()) {
            
            $file = $request->files->get('imagename');
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
           
             // Move the file to the directory where brochures are stored
             try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $image->setImage($fileName);
            $image->setProductid($id);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('admin_image_new',array('id'=>$id));
       }*/

        return $this->render('admin/image/new.html.twig', [
            'image' => $image,
            'imagelist' => $imagelist,
            'id' => $id,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_image_show", methods="GET")
     */
    public function show(Image $image): Response
    {
        return $this->render('admin/image/show.html.twig', ['image' => $image]);
    }

    /**
     * @Route("/{id}/edit", name="admin_image_edit", methods="GET|POST")
     */
    public function edit(Request $request, Image $image): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_image_index', ['id' => $image->getId()]);
        }

        return $this->render('admin/image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }



/**
     * @Route("/{id}/iedit", name="admin_image_iedit", methods="GET|POST")
     */
 /*   public function iedit(Request $request,$id , Image $image): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_image_edit', ['id' => $image->getId()]);
        }

        return $this->render('admin/image/_form.html.twig', [
            'image' => $image,
            'id' => $id,
            'form' => $form->createView(),
        ]);
    } */






    /**
     * @Route("/{id}/{pid}", name="admin_image_delete", methods="GET")
     */
    public function delete(Request $request, Image $image,$pid): Response
    {


            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();


        return $this->redirectToRoute('admin_image_new',array('id'=>$pid));
    }


    
 /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }


}
