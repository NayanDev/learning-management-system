<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AnswerParticipantController;
use App\Http\Controllers\AssesmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\MateriLogController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultQuestionController;
use App\Http\Controllers\TemplateCertificationController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainingAnalystController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainingNeedController;
use App\Http\Controllers\TrainingParticipantController;
use App\Http\Controllers\TrainingScheduleController;
use App\Http\Controllers\TrainingUnplanController;
use App\Http\Controllers\TrainingUnplanParticipantController;
use App\Http\Controllers\TrainingWorkshopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])->name('login')->middleware('web');

Route::group(['middleware' => ['web', 'auth', 'middlewareByAccess']], function () {
    // Route Dashboard
    Route::resource('dashboard', DashboardController::class);
    Route::get('dashboard-api', [DashboardController::class, 'indexApi'])->name('dashboard.listapi');
    Route::get('dashboard-export-pdf-default', [DashboardController::class, 'exportPdf'])->name('dashboard.export-pdf-default');
    Route::get('dashboard-export-excel-default', [DashboardController::class, 'exportExcel'])->name('dashboard.export-excel-default');
    Route::post('dashboard-import-excel-default', [DashboardController::class, 'importExcel'])->name('dashboard.import-excel-default');

    // Route Users
    Route::resource('user', UserController::class);
    Route::get('user-api', [UserController::class, 'indexApi'])->name('user.listapi');
    Route::get('user-export-pdf-default', [UserController::class, 'exportPdf'])->name('user.export-pdf-default');
    Route::get('user-export-excel-default', [UserController::class, 'exportExcel'])->name('user.export-excel-default');
    Route::post('user-import-excel-default', [UserController::class, 'importExcel'])->name('user.import-excel-default');

    // Route Workshops
    Route::resource('workshop', WorkshopController::class);
    Route::get('workshop-api', [WorkshopController::class, 'indexApi'])->name('workshop.listapi');
    Route::get('workshop-export-pdf-default', [WorkshopController::class, 'exportPdf'])->name('workshop.export-pdf-default');
    Route::get('workshop-export-excel-default', [WorkshopController::class, 'exportExcel'])->name('workshop.export-excel-default');
    Route::post('workshop-import-excel-default', [WorkshopController::class, 'importExcel'])->name('workshop.import-excel-default');

    // Route Training
    Route::resource('training', TrainingController::class);
    Route::get('training-api', [TrainingController::class, 'indexApi'])->name('training.listapi');
    Route::get('training-export-pdf-default', [TrainingController::class, 'exportPdf'])->name('training.export-pdf-default');
    Route::get('training-export-excel-default', [TrainingController::class, 'exportExcel'])->name('training.export-excel-default');
    Route::post('training-import-excel-default', [TrainingController::class, 'importExcel'])->name('training.import-excel-default');

    // Route Training Analyst
    Route::resource('training-analyst', TrainingAnalystController::class);
    Route::get('training-analyst-api', [TrainingAnalystController::class, 'indexApi'])->name('training-analyst.listapi');
    Route::get('training-analyst-export-pdf-default', [TrainingAnalystController::class, 'exportPdf'])->name('training-analyst.export-pdf-default');
    Route::get('training-analyst-export-excel-default', [TrainingAnalystController::class, 'exportExcel'])->name('training-analyst.export-excel-default');
    Route::post('training-analyst-import-excel-default', [TrainingAnalystController::class, 'importExcel'])->name('training-analyst.import-excel-default');

    // Route Training Need
    Route::resource('training-need', TrainingNeedController::class);
    Route::get('training-need-api', [TrainingNeedController::class, 'indexApi'])->name('training-need.listapi');
    Route::get('training-need-export-pdf-default', [TrainingNeedController::class, 'exportPdf'])->name('training-need.export-pdf-default');
    Route::get('training-need-export-excel-default', [TrainingNeedController::class, 'exportExcel'])->name('training-need.export-excel-default');
    Route::post('training-need-import-excel-default', [TrainingNeedController::class, 'importExcel'])->name('training-need.import-excel-default');

    // Route Training Workshop
    Route::resource('training-workshop', TrainingWorkshopController::class);
    Route::get('training-workshop-api', [TrainingWorkshopController::class, 'indexApi'])->name('training-workshop.listapi');
    Route::get('training-workshop-export-pdf-default', [TrainingWorkshopController::class, 'exportPdf'])->name('training-workshop.export-pdf-default');
    Route::get('training-workshop-export-excel-default', [TrainingWorkshopController::class, 'exportExcel'])->name('training-workshop.export-excel-default');
    Route::post('training-workshop-import-excel-default', [TrainingWorkshopController::class, 'importExcel'])->name('training-workshop.import-excel-default');

    // Route Training Participant
    Route::resource('training-participant', TrainingParticipantController::class);
    Route::get('training-participant-api', [TrainingParticipantController::class, 'indexApi'])->name('training-participant.listapi');
    Route::get('training-participant-export-pdf-default', [TrainingParticipantController::class, 'exportPdf'])->name('training-participant.export-pdf-default');
    Route::get('training-participant-export-excel-default', [TrainingParticipantController::class, 'exportExcel'])->name('training-participant.export-excel-default');
    Route::post('training-participant-import-excel-default', [TrainingParticipantController::class, 'importExcel'])->name('training-participant.import-excel-default');

    // Route Training Schedule
    Route::resource('training-schedule', TrainingScheduleController::class);
    Route::get('training-schedule-api', [TrainingScheduleController::class, 'indexApi'])->name('training-schedule.listapi');
    Route::get('training-schedule-export-pdf-default', [TrainingScheduleController::class, 'exportPdf'])->name('training-schedule.export-pdf-default');
    Route::get('training-schedule-export-excel-default', [TrainingScheduleController::class, 'exportExcel'])->name('training-schedule.export-excel-default');
    Route::post('training-schedule-import-excel-default', [TrainingScheduleController::class, 'importExcel'])->name('training-schedule.import-excel-default');

    // Route Training Unplane
    Route::resource('training-unplan', TrainingUnplanController::class);
    Route::get('training-unplan-api', [TrainingUnplanController::class, 'indexApi'])->name('training-unplan.listapi');
    Route::get('training-unplan-export-pdf-default', [TrainingUnplanController::class, 'exportPdf'])->name('training-unplan.export-pdf-default');
    Route::get('training-unplan-export-excel-default', [TrainingUnplanController::class, 'exportExcel'])->name('training-unplan.export-excel-default');
    Route::post('training-unplan-import-excel-default', [TrainingUnplanController::class, 'importExcel'])->name('training-unplan.import-excel-default');

    // Route Training Unplane Participant
    Route::resource('training-unplan-participant', TrainingUnplanParticipantController::class);
    Route::get('training-unplan-participant-api', [TrainingUnplanParticipantController::class, 'indexApi'])->name('training-unplan-participant.listapi');
    Route::get('training-unplan-participant-export-pdf-default', [TrainingUnplanParticipantController::class, 'exportPdf'])->name('training-unplan-participant.export-pdf-default');
    Route::get('training-unplan-participant-export-excel-default', [TrainingUnplanParticipantController::class, 'exportExcel'])->name('training-unplan-participant.export-excel-default');
    Route::post('training-unplan-participant-import-excel-default', [TrainingUnplanParticipantController::class, 'importExcel'])->name('training-unplan-participant.import-excel-default');

    // Route Event
    Route::resource('event', EventController::class);
    Route::get('event-api', [EventController::class, 'indexApi'])->name('event.listapi');
    Route::get('event-export-pdf-default', [EventController::class, 'exportPdf'])->name('event.export-pdf-default');
    Route::get('event-export-excel-default', [EventController::class, 'exportExcel'])->name('event.export-excel-default');
    Route::post('event-import-excel-default', [EventController::class, 'importExcel'])->name('event.import-excel-default');

    // Route Trainer
    Route::resource('trainer', TrainerController::class);
    Route::get('trainer-api', [TrainerController::class, 'indexApi'])->name('trainer.listapi');
    Route::get('trainer-export-pdf-default', [TrainerController::class, 'exportPdf'])->name('trainer.export-pdf-default');
    Route::get('trainer-export-excel-default', [TrainerController::class, 'exportExcel'])->name('trainer.export-excel-default');
    Route::post('trainer-import-excel-default', [TrainerController::class, 'importExcel'])->name('trainer.import-excel-default');

    // Route Participant
    Route::resource('participant', ParticipantController::class);
    Route::get('participant-api', [ParticipantController::class, 'indexApi'])->name('participant.listapi');
    Route::get('participant-export-pdf-default', [ParticipantController::class, 'exportPdf'])->name('participant.export-pdf-default');
    Route::get('participant-export-excel-default', [ParticipantController::class, 'exportExcel'])->name('participant.export-excel-default');
    Route::post('participant-import-excel-default', [ParticipantController::class, 'importExcel'])->name('participant.import-excel-default');

    // Route Attendance
    Route::resource('attendance', AttendanceController::class);
    Route::get('attendance-api', [AttendanceController::class, 'indexApi'])->name('attendance.listapi');
    Route::get('attendance-export-pdf-default', [AttendanceController::class, 'exportPdf'])->name('attendance.export-pdf-default');
    Route::get('attendance-export-excel-default', [AttendanceController::class, 'exportExcel'])->name('attendance.export-excel-default');
    Route::post('attendance-import-excel-default', [AttendanceController::class, 'importExcel'])->name('attendance.import-excel-default');

    // Route Materi
    Route::resource('materi', MateriController::class);
    Route::get('materi-api', [MateriController::class, 'indexApi'])->name('materi.listapi');
    Route::get('materi-export-pdf-default', [MateriController::class, 'exportPdf'])->name('materi.export-pdf-default');
    Route::get('materi-export-excel-default', [MateriController::class, 'exportExcel'])->name('materi.export-excel-default');
    Route::post('materi-import-excel-default', [MateriController::class, 'importExcel'])->name('materi.import-excel-default');

    // Route Materi Log
    Route::resource('materi-log', MateriLogController::class);
    Route::get('materi-log-api', [MateriLogController::class, 'indexApi'])->name('materi-log.listapi');
    Route::get('materi-log-export-pdf-default', [MateriLogController::class, 'exportPdf'])->name('materi-log.export-pdf-default');
    Route::get('materi-log-export-excel-default', [MateriLogController::class, 'exportExcel'])->name('materi-log.export-excel-default');
    Route::post('materi-log-import-excel-default', [MateriLogController::class, 'importExcel'])->name('materi-log.import-excel-default');

    // Route Question
    Route::resource('question', QuestionController::class);
    Route::get('question-api', [QuestionController::class, 'indexApi'])->name('question.listapi');
    Route::get('question-export-pdf-default', [QuestionController::class, 'exportPdf'])->name('question.export-pdf-default');
    Route::get('question-export-excel-default', [QuestionController::class, 'exportExcel'])->name('question.export-excel-default');
    Route::post('question-import-excel-default', [QuestionController::class, 'importExcel'])->name('question.import-excel-default');

    // Route Answer
    Route::resource('answer', AnswerController::class);
    Route::get('answer-api', [AnswerController::class, 'indexApi'])->name('answer.listapi');
    Route::get('answer-export-pdf-default', [AnswerController::class, 'exportPdf'])->name('answer.export-pdf-default');
    Route::get('answer-export-excel-default', [AnswerController::class, 'exportExcel'])->name('answer.export-excel-default');
    Route::post('answer-import-excel-default', [AnswerController::class, 'importExcel'])->name('answer.import-excel-default');

    // Route Result Question
    Route::resource('result-question', ResultQuestionController::class);
    Route::get('result-question-api', [ResultQuestionController::class, 'indexApi'])->name('result-question.listapi');
    Route::get('result-question-export-pdf-default', [ResultQuestionController::class, 'exportPdf'])->name('result-question.export-pdf-default');
    Route::get('result-question-export-excel-default', [ResultQuestionController::class, 'exportExcel'])->name('result-question.export-excel-default');
    Route::post('result-question-import-excel-default', [ResultQuestionController::class, 'importExcel'])->name('result-question.import-excel-default');

    // Route Answer Participant
    Route::resource('answer-participant', AnswerParticipantController::class);
    Route::get('answer-participant-api', [AnswerParticipantController::class, 'indexApi'])->name('answer-participant.listapi');
    Route::get('answer-participant-export-pdf-default', [AnswerParticipantController::class, 'exportPdf'])->name('answer-participant.export-pdf-default');
    Route::get('answer-participant-export-excel-default', [AnswerParticipantController::class, 'exportExcel'])->name('answer-participant.export-excel-default');
    Route::post('answer-participant-import-excel-default', [AnswerParticipantController::class, 'importExcel'])->name('answer-participant.import-excel-default');

    // Route Template Certification
    Route::resource('template-certification', TemplateCertificationController::class);
    Route::get('template-certification-api', [TemplateCertificationController::class, 'indexApi'])->name('template-certification.listapi');
    Route::get('template-certification-export-pdf-default', [TemplateCertificationController::class, 'exportPdf'])->name('template-certification.export-pdf-default');
    Route::get('template-certification-export-excel-default', [TemplateCertificationController::class, 'exportExcel'])->name('template-certification.export-excel-default');
    Route::post('template-certification-import-excel-default', [TemplateCertificationController::class, 'importExcel'])->name('template-certification.import-excel-default');

    // Route Certification
    Route::resource('certification', CertificationController::class);
    Route::get('certification-api', [CertificationController::class, 'indexApi'])->name('certification.listapi');
    Route::get('certification-export-pdf-default', [CertificationController::class, 'exportPdf'])->name('certification.export-pdf-default');
    Route::get('certification-export-excel-default', [CertificationController::class, 'exportExcel'])->name('certification.export-excel-default');
    Route::post('certification-import-excel-default', [CertificationController::class, 'importExcel'])->name('certification.import-excel-default');

    // Route Evaluation
    Route::resource('evaluation', EvaluationController::class);
    Route::get('evaluation-api', [EvaluationController::class, 'indexApi'])->name('evaluation.listapi');
    Route::get('evaluation-export-pdf-default', [EvaluationController::class, 'exportPdf'])->name('evaluation.export-pdf-default');
    Route::get('evaluation-export-excel-default', [EvaluationController::class, 'exportExcel'])->name('evaluation.export-excel-default');
    Route::post('evaluation-import-excel-default', [EvaluationController::class, 'importExcel'])->name('evaluation.import-excel-default');

    // Route Documentation
    Route::resource('documentation', DocumentationController::class);
    Route::get('documentation-api', [DocumentationController::class, 'indexApi'])->name('documentation.listapi');
    Route::get('documentation-export-pdf-default', [DocumentationController::class, 'exportPdf'])->name('documentation.export-pdf-default');
    Route::get('documentation-export-excel-default', [DocumentationController::class, 'exportExcel'])->name('documentation.export-excel-default');
    Route::post('documentation-import-excel-default', [DocumentationController::class, 'importExcel'])->name('documentation.import-excel-default');
});


// Custome Route
Route::group(['middleware' => ['web', 'auth']], function () {
    // Route Dashboard
    Route::get('/qrcode-scanner', [DashboardController::class, 'qrcodeScanner']);

    // Route User
    Route::get('my-account', [UserController::class, 'profile']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
    Route::get('signature-bulk-update', [UserController::class, 'signatureBulkUpdate'])->name('user.signature.bulk.update');
    Route::post('users/bulk-update-signatures', [UserController::class, 'bulkUpdateSignatures'])
        ->name('users.bulk.update.signatures');

    // Route Training
    Route::post('training/{id}', [TrainingController::class, 'approve'])->name('training.approve');

    // Route Training Analyst
    Route::get('training-analyst-form', [TrainingAnalystController::class, 'trainingForm'])->name('training-analyst.form');
    Route::post('training-analyst/save-all', [TrainingAnalystController::class, 'saveAll'])->name('training-analyst.saveAll');
    Route::get('training-analyst-pdf', [TrainingAnalystController::class, 'generatePDF'])->name('training-analyst.pdf');
    Route::post('training-analyst/{id}', [TrainingAnalystController::class, 'approve'])->name('training.analyst.approve');

    // Route Training Participant
    Route::get('participant-ajax', [TrainingParticipantController::class, 'participantAjax']);

    // Route Training Need
    Route::post('training-need/{id}', [TrainingNeedController::class, 'approve'])->name('training.need.approve');
    Route::get('training-need-pdf', [TrainingNeedController::class, 'generatePDF'])->name('training-need.pdf');

    // Route Training Schedule
    Route::get('training-schedule-pdf', [TrainingScheduleController::class, 'generatePDF'])->name('training-schedule.pdf');

    // Route Training Unplan
    Route::post('training-unplan/{id}', [TrainingUnplanController::class, 'approve'])->name('training.unplan.approve');

    // Route Participant
    Route::post('participant-generate-user/{event_id}', [ParticipantController::class, 'generateUser'])->name('participant.generate.user');

    // Route Attendance
    Route::get('attendance-participant', [AttendanceController::class, 'attendance'])->name('participant.attendance');
    Route::post('participant-attendance/{token}', [AttendanceController::class, 'attendanceForm'])->name('participant.attendance.form');
    Route::post('participant-attendance-ready/', [AttendanceController::class, 'attendanceFormReady'])->name('participant.attendance.form.ready');
    Route::get('attendance-ready-pdf', [AttendanceController::class, 'readyPdf'])->name('attendance.ready.pdf');
    Route::get('attendance-present-pdf', [AttendanceController::class, 'presentPdf'])->name('attendance.present.pdf');

    // Route Barcode
    Route::get('/set-account/{id}', function ($id) {
        session(['account_id' => $id]);
        return redirect('/participant-account');
    })->name('set.account');
    Route::get('/participant-account', [ParticipantController::class, 'participantAccount'])->name('participant.account');

    Route::get('/set-attendance/{id}', function ($id) {
        session(['attendance_id' => $id]);
        return redirect('/attendance-barcode');
    })->name('set.attendance');
    Route::get('/attendance-barcode', [AttendanceController::class, 'attendanceBarcode'])->name('attendance.barcode');

    Route::get('/set-question/{id}', function ($id) {
        session(['question_id' => $id]);
        return redirect('/question-barcode');
    })->name('set.question');
    Route::get('/question-barcode', [QuestionController::class, 'questionBarcode'])->name('question.barcode');

    // Route Assesment
    Route::get('/api/questions', [QuestionController::class, 'getQuestionsForTest'])->name('api.questions');
    Route::post('/api/submit-test', [QuestionController::class, 'submitTest'])->name('api.submit-test');
    Route::get('/assesment', [AssesmentController::class, 'assesment'])->name('assesment');
    Route::get('/assesment-pdf', [AssesmentController::class, 'generatePDF'])->name('assesment.pdf');

    // Route Certification
    Route::get('certification-pdf', [CertificationController::class, 'generatePDF'])->name('certification.pdf');

    // Route Evaluation
    Route::get('/evaluation-pdf', [EvaluationController::class, 'generatePDF'])->name('evaluation.pdf');
    Route::get('/evaluation-form', [EvaluationController::class, 'evaluationForm'])->name('evaluation.form');
    Route::get('/evaluation-bulk', [EvaluationController::class, 'evaluationBulk'])->name('evaluation.bulk');
    Route::post('/submit-evaluation', [EvaluationController::class, 'submitEvaluation'])->name('submit.evaluation');
    Route::post('/submit-evaluation-bulk', [EvaluationController::class, 'submitEvaluationBulk'])->name('submit.evaluation.bulk');

    // Route Documentation
    Route::get('documentation-pdf', [DocumentationController::class, 'generatePDF'])->name('documentation.pdf');
});
