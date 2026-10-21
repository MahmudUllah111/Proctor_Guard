<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index(Exam $exam)
    {
        $questions = $exam->questions()->with('options')->get();

        return view('admin.questions.index', compact('exam', 'questions'));
    }

    public function create(Exam $exam)
    {
        return view('admin.questions.create', compact('exam'));
    }

    public function store(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'code_snippet' => 'nullable|string',
            'language' => 'nullable|string|max:50',
            'question_type' => 'required|in:multiple_choice,true_false,code_output',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option_index' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated, $exam, $request) {
            $maxSort = $exam->questions()->max('sort_order') ?? 0;

            $question = $exam->questions()->create([
                'prompt' => $validated['prompt'],
                'code_snippet' => $validated['code_snippet'] ?? null,
                'language' => $validated['language'] ?? 'php',
                'question_type' => $validated['question_type'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
                'sort_order' => $maxSort + 1,
            ]);

            $correctIndex = (int) $validated['correct_option_index'];

            foreach ($validated['options'] as $idx => $optData) {
                $question->options()->create([
                    'option_text' => $optData['text'],
                    'is_correct' => ($idx === $correctIndex),
                    'sort_order' => $idx,
                ]);
            }
        });

        return redirect()->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question added successfully.');
    }

    public function edit(Exam $exam, Question $question)
    {
        $question->load('options');

        return view('admin.questions.edit', compact('exam', 'question'));
    }

    public function update(Request $request, Exam $exam, Question $question)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'code_snippet' => 'nullable|string',
            'language' => 'nullable|string|max:50',
            'question_type' => 'required|in:multiple_choice,true_false,code_output',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option_index' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated, $question) {
            $question->update([
                'prompt' => $validated['prompt'],
                'code_snippet' => $validated['code_snippet'] ?? null,
                'language' => $validated['language'] ?? 'php',
                'question_type' => $validated['question_type'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
            ]);

            $correctIndex = (int) $validated['correct_option_index'];

            // Replace options
            $question->options()->delete();
            foreach ($validated['options'] as $idx => $optData) {
                $question->options()->create([
                    'option_text' => $optData['text'],
                    'is_correct' => ($idx === $correctIndex),
                    'sort_order' => $idx,
                ]);
            }
        });

        return redirect()->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Exam $exam, Question $question)
    {
        $question->delete();

        return redirect()->route('admin.exams.questions.index', $exam)
            ->with('success', 'Question deleted successfully.');
    }
}
