<?php
class Chats extends Trongate {

    private function buildSystemPrompt(): string {
        $sessions = '';
        try {
            $db = new PDO(
                'mysql:host=' . HOST . ';dbname=' . DATABASE . ';charset=utf8mb4',
                USER, PASSWORD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT]
            );
            $stmt = $db->query('SELECT pamaina, start, end, price, status FROM camps_pamainos ORDER BY pamaina');
            $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_OBJ) : [];
            foreach ($rows as $r) {
                $label = match($r->status) {
                    'full'  => 'PILNA',
                    'ended' => 'BAIGĖSI',
                    default => 'laisva',
                };
                $sessions .= "- {$r->pamaina}. pamaina: {$r->start} – {$r->end}, {$r->price}€ [{$label}]\n";
            }
            $db = null;
        } catch (Exception $e) {
            $sessions = '(pamainos šiuo metu nepasiekiamos)';
        }

        return <<<PROMPT
Tu esi Molas Surf Club virtualus asistentas. Tu padedi lankytojams sužinoti apie banglenčių pamokas, įrangos nuomą, vaikų vasaros stovyklą ir renginius Klaipėdoje, Melnragėje.

SVARBI INFORMACIJA:

PAMOKOS:
- Pamokų paketas: 150€ (dvi 1h pamokos)
- Privati pamoka: 85€ (1.5h trukmė)
- Grupinė pamoka: 40€
- Pamoka dviem: 120€
- Individuali Plus: 100€ (1.5h pamoka + 30 min asmeninė konsultacija)
- Komandos formavimas: nuo 200€ (6-12 žmonių)
- Dovanų kuponai: nuo 40€ (el. paštu arba popieriniai kuponai)

ĮRANGOS NUOMA:
- Banglentė: nuo 15€/2h, 40€/diena
- Irklentė (SUP): 15€/1h, 40€/diena
- Hidrokostiumas: 10€/2h, 20€/diena
- Riedlentė: 15€/2h, 30€/diena
- Skim boardas: 10€/2h, 20€/diena
- Puslentė: 10€/2h, 25€/diena

STOVYKLA:
- Nuo birželio vidurio iki rugpjūčio pabaigos
- 8-10 moksleivių grupės
- 5 dienos, 9:00–17:00
- Registracija: forma + 100€ avansas (negrąžinamas)
- Įskaičiuota: pamokos, įrangos nuoma, maitinimas ir renginiai

STOVYKLOS PAMAINOS 2026:
{$sessions}
(Pamainos su žyme PILNA – vietos užimtos. Laisvose pamainose galima registruotis.)

KONTAKTAI:
- VšĮ Banglentė, Vėtros g. 8, Klaipėda
- Tel: +370 686 02356
- El. paštas: info@surfclub.lt
- www.surfclub.lt

TAISYKLĖS:
- Atsakyk draugiškai ir trumpai (1–3 sakiniai)
- Atsakyk ta kalba, kuria klausia
- Jei nežinai – siūlyk susisiekti tiesiogiai
- Būk entuziastingas dėl banglenčių sporto!
PROMPT;
    }

    public function proxy(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            die();
        }

        // Session-based rate limit: max 30 messages per session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['chat_count'] = ($_SESSION['chat_count'] ?? 0) + 1;
        if ($_SESSION['chat_count'] > 30) {
            http_response_code(429);
            echo json_encode(['error' => 'Per daug užklausų. Susisiekite tiesiogiai: +370 686 02356']);
            die();
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['messages']) || !is_array($input['messages']) || empty($input['messages'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            die();
        }

        // Keep only the last 20 turns to cap token usage
        $messages = array_values(array_slice($input['messages'], -20));

        $payload = json_encode([
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 600,
            'system'     => [
                [
                    'type'          => 'text',
                    'text'          => $this->buildSystemPrompt(),
                    'cache_control' => ['type' => 'ephemeral'],
                ],
            ],
            'messages' => $messages,
        ]);

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: '        . constant('ANTHROPIC_API_KEY'),
                'anthropic-version: 2023-06-01',
                'anthropic-beta: prompt-caching-2024-07-31',
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT    => 60,
        ]);

        $response   = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            http_response_code(502);
            echo json_encode(['error' => 'Upstream request failed', 'detail' => $curl_error]);
            die();
        }

        http_response_code($http_code);
        echo $response;
        die();
    }
}
