<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerParticipant;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Question;
use App\Models\ResultQuestion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class AssesmentController extends Controller
{
    public function assesment()
    {
        $token = request('token');
        $user = Auth::user();
        if (!$token) {
            abort(404);
        }
        $data = Event::where('token', $token)->first();
        $participantId = Participant::where('nik', Auth::user()->nik)->where('event_id', $data->id)->first();
        $resultQuestion = ResultQuestion::where('participant_id', $participantId?->id)->first();

        if (!$user) {
            abort(403, 'Unauthorized. Please login first.');
        }

        return view('backend.idev.assesment', compact('data', 'user', 'resultQuestion'));
    }

    public function generatePDF()
    {
        $participantId = request('participant_id');
        $type = request('type');
        if (!$participantId) {
            abort(404, 'Result Question Not Found.');
        }

        $participant = Participant::where('id', $participantId)->first();
        $resultQuestion = ResultQuestion::where('participant_id', $participantId)->first();
        $person = Participant::where('id', $participantId)->first();
        $question = Question::with(['answers', 'answerParticipants' => function ($query) use ($participantId, $type) {
            $query->where('participant_id', $participantId)
                ->where('type', $type);
        }])->where('event_id', $participant->event_id)->get();
        $answerParticipant = AnswerParticipant::where('participant_id', $participantId)
            ->where('type', $type)->get();

        $data = [
            'score' => $resultQuestion,
            'person' => $person,
            'questions' => $question,
            'answerParticipants' => $answerParticipant,
        ];

        $pdf = PDF::loadView('pdf.assesment', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->stream("Assesment" . ($request->year ?? date('Y')) . ".pdf");
    }
}
