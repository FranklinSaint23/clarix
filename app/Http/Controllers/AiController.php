<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    private function groq(array $messages, int $maxTokens = 600): string
    {
        $response = Http::withToken(config('services.groq.key'))
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => config('services.groq.model', 'llama-3.1-8b-instant'),
                'messages'    => $messages,
                'max_tokens'  => $maxTokens,
                'temperature' => 0.4,
            ]);

        if ($response->failed()) {
            abort(502, 'Erreur IA: ' . $response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

    public function summarize(Request $request)
    {
        $content = $request->validate(['content' => 'nullable|string|max:8000'])['content'] ?? '';

        $result = $this->groq([
            ['role' => 'system', 'content' => 'Tu es un assistant pédagogique expert. Génère un résumé concis du contenu de leçon en 4-6 points clés, chaque point commençant par "✓". Réponds uniquement en français, sans introduction.'],
            ['role' => 'user', 'content' => "Résume cette leçon :\n\n{$content}"],
        ]);

        return response()->json(['result' => $result]);
    }

    public function explain(Request $request)
    {
        $content = $request->validate(['content' => 'nullable|string|max:8000'])['content'] ?? '';

        $result = $this->groq([
            ['role' => 'system', 'content' => 'Tu es un tuteur pédagogique qui explique les concepts difficiles avec des mots simples, des analogies du quotidien et des exemples concrets. Réponds en français, de façon claire et accessible même pour un débutant complet.'],
            ['role' => 'user', 'content' => "Explique simplement ce contenu de leçon :\n\n{$content}"],
        ]);

        return response()->json(['result' => $result]);
    }

    public function questions(Request $request)
    {
        $content = $request->validate(['content' => 'nullable|string|max:8000'])['content'] ?? '';

        $result = $this->groq([
            ['role' => 'system', 'content' => 'Tu es un formateur qui crée des questions de révision. Génère exactement 5 questions à choix ou de réflexion basées sur la leçon, avec leur réponse. Format : "Question X : [question]\nRéponse : [réponse]". Réponds en français uniquement.'],
            ['role' => 'user', 'content' => "Génère 5 questions de révision pour cette leçon :\n\n{$content}"],
        ], 800);

        return response()->json(['result' => $result]);
    }

    public function generateQuiz(Request $request, \App\Models\Quiz $quiz)
    {
        $data = $request->validate([
            'content' => 'nullable|string|max:8000',
            'count'   => 'integer|min:3|max:15',
        ]);

        $content = $data['content'] ?? '';
        $count   = $data['count']   ?? 5;

        $context = $content
            ? "Contenu de la leçon :\n{$content}"
            : "Titre du quiz : {$quiz->title}";

        $prompt = <<<PROMPT
Tu es un expert en pédagogie. Génère exactement {$count} questions de quiz QCM en français, basées sur ce contexte :

{$context}

Retourne UNIQUEMENT un tableau JSON valide (sans markdown, sans texte autour), au format exact :
[
  {
    "question_text": "Texte de la question ?",
    "type": "single",
    "points": 1,
    "explanation": "Explication courte de la bonne réponse.",
    "answers": [
      {"text": "Bonne réponse", "is_correct": true},
      {"text": "Mauvaise réponse 1", "is_correct": false},
      {"text": "Mauvaise réponse 2", "is_correct": false},
      {"text": "Mauvaise réponse 3", "is_correct": false}
    ]
  }
]
PROMPT;

        $raw = $this->groq([
            ['role' => 'system', 'content' => 'Tu génères uniquement du JSON valide, sans aucun texte supplémentaire, sans bloc markdown.'],
            ['role' => 'user',   'content' => $prompt],
        ], 3000);

        // Nettoyer les éventuels blocs ```json ... ```
        $raw = preg_replace('/^```(?:json)?\s*/m', '', $raw);
        $raw = preg_replace('/```\s*$/m', '', $raw);
        $raw = trim($raw);

        $questions = json_decode($raw, true);

        if (!is_array($questions) || empty($questions)) {
            return response()->json(['error' => 'L\'IA n\'a pas retourné un JSON valide. Réessayez.'], 422);
        }

        $created = 0;
        $order   = $quiz->questions()->max('order') ?? 0;

        foreach ($questions as $q) {
            if (empty($q['question_text']) || empty($q['answers'])) continue;

            $question = $quiz->questions()->create([
                'question_text' => $q['question_text'],
                'type'          => $q['type'] ?? 'single',
                'points'        => $q['points'] ?? 1,
                'explanation'   => $q['explanation'] ?? null,
                'order'         => ++$order,
            ]);

            foreach ($q['answers'] as $i => $ans) {
                $question->answers()->create([
                    'answer_text' => $ans['text'],
                    'is_correct'  => (bool) ($ans['is_correct'] ?? false),
                    'order'       => $i + 1,
                ]);
            }

            $created++;
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'message' => "{$created} question(s) générée(s) et sauvegardée(s).",
        ]);
    }

    public function chat(Request $request)
    {
        $data = $request->validate([
            'content'  => 'nullable|string|max:8000',
            'messages' => 'required|array|max:20',
            'messages.*.role'    => 'required|in:user,assistant',
            'messages.*.content' => 'required|string|max:2000',
        ]);

        $content = $data['content'] ?? '';
        $systemPrompt = "Tu es un assistant pédagogique intégré dans la plateforme CLARIX. Tu aides l'étudiant à comprendre uniquement le contenu de la leçon ci-dessous. Si une question sort du contexte de la leçon, redirige poliment l'étudiant. Réponds en français, de façon concise et pédagogique.\n\nCONTENU DE LA LEÇON :\n" . ($content ?: '(Leçon vidéo — réponds aux questions générales sur le sujet de la leçon).');

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $data['messages']
        );

        $result = $this->groq($messages, 600);

        return response()->json(['result' => $result]);
    }
}
