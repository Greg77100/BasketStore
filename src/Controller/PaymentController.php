<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/paiement')]
class PaymentController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(PaymentRepository $paymentRepository): Response
    {
        return $this->render('payment/index.html.twig', [
            'payments' => $paymentRepository->findAll(),
        ]);
    }


    #[Route('/{orderId}', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $orderId): Response
    {
        $order = $entityManager->getRepository(Order::class)->find($orderId);
        if (!$order) {
                throw $this->createNotFoundException("Commande non trouvée.");
            }

            $payment = new Payment();
            $payment->setAmount($order->getTotalOrder());
            $payment->setDatePayment(new \DateTimeImmutable());
            $payment->setStatutPayment('validé');

            $form = $this->createForm(PaymentType::class, $payment);
            $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            

            $order->setPayment($payment);

            $entityManager->persist($payment);
            $entityManager->flush();

            $this->cartService->clearCart();

            return $this->redirectToRoute('app_order_confirmation', ['id' => $order->getId()]);
        }

        return $this->render('payment/new.html.twig', [

            'payment' => $payment,
            'form' => $form,
            'order' => $order,
            'orderItems' => $order->getOrderList(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'app_payment_delete', methods: ['POST'])]

    public function delete(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($payment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
    }
}
