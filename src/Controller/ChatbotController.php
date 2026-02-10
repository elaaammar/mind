<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

final class ChatbotController extends AbstractController
{
    #[Route('/api/chatbot', name: 'api_chatbot', methods: ['POST'])]
    public function chat(
        Request $request, 
        HttpClientInterface $httpClient,
        LoggerInterface $logger
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        $message = trim($data['message'] ?? '');

        if ($message === '') {
            return new JsonResponse(['answer' => "Merci de saisir une question."], 400);
        }

        $apiKey = $_ENV['GEMINI_API_KEY'] ?? null;
        if (!$apiKey) {
            return new JsonResponse([
                'answer' => "Le chatbot n'est pas encore configuré (clé API manquante).",
            ], 500);
        }

        $systemInstruction = "Tu es un assistant pour une plateforme de gestion d'audit et de réclamations. " .
            "Tu réponds UNIQUEMENT à des questions liées : aux audits, aux réclamations, au traitement des plaintes, " .
            "aux SLA, à la qualité de service, ou à l'utilisation de ce type de module. " .
            "Si la question sort de ce cadre (vie privée, politique, programmation générale, etc.), " .
            "réponds simplement : \"Je peux seulement répondre aux questions liées aux audits et aux réclamations.\"";

        try {
            $response = $httpClient->request(
                'POST',
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'query' => [
                        'key' => $apiKey,
                    ],
                    'json' => [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => $systemInstruction . "\n\nQuestion de l'utilisateur : " . $message
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'timeout' => 60, // Increased to 60 seconds
                    'max_duration' => 60, // Maximum time for the entire request
                ]
            );

            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 429) {
                return new JsonResponse([
                    'answer' => "Trop de requêtes. Veuillez patienter quelques secondes avant de réessayer.",
                ], 200);
            }
            
            if ($statusCode !== 200) {
                $logger->error('Gemini API returned non-200 status', [
                    'status' => $statusCode,
                ]);
                return new JsonResponse([
                    'answer' => "Le service d'IA a renvoyé une erreur (code: {$statusCode}).",
                ], 200);
            }

            $payload = $response->toArray(false);
            
            $logger->info('Gemini API Response', ['payload' => $payload]);

            if (isset($payload['error'])) {
                $errorMessage = $payload['error']['message'] ?? 'Unknown error';
                $logger->error('Gemini API error', ['error' => $payload['error']]);
                return new JsonResponse([
                    'answer' => "Erreur du service IA : " . $errorMessage,
                ], 200);
            }

            $answer = "Désolé, je n'ai pas pu générer de réponse.";

            if (isset($payload['candidates'][0]['content']['parts'])) {
                $parts = $payload['candidates'][0]['content']['parts'];
                
                if (is_array($parts)) {
                    $texts = [];
                    foreach ($parts as $part) {
                        if (isset($part['text']) && is_string($part['text'])) {
                            $texts[] = trim($part['text']);
                        }
                    }
                    
                    if (!empty($texts)) {
                        $answer = implode("\n\n", $texts);
                    }
                }
            }

            return new JsonResponse(['answer' => $answer]);
            
        } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
            // This catches timeout errors
            $logger->error('Transport error (timeout or network)', [
                'message' => $e->getMessage(),
            ]);
            return new JsonResponse([
                'answer' => "Le service met trop de temps à répondre. Veuillez réessayer avec une question plus courte.",
            ], 200);
            
        } catch (\Throwable $e) {
            $logger->error('Unexpected error in chatbot', [
                'message' => $e->getMessage(),
            ]);
            return new JsonResponse([
                'answer' => "Une erreur est survenue. Veuillez réessayer.",
            ], 200);
        }
    }
}