<?php

namespace App\Controller\Webhook;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

header('Content-Type: application/json');

class WebhookController extends AbstractController
{

    /**
     *@Route("/webhook", name="webhook")
     */
    public function webhook()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['error' => 'Invalid request.']);
            exit;
        }

        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY_TEST']);

        // with signature verification
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        //local secret
        $endpoint_secret = $_ENV['STRIPE_WEBHOOK_SECRET_TEST'];

        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            echo $e;
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            echo $e;
            exit();
        }

        ob_start();
        var_dump($event->type);
        var_dump($event->type);
        var_dump($event->data->object->id);
        error_log(ob_get_clean(), 4);

        echo json_encode(['status' => 'success']);

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.created':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                error_log(
                    sprintf('[%s] PaymentIntent (%s): (%s)', $event->id, $paymentIntent->id, $paymentIntent->status)
                );
                break;
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                // error_log(
                //     sprintf('[%s] PaymentIntent (%s): (%s)', $event->id, $paymentIntent->id, $paymentIntent->status)
                // );
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        http_response_code(200);
    }
}
